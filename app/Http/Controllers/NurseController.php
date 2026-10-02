<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NurseController extends Controller
{
    use \App\Traits\InteractsWithPrescriptions;

    public function dashboard()
    {
        $user = Auth::user();

        $rawQueue = Consultation::select('consultations.*', 'patients.classification')
            ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('nurse_id', $user->id)
            ->whereIn('status', ['queued', 'active', 'awaiting_results'])
            ->whereDate('consultation_date', Carbon::today())
            ->with(['patient', 'preTriage'])
            ->orderBy('consultations.created_at', 'asc')
            ->get();

        $active = $rawQueue->where('status', 'active')->values();
        $waiting = $rawQueue->where('status', 'queued')->values();
        $awaitingLabs = $rawQueue->where('status', 'awaiting_results')->values();

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

        $doctors = User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->get();

        $handledBase = Consultation::where('nurse_id', $user->id)
            ->whereIn('status', ['completed', 'done', 'cancelled'])
            ->where(function ($q) {
                $q->whereDate('consultation_end_time', today())
                  ->orWhere(function ($sub) {
                      $sub->whereNull('consultation_end_time')
                          ->whereDate('updated_at', today());
                  });
            });

        $handledCount = (clone $handledBase)->distinct('patient_id')->count('patient_id');
        $handledPatients = $handledBase->with('patient')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->unique('patient_id')
            ->take(20);

        return view('nurse.dashboard', compact('user', 'queue', 'doctors', 'awaitingLabs', 'handledPatients', 'handledCount'));
    }

    public function forwardToDoctor(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);

        $consultation->update([
            'doctor_id' => $validated['doctor_id'],
            'nurse_id' => null, // remove from nurse queue
            'status' => 'queued', // Reset status so doctor sees them as waiting
            'consultation_start_time' => null, // Clear the start time
            // 'created_at' => now(), // Uncomment this if you want them to go to the BACK of the line
        ]);

        broadcast(new \App\Events\QueueUpdated('Patient forwarded to doctor', 'general'));

        return back()->with('success', 'Patient forwarded to the scheduled doctor successfully.');
    }

    public function startConsultation(Consultation $consultation)
    {
        $user = Auth::user();

        if ($consultation->nurse_id && $consultation->nurse_id !== $user->id) {
            return redirect()->route('nurse.dashboard')->with('error', 'Unauthorized access.');
        }

        if (! $consultation->nurse_id) {
            $consultation->nurse_id = $user->id;
        }

        if (in_array($consultation->status, ['queued', 'awaiting_results', 'results_ready'])) {
            $consultation->status = 'active';
            if (! $consultation->consultation_start_time) {
                $consultation->consultation_start_time = now();
            }
            $consultation->save();

            // Sync Queue status & record called_at timestamp for wait-time analytics & queue board
            \App\Models\Queue::where('patient_id', $consultation->patient_id)
                ->where('queue_number', $consultation->queue_number)
                ->whereDate('created_at', today())
                ->where('status', 'Waiting')
                ->update([
                    'status' => 'Calling',
                    'called_at' => now(),
                ]);
        }

        $patient = $consultation->patient;
        $consultation->load('preTriage');

        $pastConsultations = \App\Models\Consultation::where('patient_id', $patient->patient_id)
            ->where('id', '!=', $consultation->id)
            ->whereIn('status', ['completed', 'done'])
            ->with(['doctor', 'nurse', 'preTriage', 'ancillaryRequests.technician'])
            ->orderBy('created_at', 'desc')
            ->get();

        // LOG ACCESS: Accountability rule for medical records
        \App\Models\AuditLog::record("Accessed Patient Medical Folder: {$patient->patient_id}", $patient);

        // Check department statuses
        $isPharmacyOnline = \App\Models\User::where('role', 'pharmacy')->whereIn('status', ['Online', 'online', 'Present'])->exists();
        $isLabOnline = \App\Models\User::where('role', 'laboratory')->whereIn('status', ['Online', 'online', 'Present'])->exists();
        $isRadOnline = \App\Models\User::where('role', 'radiology')->whereIn('status', ['Online', 'online', 'Present'])->exists();

        return view('nurse.consultation', compact('consultation', 'patient', 'pastConsultations', 'isPharmacyOnline', 'isLabOnline', 'isRadOnline'));
    }

    public function completeConsultation(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->nurse_id !== $user->id) {
            abort(403);
        }

        if (in_array($consultation->status, ['completed', 'done', 'cancelled'])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This consultation has already been completed.',
                ], 422);
            }

            return redirect()->route('nurse.dashboard')->with('warning', 'This consultation has already been completed.');
        }

        $validated = $request->validate([
            'diagnosis' => 'required|string',
            'prescriptions_list' => 'nullable|array',
            'prescriptions_list.*.medicine_name' => 'required|string',
            'prescriptions_list.*.dosage' => 'nullable|string',
            'prescriptions_list.*.frequency' => 'nullable|string',
            'prescriptions_list.*.duration' => 'nullable|string',
            'prescriptions_list.*.quantity' => 'nullable|integer',
            'prescriptions_list.*.medicine_id' => 'nullable|integer|exists:medicines,id',
            'prescriptions_list.*.is_otc' => 'nullable|boolean',
            'medical_notes' => 'nullable|string',
            'is_followup_needed' => 'nullable|boolean',
            'followup_date' => 'nullable|required_if:is_followup_needed,1|date|after_or_equal:today',
            'followup_reason' => 'nullable|required_if:is_followup_needed,1|string|max:255',
        ]);

        $hasActiveAncillary = $consultation->ancillaryRequests()
            ->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress'])
            ->exists();
        $isFollowupNeeded = $request->has('is_followup_needed') || $hasActiveAncillary;
        $followupDate = $isFollowupNeeded ? ($validated['followup_date'] ?? now()->addDays(3)->toDateString()) : null;
        $followupReason = $isFollowupNeeded ? ($validated['followup_reason'] ?? ($hasActiveAncillary ? 'Pending Diagnostic Results (Lab in progress / deferred)' : 'Scheduled Follow-Up')) : null;

        // Resolve and split prescriptions into RHU vs OTC
        $resolved = $this->resolvePrescriptionItems($validated['prescriptions_list'] ?? []);
        $rhuItems = $resolved['rhu'];
        $otcItems = $resolved['otc'];
        $reclassified = $resolved['reclassified'];

        $prescriptionText = $this->formatPrescriptionText($rhuItems, $otcItems);

        $consultation->update([
            'diagnosis' => $validated['diagnosis'],
            'prescription' => $prescriptionText,
            'medical_notes' => $validated['medical_notes'] ?? null,
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
            'status' => 'completed',
            'consultation_end_time' => now(),
        ]);

        // Create Prescription record only if RHU items exist
        $prescriptionRecord = $this->recordPrescription($consultation, $user, $rhuItems, $otcItems, $prescriptionText);

        if ($prescriptionRecord) {
            $this->notifyPharmacy($prescriptionRecord, $user, $consultation);
        }

        if (! empty($reclassified)) {
            $names = collect($reclassified)->pluck('medicine_name')->implode(', ');
            session()->flash('warning', "The following items were not found in RHU inventory and have been marked as OTC: {$names}");
        }

        // Mark the pre-triage as completed and update the appointment status
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
            'case_number' => 'CASE-N'.now()->format('Ymd').'-'.str_pad($consultation->id, 5, '0', STR_PAD_LEFT),
            'patient_id' => $consultation->patient_id,
            'consultation_id' => $consultation->id,
            'pre_triage_id' => $consultation->pre_triage_id,
            'diagnosis' => $validated['diagnosis'],
            'prescription' => $prescriptionText,
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
            $request->has('is_followup_needed') ? 'Completed Consultation (Nurse - Follow-up Scheduled)' : 'Completed Consultation (Nurse)',
            $consultation,
            [
                'diagnosis' => $validated['diagnosis'],
                'is_followup' => $request->has('is_followup_needed'),
                'followup_date' => $request->has('is_followup_needed') ? $validated['followup_date'] : null,
            ]
        );

        // ── Update Queue Status ──────────────────────────────────────────
        \App\Models\Queue::where('patient_id', $consultation->patient_id)
            ->where('queue_number', $consultation->queue_number)
            ->whereDate('created_at', today())
            ->update(['status' => 'Completed']);

        // ── Fulfill Previous Follow-ups ──────────────────────────────────
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

        return redirect()->route('nurse.dashboard')->with('success', 'Consultation completed for '.$consultation->patient->first_name);
    }

    /**
     * Record an official post-consultation clinical addendum.
     */
    public function storeAddendum(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->nurse_id && $consultation->nurse_id !== $user->id) {
            abort(403, 'Only the attending clinician may record an addendum.');
        }

        if (! in_array($consultation->status, ['completed', 'done'])) {
            return back()->with('error', 'Addenda can only be recorded on completed consultations.');
        }

        $validated = $request->validate([
            'addendum_text' => 'required|string|max:2000',
        ]);

        $timestamp = now()->format('Y-m-d h:i A');
        $clinicianName = $user->formatted_name ?? $user->name;
        $entry = "\n[CLINICAL ADDENDUM - {$timestamp} by {$clinicianName}]: {$validated['addendum_text']}";

        $consultation->update([
            'medical_notes' => ($consultation->medical_notes ? $consultation->medical_notes . "\n" : '') . $entry,
        ]);

        \App\Models\AuditLog::record("Clinical Addendum Recorded for Consultation #{$consultation->id}", $consultation, [
            'author_id' => $user->id,
            'addendum' => $validated['addendum_text'],
        ]);

        return back()->with('success', 'Clinical addendum recorded successfully.');
    }

    public function storeAncillaryRequest(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->nurse_id && $consultation->nurse_id !== $user->id) {
            abort(403);
        }
        if (! $consultation->nurse_id) {
            $consultation->nurse_id = $user->id;
            $consultation->save();
        }

        $validated = $request->validate([
            'type' => 'required|in:Laboratory,Radiology',
            'test_name' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        // Restrict allowed tests strictly to authoritative RHU diagnostic catalog
        if (! \App\Services\DiagnosticCatalogService::isValidTest($validated['type'], $validated['test_name'])) {
            $allowed = implode(', ', \App\Services\DiagnosticCatalogService::getAllowedTests($validated['type']));
            return back()->with('error', "Invalid {$validated['type']} test selected. Available tests: {$allowed}.");
        }

        // Enforce: only one active request per test type at a time for this consultation
        $existingActive = \App\Models\AncillaryRequest::where('consultation_id', $consultation->id)
            ->where('test_name', $validated['test_name'])
            ->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress'])
            ->exists();

        if ($existingActive) {
            return back()->with('error', "An active request for {$validated['test_name']} is already pending or in progress for this patient.");
        }

        \App\Models\AncillaryRequest::create([
            'consultation_id' => $consultation->id,
            'type' => $validated['type'],
            'test_name' => $validated['test_name'],
            'remarks' => $validated['remarks'] ?? null,
            'status' => 'Pending',
        ]);

        $consultation->update(['status' => 'awaiting_results']);

        broadcast(new \App\Events\QueueUpdated('New '.$validated['type'].' request', strtolower($validated['type'])));

        return back()->with('success', $validated['type'].' request for '.$validated['test_name'].' sent to queue. Patient moved to Waiting for Results.');
    }

    public function repeatAncillaryRequest(Request $request, \App\Models\AncillaryRequest $ancillary)
    {
        $consultation = $ancillary->consultation;
        if (! $consultation) {
            abort(404);
        }

        $user = Auth::user();
        if ($consultation->nurse_id && $consultation->nurse_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        $newRemarks = $validated['remarks'] ?? ($ancillary->remarks ? "Repeat: {$ancillary->remarks}" : 'Repeat test requested');

        $repeatRequest = \App\Models\AncillaryRequest::create([
            'consultation_id' => $consultation->id,
            'type' => $ancillary->type,
            'test_name' => $ancillary->test_name,
            'remarks' => $newRemarks,
            'status' => 'Pending',
            'parent_id' => $ancillary->id,
            'is_repeat' => true,
        ]);

        $consultation->update(['status' => 'awaiting_results']);

        \App\Models\AuditLog::record('Diagnostic Test Re-ordered / Repeated by Nurse', $repeatRequest, [
            'original_id' => $ancillary->id,
            'reason' => $newRemarks,
        ]);

        broadcast(new \App\Events\QueueUpdated('Repeat diagnostic ordered', strtolower($ancillary->type)));

        return back()->with('success', "Repeat order for {$ancillary->test_name} created. Patient returned to Waiting for Results.");
    }

    public function cancelAncillaryRequest(Request $request, \App\Models\AncillaryRequest $ancillary)
    {
        $consultation = $ancillary->consultation;
        if (! $consultation) {
            abort(404);
        }

        $user = Auth::user();
        if ($consultation->nurse_id && $consultation->nurse_id !== $user->id) {
            abort(403);
        }

        if ($ancillary->status === 'Done') {
            return back()->with('error', 'Cannot cancel an already completed diagnostic test.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:255',
        ]);

        $reason = $validated['cancellation_reason'] ?? 'Cancelled by clinical nurse';

        $ancillary->update([
            'status' => 'Cancelled',
            'cancellation_reason' => $reason,
            'cancelled_by' => $user->id,
            'cancelled_at' => now(),
        ]);

        $hasActiveUnfinished = $consultation->ancillaryRequests()
            ->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress'])
            ->exists();

        if (! $hasActiveUnfinished) {
            $hasDone = $consultation->ancillaryRequests()->where('status', 'Done')->exists();
            $consultation->update(['status' => $hasDone ? 'results_ready' : 'active']);
        }

        \App\Models\AuditLog::record('Diagnostic Request Cancelled by Nurse', $ancillary, ['reason' => $reason]);
        broadcast(new \App\Events\QueueUpdated('Diagnostic request cancelled', strtolower($ancillary->type)));

        return back()->with('success', "{$ancillary->test_name} request has been cancelled.");
    }

    /**
     * Cancel a prescription by the prescriber (nurse).
     */
    public function cancelByPrescriber(Request $request, Consultation $consultation)
    {
        $user = Auth::user();

        $prescription = $consultation->prescriptionRecord;
        if (! $prescription) {
            return back()->with('error', 'No prescription found for this consultation.');
        }

        // Ownership check: must be the prescriber (nurse who completed the consultation)
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

    /**
     * Cancel an active or queued consultation (e.g. Patient Walked Out, Refused, or Referred).
     */
    public function cancelConsultation(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->nurse_id && $consultation->nurse_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $reason = $validated['cancellation_reason'];
        if (!empty($validated['notes'])) {
            $reason .= ' - Notes: ' . $validated['notes'];
        }

        DB::transaction(function () use ($consultation, $user, $reason) {
            $consultation->update([
                'status' => 'cancelled',
                'consultation_end_time' => now(),
                'medical_notes' => trim(($consultation->medical_notes ? $consultation->medical_notes . "\n" : '') . "[CONSULTATION CANCELLED / WALKOUT]: {$reason}"),
            ]);

            // If pre-triage exists, cancel it and sync appointment
            if ($consultation->preTriage) {
                $consultation->preTriage->update(['status' => 'cancelled']);
                if ($consultation->preTriage->appointment_id) {
                    \App\Models\Appointment::where('id', $consultation->preTriage->appointment_id)
                        ->update([
                            'status' => 'cancelled',
                            'cancellation_reason' => $reason,
                            'cancelled_by' => $user->id,
                            'cancelled_at' => now(),
                        ]);
                }
            }

            // Sync linked queue
            \App\Models\Queue::where('patient_id', $consultation->patient_id)
                ->where('queue_number', $consultation->queue_number)
                ->whereDate('created_at', today())
                ->update(['status' => 'Cancelled']);

            // Cancel any pending ancillary requests for this consultation
            $consultation->ancillaryRequests()
                ->whereIn('status', ['Pending', 'Specimen Collected'])
                ->update([
                    'status' => 'Cancelled',
                    'cancellation_reason' => "Consultation cancelled/walkout: {$reason}",
                    'cancelled_by' => $user->id,
                    'cancelled_at' => now(),
                ]);

            // If prescription was drafted and pending, cancel it
            if ($consultation->prescriptionRecord && !in_array($consultation->prescriptionRecord->status, ['dispensed', 'cancelled'])) {
                $consultation->prescriptionRecord->update([
                    'status' => 'cancelled',
                    'cancelled_by' => $user->id,
                    'cancelled_at' => now(),
                    'cancellation_reason' => "Consultation cancelled/walkout: {$reason}",
                ]);
            }

            \App\Models\AuditLog::record("Consultation Cancelled / Patient Walked Out: {$reason}", $consultation, [
                'reason' => $reason,
                'nurse_id' => $user->id,
            ]);
        });

        broadcast(new \App\Events\QueueUpdated('Consultation cancelled / patient walkout', 'general'));

        return redirect()->route('nurse.dashboard')->with('success', 'Consultation marked as cancelled / walked out.');
    }
}

