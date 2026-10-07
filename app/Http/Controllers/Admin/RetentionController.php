<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RetentionController extends Controller
{
    public function index()
    {
        $inactivePatients = Patient::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->orderBy('expires_at', 'asc')
            ->paginate(20);

        return view('admin.retention.index', compact('inactivePatients'));
    }

    public function extend(Patient $patient)
    {
        $patient->touch();

        return back()->with('success', "Data retention for patient {$patient->full_name} extended by 1 year.");
    }

    public function destroy(Patient $patient)
    {
        Gate::authorize('delete-retention');

        $patient->delete();

        AuditLog::record("Archived Inactive Patient (Data Retention): {$patient->patient_id}", $patient);

        return back()->with('success', 'Inactive patient record archived safely.');
    }

    public function bulkExtend(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:patients,patient_id',
        ]);

        Patient::whereIn('patient_id', $request->ids)->update([
            'updated_at' => now(),
            'expires_at' => now()->addYears(10),
        ]);

        return back()->with('success', 'Data retention for '.count($request->ids).' patients extended.');
    }

    public function bulkDelete(Request $request)
    {
        Gate::authorize('delete-retention');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:patients,patient_id',
        ]);

        Patient::whereIn('patient_id', $request->ids)->forceDelete();

        return back()->with('success', count($request->ids).' inactive patient records permanently deleted.');
    }
}
