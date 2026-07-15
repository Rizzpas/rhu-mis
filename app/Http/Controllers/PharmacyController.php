<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    /**
     * Display the pharmacy dashboard.
     */
    public function dashboard()
    {
        // For storyboard purposes, we return an empty collection to simulate the UI state
        // In a real implementation, you would load pending prescriptions here.
        $prescriptions = collect([]);

        return view('pharmacy.dashboard', compact('prescriptions'));
    }

    /**
     * Display the medicine list/inventory.
     */
    public function medicines(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $formFilter = $request->input('form_filter');

        $query = \App\Models\Medicine::query();

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
        
        $forms = \App\Models\Medicine::select('form')->whereNotNull('form')->distinct()->pluck('form');

        return view('pharmacy.medicines', compact('medicines', 'forms'));
    }
}
