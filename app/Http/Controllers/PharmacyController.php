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
        // Load pending prescriptions, oldest first
        $prescriptions = Prescription::where('status', 'pending')
            ->with(['patient', 'doctor', 'items.medicine.batches'])
            ->orderBy('created_at', 'asc')
            ->get();

        // ── Inventory Stats for Banners ──
        $expiredBatchesCount = MedicineBatch::where('quantity', '>', 0)
            ->whereDate('expiration_date', '<=', today())->count();

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
        $query = Prescription::where('status', 'dispensed')->with(['patient', 'doctor', 'items']);

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
     */
    public function dispense(Request $request, Prescription $prescription)
    {
        if ($prescription->status !== 'pending') {
            return back()->with('error', 'This prescription has already been processed.');
        }

        // Validate stock availability first
        $insufficientStock = [];
        foreach ($prescription->items as $item) {
            $requiredQuantity = $item->quantity;
            if ($requiredQuantity <= 0) {
                $insufficientStock[] = "Invalid quantity for {$item->medicine_name}. Quantity must be greater than zero.";

                continue;
            }

            // Try to match the prescribed medicine name to an actual Medicine in the database.
            // The frontend might append the form, e.g., "Paracetamol (Tablet)"
            $cleanName = trim(preg_replace('/\s*\([^)]*\)$/', '', $item->medicine_name));

            $medicine = Medicine::where('name', $item->medicine_name)
                ->orWhere('generic_name', $item->medicine_name)
                ->orWhere('name', $cleanName)
                ->orWhere('generic_name', $cleanName)
                ->first();

            if (! $medicine) {
                $insufficientStock[] = "{$item->medicine_name} is not in the system inventory.";

                continue;
            }

            if ($medicine->total_stock < $requiredQuantity) {
                $insufficientStock[] = "Insufficient stock for {$item->medicine_name}. Required: {$requiredQuantity}, Available: {$medicine->total_stock}.";
            }
        }

        if (count($insufficientStock) > 0) {
            return back()->with('error', implode(' ', $insufficientStock));
        }

        DB::transaction(function () use ($prescription) {
            foreach ($prescription->items as $item) {
                $requiredQuantity = $item->quantity;
                if (! $requiredQuantity) {
                    continue;
                }

                $cleanName = trim(preg_replace('/\s*\([^)]*\)$/', '', $item->medicine_name));

                $medicine = Medicine::where('name', $item->medicine_name)
                    ->orWhere('generic_name', $item->medicine_name)
                    ->orWhere('name', $cleanName)
                    ->orWhere('generic_name', $cleanName)
                    ->first();

                // Deduct from batches (FIFO based on expiration date)
                $batches = $medicine->batches()
                    ->whereDate('expiration_date', '>=', today())
                    ->where('quantity', '>', 0)
                    ->orderBy('expiration_date', 'asc')
                    ->get();

                $remainingToDeduct = $requiredQuantity;

                foreach ($batches as $batch) {
                    if ($remainingToDeduct <= 0) {
                        break;
                    }

                    $deductAmount = min($batch->quantity, $remainingToDeduct);
                    $batch->decrement('quantity', $deductAmount);

                    InventoryLog::create([
                        'medicine_id' => $medicine->id,
                        'batch_id' => $batch->id,
                        'action' => 'Dispensed',
                        'quantity_changed' => -$deductAmount,
                        'remarks' => 'Dispensed for Case #'.$prescription->consultation_id,
                        'performed_by' => Auth::id(),
                    ]);

                    $remainingToDeduct -= $deductAmount;
                }
            }

            $prescription->update(['status' => 'dispensed']);

            // Also update consultation status if it was just completed/pending pharmacy
            // In a real system, the consultation was already marked 'completed' by the doctor/nurse,
            // but we might want a 'dispensed' final status if needed.
        });

        broadcast(new \App\Events\QueueUpdated('Prescription dispensed', 'pharmacy'));

        return back()->with('success', 'Prescription dispensed successfully. Inventory updated.');
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
                        $q->where('quantity', '>', 0)->whereDate('expiration_date', '<=', today());
                    });
                    break;
                case 'expiring_soon':
                    $query->whereHas('batches', function ($q) {
                        $q->where('quantity', '>', 0)
                          ->whereDate('expiration_date', '>', today())
                          ->whereDate('expiration_date', '<=', today()->addDays(30));
                    });
                    break;
                case 'low_stock':
                    $query->withSum(['batches as active_stock' => function ($q) {
                        $q->where('quantity', '>', 0)->whereDate('expiration_date', '>=', today());
                    }], 'quantity')
                    ->having('active_stock', '>', 0)
                    ->having('active_stock', '<', 20);
                    break;
                case 'out_of_stock':
                    $query->whereDoesntHave('batches', function ($q) {
                        $q->where('quantity', '>', 0)->whereDate('expiration_date', '>=', today());
                    });
                    break;
            }
        }

        $query->addSelect([
            'earliest_expiry' => \App\Models\MedicineBatch::select('expiration_date')
                ->whereColumn('medicine_id', 'medicines.id')
                ->where('quantity', '>', 0)
                ->orderBy('expiration_date', 'asc')
                ->limit(1)
        ]);

        $medicines = $query->orderByRaw('earliest_expiry IS NULL ASC, earliest_expiry ASC')
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        $forms = Medicine::select('form')->whereNotNull('form')->distinct()->pluck('form');

        // Get counts for the banner (always show original counts)
        $expiredBatchesCount = \App\Models\MedicineBatch::where('quantity', '>', 0)
            ->whereDate('expiration_date', '<=', today())->count();

        $expiringSoonCount = \App\Models\MedicineBatch::where('quantity', '>', 0)
            ->whereDate('expiration_date', '>', today())
            ->whereDate('expiration_date', '<=', today()->addDays(30))->count();

        $expiring60Count = \App\Models\MedicineBatch::where('quantity', '>', 0)
            ->whereDate('expiration_date', '>', today()->addDays(30))
            ->whereDate('expiration_date', '<=', today()->addDays(60))->count();

        return view('pharmacy.medicines', compact('medicines', 'forms', 'expiredBatchesCount', 'expiringSoonCount', 'expiring60Count'));

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
            'batch_number' => 'required|string|max:255',
            'expiration_date' => 'required|date|after:today',
            'quantity' => 'required|integer|min:1',
        ]);

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
            'batch_number' => 'required|string|max:255',
            'expiration_date' => 'required|date|after:today',
            'quantity' => 'required|integer|min:1',
        ]);

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
}
