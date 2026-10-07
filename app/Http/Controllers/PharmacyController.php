<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    /**
     * Display the pharmacy dashboard (dispensing queue).
     */
    public function dashboard()
    {
        // Load active prescriptions (pending + partially dispatched), oldest first
        $prescriptions = Prescription::active()
            ->with(['patient', 'doctor', 'items.medicine.batches'])
            ->orderBy('created_at', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Per-item stock pre-flight summary so the dispense modal can warn
        // before submitting. Built from the already-eager-loaded batches,
        // so this costs no extra queries.
        $stockSummary = [];
        foreach ($prescriptions as $prescription) {
            foreach ($prescription->items as $item) {
                $available = 0;
                $earliestExpiry = null;
                $medicine = $item->medicine ?: $this->resolveInventoryMedicine($item);

                foreach ($medicine?->batches ?? [] as $batch) {
                    if (($batch->quantity ?? 0) <= 0) {
                        continue;
                    }
                    if ($batch->expiration_date && $batch->expiration_date->lt(today())) {
                        continue;
                    }

                    $available += (int) $batch->quantity;

                    if ($batch->expiration_date && (! $earliestExpiry || $batch->expiration_date->lt($earliestExpiry))) {
                        $earliestExpiry = $batch->expiration_date;
                    }
                }

                $stockSummary[$item->id] = [
                    'available' => $available,
                    'earliest_expiry' => $earliestExpiry,
                    'tracked' => (bool) $medicine,
                ];
            }
        }

        // ── Inventory Stats for Banners ──
        $expiredBatchesCount = MedicineBatch::where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'active')
                      ->where('quantity', '>', 0)
                      ->whereDate('expiration_date', '<=', today());
              });
        })->count();

        $expiringSoonCount = MedicineBatch::where('quantity', '>', 0)
            ->whereDate('expiration_date', '>', today())
            ->whereDate('expiration_date', '<=', today()->addDays(30))->count();

        // Low stock = medicines with total non-expired stock between 1 and 20
        $lowStockMedicines = Medicine::withSum(['batches as active_stock' => function ($q) {
            $q->where('quantity', '>', 0)->whereDate('expiration_date', '>=', today());
        }], 'quantity')
            ->having('active_stock', '>', 0)
            ->having('active_stock', '<', 20)
            ->get();
        $lowStockCount = $lowStockMedicines->count();

        // Out of stock = medicines where ALL batches have 0 qty or are expired
        $outOfStockCount = Medicine::whereDoesntHave('batches', function ($q) {
            $q->where('quantity', '>', 0)->whereDate('expiration_date', '>=', today());
        })->count();

        $totalMedicines = Medicine::count();

        // ── Analytics: Top 10 Most Dispensed Medicines (last 30 days) ──
        $topDispensed = InventoryLog::where('action', 'Dispensed')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('medicine_id', DB::raw('SUM(ABS(quantity_changed)) as total_dispensed'))
            ->groupBy('medicine_id')
            ->orderByDesc('total_dispensed')
            ->limit(10)
            ->with('medicine:id,name,generic_name')
            ->get();

        // ── Analytics: Monthly Dispensing Trend (last 6 months) ──
        $monthlyTrend = InventoryLog::where('action', 'Dispensed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(ABS(quantity_changed)) as total_dispensed')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $item->label = \Carbon\Carbon::create($item->year, $item->month)->format('M Y');
                return $item;
            });

        return view('pharmacy.dashboard', compact(
            'prescriptions',
            'stockSummary',
            'expiredBatchesCount',
            'expiringSoonCount',
            'lowStockCount',
            'outOfStockCount',
            'totalMedicines',
            'topDispensed',
            'monthlyTrend'
        ));
    }

    /**
     * Display the prescription history log.
     */
    public function history(Request $request)
    {
        $query = Prescription::closed()->with(['patient', 'doctor', 'items']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('patient_id', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        $prescriptions = $query->orderBy('updated_at', 'desc')->paginate($request->get('per_page', 10));

        return view('pharmacy.history', compact('prescriptions'));
    }

    /**
     * Dispense a prescription, deducting stock from inventory.
     * Supports partial and cumulative dispensing.
     */
    public function dispense(Request $request, Prescription $prescription)
    {
        if (! in_array($prescription->status, ['pending', 'partially_dispensed'])) {
            return $this->respondError($request, 'This prescription cannot be dispensed in its current status.');
        }

        if ($prescription->isExpired()) {
            return $this->respondError(
                $request,
                "Order #{$prescription->id} expired on {$prescription->expires_at->format('j M Y')} and can no longer be dispensed. Ask the prescriber to re-issue it."
            );
        }

        $submitted = $request->input('items');

        if (! is_array($submitted) || $submitted === []) {
            return $this->respondError($request, 'No dispensing quantities were submitted. Enter a quantity for at least one item.');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:0',
            'pharmacist_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->respondError($request, $validator->errors()->first(), 422, $validator->errors()->toArray());
        }

        $validated = $validator->validated();

        // ── Sequence: items belong to this prescription → quantity ≤ outstanding ──
        $prescription->load('items.medicine.batches');

        $lines = [];
        $hasNonZero = false;
        $problems = [];
        $offendingItemIds = [];

        foreach ($validated['items'] as $dispenseItem) {
            $item = $prescription->items->firstWhere('id', (int) $dispenseItem['item_id']);

            if (! $item) {
                return $this->respondError($request, 'One of the submitted lines does not belong to this prescription. Reload the queue and try again.');
            }

            $requested = (int) $dispenseItem['quantity'];
            $outstanding = $item->outstanding_quantity;

            if ($requested > $outstanding) {
                $problems[] = "{$item->medicine_name} — only {$outstanding} still outstanding on this order ({$requested} requested).";
                $offendingItemIds[] = $item->id;
            }

            if ($requested > 0) {
                $hasNonZero = true;
            }

            $lines[] = ['item' => $item, 'requested' => $requested];
        }

        if (! $hasNonZero) {
            return $this->respondError($request, 'Enter a quantity greater than zero for at least one item, or cancel this prescription instead.');
        }

        if ($problems !== []) {
            return $this->respondError(
                $request,
                implode(' ', $problems).' Reduce the quantity or leave the line at 0.',
                422,
                [],
                $offendingItemIds
            );
        }

        // ── Stock pre-flight: collect every shortfall before opening the transaction ──
        $stockProblems = [];
        $stockOffenders = [];

        foreach ($lines as $line) {
            if ($line['requested'] <= 0) {
                continue;
            }

            $item = $line['item'];
            $medicine = $this->resolveInventoryMedicine($item);

            if (! $medicine) {
                $stockProblems[] = "{$item->medicine_name} — not found in RHU inventory. Ask the prescriber to revise or cancel this order.";
                $stockOffenders[] = $item->id;
                continue;
            }

            $available = (int) $medicine->batches()
                ->whereDate('expiration_date', '>=', today())
                ->where('quantity', '>', 0)
                ->sum('quantity');

            if ($available < $line['requested']) {
                $stockProblems[] = "{$item->medicine_name} — only {$available} in stock ({$line['requested']} requested). Reduce the quantity or leave the line at 0.";
                $stockOffenders[] = $item->id;
            }
        }

        if ($stockProblems !== []) {
            return $this->respondError(
                $request,
                implode(' ', $stockProblems),
                422,
                [],
                array_values(array_unique($stockOffenders))
            );
        }

        $newStatus = 'dispensed';

        try {
            DB::transaction(function () use ($prescription, $validated, $lines, &$newStatus) {
                $prescription->load('items.medicine.batches');

                foreach ($lines as $line) {
                    $dispenseQty = $line['requested'];
                    if ($dispenseQty <= 0) {
                        continue;
                    }

                    $item = $prescription->items()->lockForUpdate()->find($line['item']->id);

                    if (! $item) {
                        throw new \Exception("Prescription item #{$line['item']->id} disappeared during dispensing.");
                    }

                    $medicine = $this->resolveInventoryMedicine($item, lock: true);

                    if (! $medicine) {
                        throw new \Exception("{$item->medicine_name} is no longer in RHU inventory.");
                    }

                    // Re-check stock under lock; a batch may have been drained concurrently.
                    $available = (int) $medicine->batches()
                        ->whereDate('expiration_date', '>=', today())
                        ->where('quantity', '>', 0)
                        ->sum('quantity');

                    if ($available < $dispenseQty) {
                        throw new \Exception("{$item->medicine_name} stock changed while dispensing — {$available} now available, {$dispenseQty} requested.");
                    }

                    // FEFO deduction with row locking
                    $batches = $medicine->batches()
                        ->whereDate('expiration_date', '>=', today())
                        ->where('quantity', '>', 0)
                        ->orderBy('expiration_date', 'asc')
                        ->lockForUpdate()
                        ->get();

                    $remainingToDeduct = $dispenseQty;

                    foreach ($batches as $batch) {
                        if ($remainingToDeduct <= 0) {
                            break;
                        }

                        $deductAmount = min($batch->quantity, $remainingToDeduct);
                        $batch->decrement('quantity', $deductAmount);

                        if ($batch->fresh()->quantity <= 0) {
                            $batch->update(['status' => 'depleted']);
                        }

                        $patient = $prescription->patient;
                        $patientName = $patient ? $patient->full_name : 'Patient';
                        $patientId = $patient ? $patient->patient_id : 'N/A';

                        InventoryLog::create([
                            'medicine_id' => $medicine->id,
                            'batch_id' => $batch->id,
                            'action' => 'Dispensed',
                            'quantity_changed' => -$deductAmount,
                            'remarks' => "Dispensed for Rx #{$prescription->id} | Patient: {$patientName} ({$patientId}) | Batch: {$batch->batch_number} | Deducted: {$deductAmount} | Cumulative: " . ($item->dispensed_quantity + $deductAmount) . " / {$item->quantity}",
                            'performed_by' => Auth::id(),
                        ]);

                        $remainingToDeduct -= $deductAmount;
                    }

                    if ($remainingToDeduct > 0) {
                        throw new \Exception("Insufficient unexpired batch stock during FEFO deduction for {$item->medicine_name}. Short by {$remainingToDeduct} unit(s).");
                    }

                    // Update cumulative dispensed quantity
                    $item->increment('dispensed_quantity', $dispenseQty);
                }

                // Determine new prescription status (reload so the increments above are reflected)
                $prescription->load('items');
                $allDispensed = true;
                foreach ($prescription->items as $item) {
                    if ($item->outstanding_quantity > 0) {
                        $allDispensed = false;
                        break;
                    }
                }

                $newStatus = $allDispensed ? 'dispensed' : 'partially_dispensed';

                $prescription->update([
                    'status' => $newStatus,
                    'dispensed_by' => Auth::id(),
                    'dispensed_at' => now(),
                    'pharmacist_notes' => $validated['pharmacist_notes'] ?? null,
                ]);

                \App\Models\AuditLog::record(
                    "Prescription Dispensed: #{$prescription->id} ({$newStatus})",
                    $prescription,
                    [
                        'dispensed_by' => Auth::id(),
                        'patient_id' => $prescription->patient?->patient_id,
                        'patient_name' => $prescription->patient?->full_name,
                        'status' => $newStatus,
                        'pharmacist_notes' => $validated['pharmacist_notes'] ?? null,
                    ]
                );
            });
        } catch (\Throwable $e) {
            report($e);

            return $this->respondError(
                $request,
                'Could not dispense. Nothing was saved — please try again.',
                500
            );
        }

        broadcast(new \App\Events\QueueUpdated('Prescription dispensed', 'pharmacy'));

        $message = $newStatus === 'dispensed'
            ? 'Prescription dispensed successfully. Inventory updated.'
            : 'Partially dispensed. The remaining quantities stay in the queue.';

        return $this->respondSuccess($request, $message, ['status' => $newStatus]);
    }

    /**
     * Whether the caller expects a JSON response (AJAX endpoints) rather than a redirect.
     */
    private function isAjax(Request $request): bool
    {
        return $request->expectsJson()
            || $request->ajax()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Return a usable error for both AJAX and no-JS callers.
     */
    private function respondError(Request $request, string $message, int $status = 422, array $errors = [], array $itemIds = [])
    {
        if ($this->isAjax($request)) {
            return response()->json([
                'success' => false,
                'ok' => false,
                'message' => $message,
                'errors' => $errors,
                'item_ids' => $itemIds,
            ], $status);
        }

        return back()->with('error', $message);
    }

    /**
     * Return a usable success response for both AJAX and no-JS callers.
     */
    private function respondSuccess(Request $request, string $message, array $extra = [])
    {
        if ($this->isAjax($request)) {
            return response()->json(array_merge([
                'success' => true,
                'ok' => true,
                'message' => $message,
            ], $extra));
        }

        return back()->with('success', $message);
    }

    /**
     * Resolve the inventory medicine for a prescription item, by id when
     * present, otherwise by name match. Optionally lock the row.
     */
    private function resolveInventoryMedicine($item, bool $lock = false): ?Medicine
    {
        if ($item->medicine_id) {
            $query = Medicine::query();

            if ($lock) {
                $query->lockForUpdate();
            }

            $medicine = $query->find($item->medicine_id);

            if ($medicine) {
                return $medicine;
            }
        }

        $cleanName = trim(preg_replace('/\s*\([^)]*\)$/', '', (string) $item->medicine_name));

        $query = Medicine::where('name', $item->medicine_name)
            ->orWhere('generic_name', $item->medicine_name)
            ->orWhere('name', $cleanName)
            ->orWhere('generic_name', $cleanName);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /**
     * Normalise a lot/batch number typed off the packaging:
     * trim, collapse internal whitespace, uppercase, drop trailing punctuation.
     */
    private function normalizeBatchNumber($value): string
    {
        $value = preg_replace('/\s+/', ' ', (string) $value);
        $value = strtoupper(trim($value));

        return rtrim($value, " \t\n\r\0\x0B.,;:/-");
    }

    /**
     * Display the medicine list/inventory.
     */
    public function medicines(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $formFilter = $request->input('form_filter');
        $statusFilter = $request->input('status_filter');

        $query = Medicine::with(['batches' => function ($q) {
            $q->orderBy('expiration_date', 'asc');
        }, 'batches.disposer']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%");
            });
        }

        if ($formFilter && $formFilter !== 'all') {
            $query->where('form', $formFilter);
        }

        if ($statusFilter) {
            switch ($statusFilter) {
                case 'expired':
                    $query->whereHas('batches', function ($q) {
                        $q->where('quantity', '>', 0)
                          ->where('status', '!=', 'disposed')
                          ->whereDate('expiration_date', '<=', today());
                    });
                    break;
                case 'expiring_soon':
                    $query->whereHas('batches', function ($q) {
                        $q->where('quantity', '>', 0)
                          ->where('status', '!=', 'disposed')
                          ->whereDate('expiration_date', '>', today())
                          ->whereDate('expiration_date', '<=', today()->addDays(30));
                    });
                    break;
                case 'low_stock':
                    $query->withSum(['batches as active_stock' => function ($q) {
                        $q->where('quantity', '>', 0)
                          ->where('status', '!=', 'disposed')
                          ->whereDate('expiration_date', '>=', today());
                    }], 'quantity')
                    ->having('active_stock', '>', 0)
                    ->having('active_stock', '<', 20);
                    break;
                case 'out_of_stock':
                    $query->whereDoesntHave('batches', function ($q) {
                        $q->where('quantity', '>', 0)
                          ->where('status', '!=', 'disposed')
                          ->whereDate('expiration_date', '>=', today());
                    });
                    break;
                case 'archived':
                    $query->where('is_active', false);
                    break;
            }
        }

        $query->addSelect([
            'earliest_expiry' => \App\Models\MedicineBatch::select('expiration_date')
                ->whereColumn('medicine_id', 'medicines.id')
                ->where('quantity', '>', 0)
                ->where('status', '!=', 'disposed')
                ->orderBy('expiration_date', 'asc')
                ->limit(1)
        ]);

        $medicines = $query->orderByRaw('earliest_expiry IS NULL ASC, earliest_expiry ASC')
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        $forms = Medicine::select('form')->whereNotNull('form')->distinct()->pluck('form');

        // Get counts for the banner (always show original counts)
        $expiredBatchesCount = \App\Models\MedicineBatch::where('quantity', '>', 0)
            ->where('status', '!=', 'disposed')
            ->whereDate('expiration_date', '<=', today())->count();

        $expiringSoonCount = \App\Models\MedicineBatch::where('quantity', '>', 0)
            ->where('status', '!=', 'disposed')
            ->whereDate('expiration_date', '>', today())
            ->whereDate('expiration_date', '<=', today()->addDays(30))->count();

        $expiring60Count = \App\Models\MedicineBatch::where('quantity', '>', 0)
            ->where('status', '!=', 'disposed')
            ->whereDate('expiration_date', '>', today()->addDays(30))
            ->whereDate('expiration_date', '<=', today()->addDays(60))->count();

        $disposedBatchesCount = \App\Models\MedicineBatch::where('status', 'disposed')->count();
        $archivedMedicinesCount = Medicine::where('is_active', false)->count();

        return view('pharmacy.medicines', compact(
            'medicines', 
            'forms', 
            'expiredBatchesCount', 
            'expiringSoonCount', 
            'expiring60Count', 
            'disposedBatchesCount',
            'archivedMedicinesCount'
        ));
    }

    /**
     * Export the current pharmacy inventory & formulary stock to CSV.
     */
    public function exportStockCsv(Request $request)
    {
        $search = $request->input('search');
        $formFilter = $request->input('form_filter');
        $statusFilter = $request->input('status_filter');

        $query = Medicine::with(['batches' => function ($q) {
            $q->orderBy('expiration_date', 'asc');
        }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%");
            });
        }

        if ($formFilter && $formFilter !== 'all') {
            $query->where('form', $formFilter);
        }

        if ($statusFilter) {
            switch ($statusFilter) {
                case 'expired':
                    $query->whereHas('batches', function ($q) {
                        $q->where('quantity', '>', 0)
                          ->where('status', '!=', 'disposed')
                          ->whereDate('expiration_date', '<=', today());
                    });
                    break;
                case 'expiring_soon':
                    $query->whereHas('batches', function ($q) {
                        $q->where('quantity', '>', 0)
                          ->where('status', '!=', 'disposed')
                          ->whereDate('expiration_date', '>', today())
                          ->whereDate('expiration_date', '<=', today()->addDays(30));
                    });
                    break;
                case 'low_stock':
                    $query->withSum(['batches as active_stock' => function ($q) {
                        $q->where('quantity', '>', 0)
                          ->where('status', '!=', 'disposed')
                          ->whereDate('expiration_date', '>=', today());
                    }], 'quantity')
                    ->having('active_stock', '>', 0)
                    ->having('active_stock', '<', 20);
                    break;
                case 'out_of_stock':
                    $query->whereDoesntHave('batches', function ($q) {
                        $q->where('quantity', '>', 0)
                          ->where('status', '!=', 'disposed')
                          ->whereDate('expiration_date', '>=', today());
                    });
                    break;
                case 'archived':
                    $query->where('is_active', false);
                    break;
            }
        }

        $count = $query->count();
        if ($count === 0) {
            return response()->json(['message' => 'No inventory stock records found matching the filter.'], 404);
        }

        $filterLabel = $statusFilter ? "-{$statusFilter}" : '';
        $filename = "pharmacy-stock-report{$filterLabel}-" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store, no-cache',
        ];

        $csvHeaders = [
            'Medicine ID',
            'Brand Name',
            'Generic Name',
            'Dosage Form',
            'Category',
            'Unit',
            'Medicine Status',
            'Total Active Stock',
            'Stock Alert Level',
            'Batch Number',
            'Batch Current Quantity',
            'Batch Original Quantity',
            'Expiration Date',
            'Expiry Status',
            'Batch Status',
            'Disposal Reason',
        ];

        return response()->stream(function () use ($query, $csvHeaders) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, $csvHeaders);

            $query->orderBy('name', 'asc')->chunk(100, function ($medicines) use ($handle) {
                foreach ($medicines as $medicine) {
                    $totalStock = $medicine->total_stock;
                    $hasExpired = $medicine->batches->contains(function ($batch) {
                        return $batch->quantity > 0 && $batch->status !== 'disposed' && $batch->expiration_date < now();
                    });
                    $hasExpiringSoon = $medicine->batches->contains(function ($batch) {
                        return $batch->quantity > 0 && $batch->status !== 'disposed' && $batch->expiration_date >= now() && $batch->expiration_date <= now()->addDays(30);
                    });

                    if (! $medicine->is_active) {
                        $stockAlert = 'Archived';
                    } elseif ($hasExpired) {
                        $stockAlert = 'Expired Batch';
                    } elseif ($totalStock == 0) {
                        $stockAlert = 'Out of Stock';
                    } elseif ($hasExpiringSoon) {
                        $stockAlert = 'Expiring Soon';
                    } elseif ($totalStock < 20) {
                        $stockAlert = 'Low Stock';
                    } else {
                        $stockAlert = 'Optimal';
                    }

                    $batches = $medicine->batches;
                    if ($batches->isEmpty()) {
                        fputcsv($handle, [
                            $medicine->id,
                            $medicine->name,
                            $medicine->generic_name ?? '—',
                            $medicine->form ?? '—',
                            $medicine->category ?? '—',
                            $medicine->unit ?? '—',
                            $medicine->is_active ? 'Active' : 'Archived',
                            $totalStock,
                            $stockAlert,
                            'No Batches',
                            0,
                            0,
                            '—',
                            'No Stock',
                            'N/A',
                            '—',
                        ]);
                    } else {
                        foreach ($batches as $batch) {
                            $expiryDate = $batch->expiration_date ? $batch->expiration_date->format('Y-m-d') : '—';
                            $expiryStatus = 'Valid';
                            if ($batch->status === 'disposed') {
                                $expiryStatus = 'Disposed';
                            } elseif ($batch->expiration_date && $batch->expiration_date < today()) {
                                $expiryStatus = 'Expired';
                            } elseif ($batch->expiration_date && $batch->expiration_date <= today()->addDays(30)) {
                                $expiryStatus = 'Expiring Soon (<= 30d)';
                            }

                            fputcsv($handle, [
                                $medicine->id,
                                $medicine->name,
                                $medicine->generic_name ?? '—',
                                $medicine->form ?? '—',
                                $medicine->category ?? '—',
                                $medicine->unit ?? '—',
                                $medicine->is_active ? 'Active' : 'Archived',
                                $totalStock,
                                $stockAlert,
                                $batch->batch_number,
                                $batch->quantity,
                                $batch->original_quantity ?? $batch->quantity,
                                $expiryDate,
                                $expiryStatus,
                                ucfirst($batch->status ?? 'active'),
                                $batch->disposal_reason ?? '—',
                            ]);
                        }
                    }
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Store a new medicine in the inventory.
     */
    public function storeMedicine(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'form' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'batch_number' => 'required|string|max:40',
            'expiration_date' => 'required|date|after:today',
            'quantity' => 'required|integer|min:1',
        ]);

        $validated['batch_number'] = $this->normalizeBatchNumber($validated['batch_number'] ?? '');

        $existing = Medicine::where('name', trim($validated['name']))
            ->when(!empty($validated['form']), function ($q) use ($validated) {
                $q->where('form', $validated['form']);
            })
            ->first();

        if ($existing) {
            return back()->withErrors([
                'name' => "A medicine with the name '{$existing->name}' already exists in the catalog. Please use '+ Stock' on that item to add a new batch.",
            ])->withInput();
        }

        DB::transaction(function () use ($validated) {
            $medicine = Medicine::create([
                'name' => $validated['name'],
                'generic_name' => $validated['generic_name'],
                'form' => $validated['form'],
                'category' => $validated['category'],
                'unit' => $validated['unit'],
            ]);

            $batch = MedicineBatch::create([
                'medicine_id' => $medicine->id,
                'batch_number' => $validated['batch_number'],
                'expiration_date' => $validated['expiration_date'],
                'quantity' => $validated['quantity'],
                'original_quantity' => $validated['quantity'],
            ]);

            InventoryLog::create([
                'medicine_id' => $medicine->id,
                'batch_id' => $batch->id,
                'action' => 'Added',
                'quantity_changed' => $validated['quantity'],
                'remarks' => 'Initial stock intake',
                'performed_by' => Auth::id(),
            ]);
        });

        return back()->with('success', 'New medicine and initial stock added successfully.');
    }

    /**
     * Update an existing medicine.
     */
    public function updateMedicine(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'form' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
        ]);

        $medicine->update($validated);

        return back()->with('success', 'Medicine updated successfully.');
    }

    /**
     * Add stock (new batch) to a medicine.
     */
    public function addStock(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'batch_number' => 'required|string|max:40',
            'expiration_date' => 'required|date|after:today',
            'quantity' => 'required|integer|min:1',
        ]);

        $validated['batch_number'] = $this->normalizeBatchNumber($validated['batch_number'] ?? '');

        DB::transaction(function () use ($validated, $medicine) {
            $batch = MedicineBatch::create([
                'medicine_id' => $medicine->id,
                'batch_number' => $validated['batch_number'],
                'expiration_date' => $validated['expiration_date'],
                'quantity' => $validated['quantity'],
                'original_quantity' => $validated['quantity'],
            ]);

            InventoryLog::create([
                'medicine_id' => $medicine->id,
                'batch_id' => $batch->id,
                'action' => 'Added',
                'quantity_changed' => $validated['quantity'],
                'remarks' => 'Stock delivery',
                'performed_by' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Stock added successfully to '.$medicine->name);
    }

    /**
     * Toggle active/archived status of a medicine formulary item.
     */
    public function toggleMedicineStatus(Request $request, Medicine $medicine)
    {
        $medicine->update([
            'is_active' => ! $medicine->is_active,
        ]);

        $state = $medicine->is_active ? 'activated' : 'archived';

        \App\Models\AuditLog::record(
            "Medicine {$medicine->name} status changed to {$state}",
            $medicine,
            ['is_active' => $medicine->is_active]
        );

        return $this->respondSuccess($request, "Medicine '{$medicine->name}' has been {$state}.");
    }

    /**
     * Dispose / write off an inventory batch (expired, damaged, recalled, etc.).
     */
    public function disposeBatch(Request $request, MedicineBatch $batch)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'disposal_reason' => 'required|string|in:Expired,Damaged / Broken,Contaminated,Supplier Recall,Other',
            'disposal_notes' => 'nullable|required_if:disposal_reason,Other|string|max:500',
        ], [
            'disposal_notes.required_if' => 'Detailed notes are required when choosing "Other" as disposal reason.',
        ]);

        if ($validator->fails()) {
            if ($this->isAjax($request)) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if ($batch->status === 'disposed') {
            return $this->respondError($request, 'This batch has already been disposed / written off.');
        }

        $validated = $validator->validated();
        $currentQty = (int) $batch->quantity;
        $user = Auth::user();

        DB::transaction(function () use ($batch, $validated, $currentQty, $user) {
            $batch->update([
                'status' => 'disposed',
                'quantity' => 0,
                'disposed_at' => now(),
                'disposed_by' => $user->id,
                'disposal_reason' => $validated['disposal_reason'],
                'disposal_notes' => $validated['disposal_notes'] ?? null,
            ]);

            if ($currentQty > 0) {
                InventoryLog::create([
                    'medicine_id' => $batch->medicine_id,
                    'batch_id' => $batch->id,
                    'action' => 'Deducted',
                    'quantity_changed' => -$currentQty,
                    'remarks' => "Batch Disposed/Write-off [{$validated['disposal_reason']}]: " . ($validated['disposal_notes'] ?? 'Batch removed from active stock'),
                    'performed_by' => $user->id,
                ]);
            }

            \App\Models\AuditLog::record(
                "Medicine Batch Disposed: {$batch->batch_number} ({$currentQty} units written off)",
                $batch,
                [
                    'performed_by' => $user->id,
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'reason' => $validated['disposal_reason'],
                    'notes' => $validated['disposal_notes'] ?? null,
                    'quantity_written_off' => $currentQty,
                ]
            );
        });

        return $this->respondSuccess($request, "Batch {$batch->batch_number} has been officially written off / disposed.");
    }

    /**
     * Adjust physical inventory count for a batch (reconciliation/audit).
     */
    public function adjustStock(Request $request, MedicineBatch $batch)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'new_quantity' => 'required|integer|min:0',
            'adjustment_reason' => 'required|string|in:Physical Recount,Damage / Breakage,Audit Correction,Received Adjustment,Other',
            'adjustment_notes' => 'nullable|required_if:adjustment_reason,Other|string|max:500',
        ], [
            'adjustment_notes.required_if' => 'Detailed notes are required when choosing "Other" as adjustment reason.',
        ]);

        if ($validator->fails()) {
            if ($this->isAjax($request)) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if ($batch->status === 'disposed') {
            return $this->respondError($request, 'Cannot adjust stock on a disposed/written off batch.');
        }

        $validated = $validator->validated();
        $oldQty = (int) $batch->quantity;
        $newQty = (int) $validated['new_quantity'];
        $diff = $newQty - $oldQty;

        if ($diff === 0) {
            return $this->respondSuccess($request, 'Stock count is unchanged.');
        }

        $user = Auth::user();

        DB::transaction(function () use ($batch, $newQty, $diff, $oldQty, $validated, $user) {
            $newStatus = ($newQty === 0) ? 'depleted' : 'active';

            $batch->update([
                'quantity' => $newQty,
                'status' => $newStatus,
            ]);

            InventoryLog::create([
                'medicine_id' => $batch->medicine_id,
                'batch_id' => $batch->id,
                'action' => $diff > 0 ? 'Added' : 'Deducted',
                'quantity_changed' => $diff,
                'remarks' => "Stock Adjustment [{$validated['adjustment_reason']}]: From {$oldQty} to {$newQty} (" . ($diff > 0 ? "+{$diff}" : "{$diff}") . "). Notes: " . ($validated['adjustment_notes'] ?? 'None'),
                'performed_by' => $user->id,
            ]);

            \App\Models\AuditLog::record(
                "Stock Adjusted for Batch {$batch->batch_number}: {$oldQty} -> {$newQty}",
                $batch,
                [
                    'performed_by' => $user->id,
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'old_quantity' => $oldQty,
                    'new_quantity' => $newQty,
                    'delta' => $diff,
                    'reason' => $validated['adjustment_reason'],
                    'notes' => $validated['adjustment_notes'] ?? null,
                ]
            );
        });

        return $this->respondSuccess($request, "Batch {$batch->batch_number} stock count adjusted to {$newQty} successfully.");
    }

    /**
     * Display the Written-Off / Disposed Batches page (grouped by medicine).
     */
    public function writtenOff(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $reasonFilter = $request->input('reason');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Build a closure that applies batch-level filters so we can reuse it
        $batchFilters = function ($q) use ($reasonFilter, $dateFrom, $dateTo) {
            $q->where('status', 'disposed');
            if ($reasonFilter && $reasonFilter !== 'all') {
                $q->where('disposal_reason', $reasonFilter);
            }
            if ($dateFrom) {
                $q->whereDate('disposed_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $q->whereDate('disposed_at', '<=', $dateTo);
            }
        };

        // Query medicines that have at least one disposed batch matching filters
        $query = Medicine::whereHas('batches', $batchFilters)
            ->with(['batches' => function ($q) use ($batchFilters) {
                $batchFilters($q);
                $q->with('disposer')->orderBy('disposed_at', 'desc');
            }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhereHas('batches', function ($q2) use ($search) {
                      $q2->where('status', 'disposed')
                         ->where('batch_number', 'like', "%{$search}%");
                  });
            });
        }

        $medicines = $query->orderBy('name', 'asc')->paginate($perPage);

        // ── Summary Stats (global, unfiltered) ──
        $totalDisposedBatches = MedicineBatch::disposed()->count();
        $totalUnitsDestroyed = (int) DB::table('inventory_logs')
            ->whereIn('batch_id', MedicineBatch::disposed()->pluck('id'))
            ->where('remarks', 'like', '%Disposed/Write-off%')
            ->sum(DB::raw('ABS(quantity_changed)'));

        $reasonBreakdown = MedicineBatch::disposed()
            ->select('disposal_reason', DB::raw('COUNT(*) as count'))
            ->groupBy('disposal_reason')
            ->pluck('count', 'disposal_reason')
            ->toArray();

        $distinctReasons = MedicineBatch::disposed()
            ->whereNotNull('disposal_reason')
            ->distinct()
            ->pluck('disposal_reason');

        $affectedMedicinesCount = Medicine::whereHas('batches', function ($q) {
            $q->where('status', 'disposed');
        })->count();

        return view('pharmacy.written-off', compact(
            'medicines',
            'totalDisposedBatches',
            'totalUnitsDestroyed',
            'reasonBreakdown',
            'distinctReasons',
            'affectedMedicinesCount'
        ));
    }

    /**
     * Cancel a prescription (pharmacist authority).
     */
    public function cancel(Request $request, Prescription $prescription)
    {
        $user = Auth::user();

        // Authority check: pharmacy or super_admin
        if (! in_array($user->role, ['pharmacy', 'super_admin'])) {
            abort(403);
        }

        // Cannot cancel already terminal statuses
        if (in_array($prescription->status, ['dispensed', 'expired'])) {
            return $this->respondError($request, 'Cannot cancel a prescription that is already dispensed or expired.');
        }

        // Check if any stock was already dispensed
        $hasDispensedStock = $prescription->items->contains(function ($item) {
            return ($item->dispensed_quantity ?? 0) > 0;
        });

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|max:500',
            'cancellation_note' => 'nullable|string|max:500',
            'acknowledge_dispensed_stock' => $hasDispensedStock ? 'required|accepted' : 'nullable',
        ]);

        if ($validator->fails()) {
            if ($this->isAjax($request)) {
                return response()->json([
                    'success' => false,
                    'ok' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()->toArray(),
                    'item_ids' => [],
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // The dropdown may hold the "Other" sentinel; the free-text note then carries the reason.
        $reason = trim((string) $validated['cancellation_reason']);
        $note = trim((string) ($validated['cancellation_note'] ?? ''));

        if ($reason === '__other__' || $reason === '') {
            $reason = $note;
        } elseif ($note !== '') {
            $reason .= ' — ' . $note;
        }

        if ($reason === '') {
            return $this->respondError($request, 'A cancellation reason is required.');
        }

        $reason = mb_strimwidth($reason, 0, 500, '…');

        DB::transaction(function () use ($prescription, $user, $validated, $reason, $hasDispensedStock) {
            $prescription->update([
                'status' => 'cancelled',
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            \App\Models\AuditLog::record(
                "Prescription Cancelled by Pharmacy: {$reason}",
                $prescription,
                [
                    'had_dispensed_stock' => $hasDispensedStock,
                    'acknowledged' => $hasDispensedStock && ($validated['acknowledge_dispensed_stock'] ?? false),
                ]
            );
        });

        broadcast(new \App\Events\QueueUpdated('Prescription cancelled', 'pharmacy'));

        return $this->respondSuccess($request, 'Prescription cancelled successfully.');
    }

    /**
     * Cancel a prescription (prescriber authority - doctor/nurse).
     */
    public function cancelByPrescriber(Request $request, Consultation $consultation)
    {
        $user = Auth::user();

        // Load prescription
        $prescription = $consultation->prescriptionRecord;
        if (! $prescription) {
            return back()->with('error', 'No prescription found for this consultation.');
        }

        // Ownership check: must be the prescriber
        if ($prescription->doctor_id !== $user->id) {
            abort(403, 'You can only cancel prescriptions you created.');
        }

        // Cannot cancel already terminal statuses
        if (in_array($prescription->status, ['dispensed', 'expired'])) {
            return back()->with('error', 'Cannot cancel a prescription that is already dispensed or expired.');
        }

        // Check if any stock was already dispensed
        $hasDispensedStock = $prescription->items->contains(function ($item) {
            return ($item->dispensed_quantity ?? 0) > 0;
        });

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
            'acknowledge_dispensed_stock' => $hasDispensedStock ? 'required|accepted' : 'nullable',
        ]);

        DB::transaction(function () use ($prescription, $user, $validated, $hasDispensedStock) {
            $prescription->update([
                'status' => 'cancelled',
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
                'cancellation_reason' => $validated['cancellation_reason'],
            ]);

            \App\Models\AuditLog::record(
                "Prescription Cancelled by Prescriber: {$validated['cancellation_reason']}",
                $prescription,
                [
                    'had_dispensed_stock' => $hasDispensedStock,
                    'acknowledged' => $hasDispensedStock && ($validated['acknowledge_dispensed_stock'] ?? false),
                ]
            );
        });

        broadcast(new \App\Events\QueueUpdated('Prescription cancelled', 'pharmacy'));

        return back()->with('success', 'Prescription cancelled successfully.');
    }
}
