<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AncillaryRequest;
use App\Models\AuditLog;
use App\Models\MedicalCase;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('patient_id', 'like', "%$search%");
            });
        }

        if ($request->filled('classification') && $request->classification !== 'all') {
            $query->where('classification', $request->classification);
        }

        $perPage = 10;

        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / $perPage));

        $rawPage = $request->input('page', 1);
        $page = filter_var($rawPage, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'default' => 1]]);
        if ($page > $totalPages && $total > 0) {
            $page = $totalPages;
        }

        $patients = $query->withCount('consultations')
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        AuditLog::record('Viewed Patient Master List');

        return view('admin.patients.index', compact('patients'));
    }

    public function show(Request $request, Patient $patient)
    {
        // Self-heal: ensure any completed consultations have an archived MedicalCase
        foreach ($patient->consultations()->where('status', 'completed')->with('preTriage')->get() as $c) {
            if (! MedicalCase::where('consultation_id', $c->id)->exists()) {
                $datePrefix = $c->created_at ? $c->created_at->format('Ymd') : now()->format('Ymd');
                $caseNumber = 'CASE-'.$datePrefix.'-'.str_pad($c->id, 5, '0', STR_PAD_LEFT);
                if (MedicalCase::where('case_number', $caseNumber)->exists()) {
                    $caseNumber .= '-C'.$c->id;
                }
                MedicalCase::create([
                    'case_number' => $caseNumber,
                    'patient_id' => $c->patient_id,
                    'consultation_id' => $c->id,
                    'pre_triage_id' => $c->pre_triage_id,
                    'diagnosis' => $c->diagnosis ?: 'Clinical Follow-up & Evaluation',
                    'prescription' => $c->prescription ?: '[]',
                    'vitals_snapshot' => [
                        'bp' => $c->preTriage->blood_pressure ?? $c->blood_pressure ?? null,
                        'temp' => $c->preTriage->temperature ?? $c->temperature ?? null,
                        'wt' => $c->preTriage->weight ?? $c->weight ?? null,
                        'ht' => $c->preTriage->height ?? $c->height ?? null,
                        'hr' => $c->preTriage->heart_rate ?? $c->heart_rate ?? null,
                        'rr' => $c->preTriage->respiratory_rate ?? $c->respiratory_rate ?? null,
                        'pr' => $c->preTriage->pulse_rate ?? $c->pulse_rate ?? null,
                        'spo2' => $c->preTriage->spo2 ?? $c->spo2 ?? ($c->preTriage->oxygen_saturation ?? null),
                    ],
                    'closed_at' => $c->consultation_end_time ?? $c->updated_at ?? now(),
                    'created_at' => $c->created_at ?? now(),
                    'updated_at' => $c->updated_at ?? now(),
                ]);
            }
        }

        $patient->load([
            'consultations' => function ($q) {
                $q->orderBy('consultation_date', 'desc')->orderBy('created_at', 'desc');
            },
            'consultations.doctor',
            'consultations.nurse',
            'medicalCases.consultation.ancillaryRequests.technician',
            'medicalCases.consultation.prescriptionRecord.items',
            'medicalCases.preTriage',
        ]);

        $isUnmasked = false;
        if ($request->has('unmask') && $request->get('unmask') == '1') {
            $reason = $request->input('override_reason', 'Administrative clinical record inspection');
            AuditLog::record("Admin Clinical PHI Override Accessed: {$reason}", $patient, [
                'admin_id' => auth()->id(),
                'reason' => $reason,
                'ip' => $request->ip(),
            ]);
            $isUnmasked = true;
        } else {
            AuditLog::record('Viewed Patient Demographics & Administrative Record', $patient);
        }

        return view('admin.patients.show', compact('patient', 'isUnmasked'));
    }

    public function printItr(Request $request, Patient $patient)
    {
        $caseIds = $request->input('cases', []);

        $query = $patient->medicalCases()
            ->with([
                'consultation.doctor',
                'consultation.nurse',
                'consultation.ancillaryRequests.technician',
                'consultation.prescriptionRecord.items',
                'preTriage',
            ])
            ->orderBy('created_at', 'desc');

        if (! empty($caseIds)) {
            if (is_string($caseIds)) {
                $caseIds = explode(',', $caseIds);
            }
            $query->whereIn('id', $caseIds);
        }

        $cases = $query->get();

        return view('admin.patients.itr', compact('patient', 'cases'));
    }

    public function printAncillary(Request $request, Patient $patient)
    {
        $ids = $request->input('ids', []);
        $caseId = $request->input('case_id');

        $query = AncillaryRequest::with([
            'consultation.doctor',
            'consultation.nurse',
            'consultation.patient',
            'technician',
            'amender',
            'collector',
        ])
            ->whereHas('consultation', function ($q) use ($patient) {
                $q->where('patient_id', $patient->patient_id)
                  ->orWhere('patient_id', (string) $patient->id);
            })
            ->where('status', 'Done');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = array_filter(explode(',', $ids));
            }
            if (! empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        if ($caseId) {
            $case = MedicalCase::find($caseId);
            if ($case && $case->consultation_id) {
                $query->where('consultation_id', $case->consultation_id);
            }
        }

        $requests = $query->orderBy('completed_at', 'desc')->get();

        if ($requests->isEmpty()) {
            return back()->with('error', 'No completed diagnostic results found to print.');
        }

        return view('admin.ancillary.print', compact('patient', 'requests'));
    }

    public function printAncillarySingle(Request $request, AncillaryRequest $ancillary)
    {
        $ancillary->load([
            'consultation.doctor',
            'consultation.nurse',
            'consultation.patient',
            'technician',
            'amender',
            'collector',
        ]);

        $patient = $ancillary->consultation ? $ancillary->consultation->patient : null;
        if (! $patient) {
            return back()->with('error', 'No patient record linked to this diagnostic request.');
        }

        $requests = collect([$ancillary]);

        return view('admin.ancillary.print', compact('patient', 'requests'));
    }
}
