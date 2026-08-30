<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $rawQueue = Consultation::select('consultations.*', 'patients.classification')
            ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('doctor_id', $user->id)
            ->whereIn('status', ['queued', 'active', 'awaiting_results', 'results_ready'])
            ->whereDate('consultation_date', Carbon::today())
            ->with(['patient', 'preTriage', 'ancillaryRequests'])
            ->orderBy('consultations.created_at', 'asc')
            ->get();

        $active = $rawQueue->where('status', 'active')->values();
        $resultsReady = $rawQueue->where('status', 'results_ready')->values();
        $waiting = $rawQueue->where('status', 'queued')->values();
        $awaitingLabs = Consultation::where('doctor_id', $user->id)
            ->where('status', 'awaiting_results')
            ->whereDate('consultation_date', Carbon::today())
            ->with('patient')
            ->get();

        $regular = $waiting->filter(function ($c) {
            return ! in_array($c->classification, ['Senior Citizen', 'PWD']);
        })->values();
        $priority = $waiting->filter(function ($c) {
            return in_array($c->classification, ['Senior Citizen', 'PWD']);
        })->values();

        $queue = collect();
        foreach ($active as $a) {
            $queue->push($a);
        }

        // Results ready take top priority next in line
        foreach ($resultsReady as $rr) {
            $queue->push($rr);
        }

        if ($user->role === 'pedia_doctor') {
            $appointments = $waiting->filter(function ($c) {
                return str_starts_with($c->queue_number, 'APED-');
            })->values();
            $emergencies = $waiting->filter(function ($c) {
                return str_starts_with($c->queue_number, 'PED-E-');
            })->values();
            // Walk-ins: starts with PED- but NOT PED-E- (excludes emergencies to prevent duplicates)
            $walkins = $waiting->filter(function ($c) {
                return str_starts_with($c->queue_number, 'PED-') && !str_starts_with($c->queue_number, 'PED-E-');
            })->values();

            // Push emergencies first
            foreach ($emergencies as $e) {
                $queue->push($e);
            }

            $aptIdx = 0;
            $walkIdx = 0;
            while ($aptIdx < $appointments->count() || $walkIdx < $walkins->count()) {
                if ($aptIdx < $appointments->count()) {
                    $queue->push($appointments[$aptIdx++]);
                }
                if ($aptIdx < $appointments->count()) {
                    $queue->push($appointments[$aptIdx++]);
                }
                if ($walkIdx < $walkins->count()) {
                    $queue->push($walkins[$walkIdx++]);
                }
            }
        } else {
            $regIdx = 0;
            $prioIdx = 0;
            while ($regIdx < $regular->count() || $prioIdx < $priority->count()) {
                if ($prioIdx < $priority->count()) {
                    $queue->push($priority[$prioIdx++]);
                }
                if ($prioIdx < $priority->count()) {
                    $queue->push($priority[$prioIdx++]);
                }
                if ($regIdx < $regular->count()) {
                    $queue->push($regular[$regIdx++]);
                }
            }
        }

        $handledPatients = Consultation::where('doctor_id', $user->id)
            ->whereIn('status', ['completed', 'done', 'cancelled'])
            ->whereDate('consultation_end_time', today())
            ->with('patient')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->unique('patient_id')
            ->take(20);

        return view('doctor.dashboard', compact('user', 'queue', 'handledPatients', 'awaitingLabs'));
    }

    /**
     * Dedicated page for tracking patients waiting for Laboratory / Radiology results.
     */
    public function waitingResults()
    {
        $user = Auth::user();

        $awaitingPatients = Consultation::where('doctor_id', $user->id)
            ->whereIn('status', ['awaiting_results', 'results_ready'])
            ->with(['patient', 'preTriage', 'ancillaryRequests'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('doctor.waiting-results', compact('user', 'awaitingPatients'));
    }

    public function startConsultation(Consultation $consultation)
    {
        $user = Auth::user();

        if ($consultation->doctor_id && $consultation->doctor_id !== $user->id) {
            return redirect()->route('doctor.dashboard')->with('error', 'Unauthorized access.');
        }

        if (! $consultation->doctor_id) {
            $consultation->doctor_id = $user->id;
        }

        if (in_array($consultation->status, ['queued', 'awaiting_results', 'results_ready'])) {
            $consultation->status = 'active';
            if (! $consultation->consultation_start_time) {
                $consultation->consultation_start_time = now();
            }
            $consultation->save();
        }

        $patient = $consultation->patient;
        $consultation->load(['preTriage', 'ancillaryRequests.technician']);

        $pastConsultations = \App\Models\Consultation::where('patient_id', $patient->patient_id)
            ->where('id', '!=', $consultation->id)
            ->whereIn('status', ['completed', 'done'])
            ->with('preTriage')
            ->orderBy('created_at', 'desc')
            ->get();

        // LOG ACCESS: Accountability rule for medical records
        \App\Models\AuditLog::record("Accessed Patient Medical Folder: {$patient->patient_id}", $patient);

        // Check department statuses
        $isPharmacyOnline = \App\Models\User::where('role', 'pharmacy')->where('status', 'Present')->exists();
        $isLabOnline = \App\Models\User::where('role', 'laboratory')->where('status', 'Present')->exists();
        $isRadOnline = \App\Models\User::where('role', 'radiology')->where('status', 'Present')->exists();

        return view('doctor.consultation', compact('consultation', 'patient', 'pastConsultations', 'isPharmacyOnline', 'isLabOnline', 'isRadOnline'));
    }

    public function completeConsultation(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->doctor_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'diagnosis' => 'required|string',
            'prescription' => 'nullable|string', // Legacy text
            'prescriptions_list' => 'nullable|array', // New structured array
            'prescriptions_list.*.medicine_name' => 'required|string',
            'prescriptions_list.*.dosage' => 'nullable|string',
            'prescriptions_list.*.frequency' => 'nullable|string',
            'prescriptions_list.*.duration' => 'nullable|string',
            'prescriptions_list.*.quantity' => 'nullable|integer',
            'medical_notes' => 'nullable|string',
            'is_followup_needed' => 'nullable|boolean',
            'followup_date' => 'nullable|required_if:is_followup_needed,1|date|after_or_equal:today',
            'followup_reason' => 'nullable|required_if:is_followup_needed,1|string|max:255',
        ]);

        $hasPendingAncillary = $consultation->ancillaryRequests()->where('status', 'Pending')->exists();
        $isFollowupNeeded = $request->has('is_followup_needed') || $hasPendingAncillary;
        $followupDate = $isFollowupNeeded ? ($validated['followup_date'] ?? now()->addDays(7)->toDateString()) : null;
        $followupReason = $isFollowupNeeded ? ($validated['followup_reason'] ?? 'Pending Diagnostic Results Review') : null;

        $consultation->update([
            'diagnosis' => $validated['diagnosis'],
            'prescription' => $validated['prescription'],
            'medical_notes' => $validated['medical_notes'],
            'is_followup_needed' => $isFollowupNeeded,
            'followup_date' => $followupDate,
            'followup_reason' => $followupReason,
            'followup_doctor_id' => $isFollowupNeeded ? $user->id : null,
            'blood_pressure' => $consultation->preTriage->blood_pressure ?? null,
            'temperature' => $consultation->preTriage->temperature ?? null,
            'weight' => $consultation->preTriage->weight ?? null,
            'height' => $consultation->preTriage->height ?? null,
            'heart_rate' => $consultation->preTriage->heart_rate ?? null,
            'respiratory_rate' => $consultation->preTriage->respiratory_rate ?? null,
            'pulse_rate' => $consultation->preTriage->pulse_rate ?? null,
            'spo2' => $consultation->preTriage->spo2 ?? ($consultation->preTriage->oxygen_saturation ?? null),
            'consultation_end_time' => now(),
            'status' => 'completed',
        ]);

        if (! empty($validated['prescriptions_list'])) {
            $prescriptionRecord = \App\Models\Prescription::create([
                'consultation_id' => $consultation->id,
                'patient_id' => $consultation->patient_id,
                'doctor_id' => $user->id,
                'status' => 'pending',
            ]);

            foreach ($validated['prescriptions_list'] as $item) {
                \App\Models\PrescriptionItem::create([
                    'prescription_id' => $prescriptionRecord->id,
                    'medicine_name' => $item['medicine_name'],
                    'dosage' => $item['dosage'] ?? null,
                    'frequency' => $item['frequency'] ?? null,
                    'duration' => $item['duration'] ?? null,
                    'quantity' => $item['quantity'] ?? null,
                ]);
            }

            $pharmacists = \App\Models\User::where('role', 'pharmacy')->get();
            \Illuminate\Support\Facades\Notification::send(
                $pharmacists,
                new \App\Notifications\NewPrescriptionNotification(
                    $consultation->patient->first_name.' '.$consultation->patient->last_name,
                    $user->id,
                    $prescriptionRecord->id
                )
            );

            broadcast(new \App\Events\QueueUpdated('New prescription', 'pharmacy'));
        }

        if ($consultation->preTriage) {
            $consultation->preTriage->update(['status' => 'completed']);
            
            // Mark the original appointment as done so it drops off active calendar queues
            if ($consultation->preTriage->appointment_id) {
                \App\Models\Appointment::where('id', $consultation->preTriage->appointment_id)
                    ->update(['status' => 'done']);
            }
        }

        // ── Record Medical Case (Persistent Case Record) ──────────────────
        \App\Models\MedicalCase::create([
            'case_number' => 'CASE-'.now()->format('Ymd').'-'.str_pad($consultation->id, 5, '0', STR_PAD_LEFT),
            'patient_id' => $consultation->patient_id,
            'consultation_id' => $consultation->id,
            'pre_triage_id' => $consultation->pre_triage_id,
            'diagnosis' => $validated['diagnosis'],
            'prescription' => $validated['prescription'],
            'vitals_snapshot' => [
                'bp' => $consultation->preTriage->blood_pressure ?? null,
                'temp' => $consultation->preTriage->temperature ?? null,
                'wt' => $consultation->preTriage->weight ?? null,
                'ht' => $consultation->preTriage->height ?? null,
                'hr' => $consultation->preTriage->heart_rate ?? null,
                'rr' => $consultation->preTriage->respiratory_rate ?? null,
                'pr' => $consultation->preTriage->pulse_rate ?? null,
                'spo2' => $consultation->preTriage->spo2 ?? ($consultation->preTriage->oxygen_saturation ?? null),
            ],
            'closed_at' => now(),
        ]);

        // ── Audit Trail ──────────────────────────────────────────────────
        \App\Models\AuditLog::record(
            $request->has('is_followup_needed') ? 'Completed Consultation (Follow-up Scheduled)' : 'Completed Consultation',
            $consultation,
            [
                'diagnosis' => $validated['diagnosis'],
                'is_followup' => $request->has('is_followup_needed'),
                'followup_date' => $request->has('is_followup_needed') ? $validated['followup_date'] : null,
            ]
        );

        // ── Update Queue Status ───────────────────────────────────────────
        \App\Models\Queue::where('patient_id', $consultation->patient_id)
            ->where('queue_number', $consultation->queue_number)
            ->whereDate('created_at', today())
            ->update(['status' => 'Completed']);

        // ── Fulfill Previous Follow-ups ───────────────────────────────────
        // Mark any prior unfulfilled follow-up consultations as completed
        // since this visit satisfies the follow-up requirement.
        \App\Models\Consultation::where('patient_id', $consultation->patient_id)
            ->where('id', '!=', $consultation->id)
            ->where('is_followup_needed', true)
            ->whereNull('followup_completed_at')
            ->update(['followup_completed_at' => now()]);

        // ── Update Patient Follow-up Tracking ────────────────────────────
        $patient = $consultation->patient;
        if ($patient) {
            if ($isFollowupNeeded) {
                $patient->update([
                    'next_followup_date' => $followupDate,
                    'previous_doctor_id' => $user->id,
                ]);
            } else {
                $patient->update([
                    'next_followup_date' => null,
                    'previous_doctor_id' => null,
                ]);
            }
        }

        \App\Models\AuditLog::record('Consultation Completed', $consultation, [
            'patient_id' => $consultation->patient_id,
            'diagnosis' => $validated['diagnosis'],
        ]);

        if ($consultation->is_followup_needed) {
            \App\Models\AuditLog::record('Follow-up Scheduled', $consultation, [
                'followup_date' => $consultation->followup_date,
                'reason' => $consultation->followup_reason,
            ]);
        }

        return redirect()->route('doctor.dashboard')->with('success', 'Consultation completed for '.$consultation->patient->first_name);
    }

    public function showPatient(\App\Models\Patient $patient)
    {
        $user = Auth::user();

        $hasActiveConsultation = Consultation::where('patient_id', $patient->patient_id)
            ->where('doctor_id', $user->id)
            ->whereIn('status', ['active', 'queued', 'awaiting_results', 'results_ready'])
            ->whereDate('consultation_date', \Carbon\Carbon::today())
            ->exists();

        if (! $hasActiveConsultation) {
            return redirect()->route('doctor.dashboard')->with('error', 'Unauthorized access. You may only view historical records of patients currently active in your queue.');
        }

        $patient->load(['consultations' => function ($query) {
            $query->orderBy('created_at', 'asc'); // Ascending for chronological chart
        }, 'consultations.doctor', 'consultations.nurse']);

        $vitalsData = $patient->consultations->filter(function ($c) {
            return $c->blood_pressure || $c->weight;
        })->map(function ($c) {
            $systolic = null;
            $diastolic = null;
            if ($c->blood_pressure && str_contains($c->blood_pressure, '/')) {
                [$systolic, $diastolic] = explode('/', $c->blood_pressure);
            }

            return [
                'date' => $c->created_at->format('M d, Y'),
                'weight' => floatval($c->weight),
                'systolic' => floatval($systolic),
                'diastolic' => floatval($diastolic),
            ];
        })->values();

        // Reload consultations descending for the timeline display
        $patient->load(['consultations' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'consultations.doctor', 'consultations.nurse']);

        return view('doctor.patients.show', [
            'patient' => $patient,
            'vitalsChartData' => $vitalsData,
        ]);
    }

    public function storeAncillaryRequest(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->doctor_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:Laboratory,Radiology',
            'test_name' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        \App\Models\AncillaryRequest::create([
            'consultation_id' => $consultation->id,
            'type' => $validated['type'],
            'test_name' => $validated['test_name'],
            'remarks' => $validated['remarks'] ?? null,
            'status' => 'Pending',
        ]);

        // Move patient to "Waiting for Results" status
        $consultation->update(['status' => 'awaiting_results']);

        broadcast(new \App\Events\QueueUpdated('New '.$validated['type'].' request', strtolower($validated['type'])));

        return back()->with('success', $validated['type'].' request sent to lab/radiology. Patient has been moved to the Waiting for Results queue.');
    }
}
