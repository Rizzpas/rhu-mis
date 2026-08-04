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

        return view('pharmacy.dashboard', compact('prescriptions'));
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

        $medicines = $query->orderBy('name')->paginate($perPage);

        $forms = Medicine::select('form')->whereNotNull('form')->distinct()->pluck('form');

        // Expiration Statistics for alerts
        $expiredBatchesCount = MedicineBatch::where('quantity', '>', 0)->whereDate('expiration_date', '<=', today())->count();
        $expiringSoonCount = MedicineBatch::where('quantity', '>', 0)->whereDate('expiration_date', '>', today())->whereDate('expiration_date', '<=', today()->addDays(30))->count();
        $expiring60Count = MedicineBatch::where('quantity', '>', 0)->whereDate('expiration_date', '>', today()->addDays(30))->whereDate('expiration_date', '<=', today()->addDays(60))->count();

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
        ]);

        Medicine::create($validated);

        return back()->with('success', 'New medicine added successfully.');
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
            'batch_number' => 'nullable|string|max:255',
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
