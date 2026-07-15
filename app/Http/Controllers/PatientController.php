<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Display a listing of patients with filtering.
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // 1. Search Query
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function(\Illuminate\Database\Eloquent\Builder $q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('philhealth_number', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Classification
        if ($request->filled('classification') && $request->classification !== 'all') {
            $query->where('classification', $request->classification);
        }

        // 3. Filter by Date From
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $query->where('created_at', '>=', $dateFrom);
        }

        // 4. Filter by Date To
        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->where('created_at', '<=', $dateTo);
        }

        // Paginate results
        $patients = $query->withCount('consultations')->orderBy('last_name', 'asc')->orderBy('first_name', 'asc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        \App\Models\AuditLog::record('Viewed Patient List (Information Desk)');

        return view('frontdesk.patients.index', compact('patients'));
    }

    /**
     * Display the specified patient and their history.
     */
    public function show(Patient $patient)
    {
        // Load relations (e.g. consultations) natively for the timeline
        $patient->load(['consultations' => function($q) {
            $q->orderBy('consultation_date', 'desc')->orderBy('created_at', 'desc');
        }]);

        \App\Models\AuditLog::record('Viewed Patient Info (Information Desk)', $patient);

        return view('frontdesk.patients.show', compact('patient'));
    }
}
