<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\MedicalCase;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    use \App\Traits\InteractsWithPrescriptions;

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
            ->where(function ($q) {
                $q->whereIn('status', ['awaiting_results', 'results_ready'])
                  ->orWhere(function ($sub) {
                      $sub->whereNotIn('status', ['completed', 'done', 'cancelled'])
                          ->whereHas('ancillaryRequests', function ($ar) {
                              $ar->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress']);
                          });
                  });
            })
            ->whereDate('consultation_date', Carbon::today())
            ->with(['patient', 'ancillaryRequests'])
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

        $handledBase = Consultation::where('doctor_id', $user->id)
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

        return view('doctor.dashboard', compact('user', 'queue', 'handledPatients', 'handledCount', 'awaitingLabs'));
    }

    /**
     * Dedicated page for tracking patients waiting for Laboratory / Radiology results.
     */
    public function waitingResults()
    {
        $user = Auth::user();

        // Include consultations in awaiting_results, results_ready, or active consultations with pending/completed diagnostics
        $awaitingPatients = Consultation::where('doctor_id', $user->id)
            ->where(function ($q) {
                $q->whereIn('status', ['awaiting_results', 'results_ready'])
                  ->orWhere(function ($sub) {
                      $sub->whereNotIn('status', ['completed', 'done'])
                          ->whereHas('ancillaryRequests', function ($ar) {
                              $ar->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress', 'Done']);
                          });
                  });
            })
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

        // Check if patient has any unfinished diagnostic tests
        $hasActiveUnfinished = $consultation->ancillaryRequests()
            ->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress'])
            ->exists();

        // Only switch to 'active' if queued, or results are ready to resume,
        // or there are no pending diagnostic tests currently in progress at the lab/radiology
        if ($consultation->status === 'queued' || $consultation->status === 'results_ready' || ($consultation->status === 'awaiting_results' && ! $hasActiveUnfinished)) {
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
        } elseif ($hasActiveUnfinished && $consultation->status !== 'awaiting_results') {
            // Keep patient in awaiting_results state while tests are unfinished
            $consultation->status = 'awaiting_results';
            $consultation->save();
        }

        $patient = $consultation->patient;
        $consultation->load(['preTriage', 'ancillaryRequests.technician']);

        $pastConsultations = \App\Models\Consultation::where('patient_id', $patient->patient_id)
            ->where('id', '!=', $consultation->id)
            ->whereIn('status', ['completed', 'done'])
            ->with(['doctor', 'nurse', 'preTriage', 'ancillaryRequests.technician'])
            ->orderBy('created_at', 'desc')
            ->get();

        // LOG ACCESS: Accountability rule for medical records
        \App\Models\AuditLog::record("Accessed Patient Medical Folder: {$patient->patient_id}", $patient);

        // Check department statuses
        $isPharmacyOnline = \App\Models\User::where('role', 'pharmacy')->present()->exists();
        $isLabOnline = \App\Models\User::where('role', 'laboratory')->present()->exists();
        $isRadOnline = \App\Models\User::where('role', 'radiology')->present()->exists();

        return view('doctor.consultation', compact('consultation', 'patient', 'pastConsultations', 'isPharmacyOnline', 'isLabOnline', 'isRadOnline'));
    }

public function completeConsultation(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->doctor_id !== $user->id) {
            abort(403);
        }

        if (in_array($consultation->status, ['completed', 'done', 'cancelled'])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This consultation has already been completed.',
                ], 422);
            }

            return redirect()->route('doctor.dashboard')->with('warning', 'This consultation has already been completed.');
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
            'consultation_end_time' => now(),
            'status' => 'completed',
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

    /**
     * Record an official post-consultation clinical addendum.
     */
    public function storeAddendum(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->doctor_id && $consultation->doctor_id !== $user->id) {
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

    public function showPatient(\App\Models\Patient $patient)
    {
        $user = Auth::user();

        // Clinicians (doctors and clinical nurses) can view patient historical records for care continuity
        if (! in_array($user->role, ['regular_doctor', 'pedia_doctor', 'clinical_nurse', 'admin', 'super_admin'])) {
            return redirect()->route('doctor.dashboard')->with('error', 'Unauthorized access.');
        }

        // LOG ACCESS: Accountability audit trail for medical records
        \App\Models\AuditLog::record("Accessed Historical Patient Medical Folder: {$patient->patient_id}", $patient, [
            'viewed_by' => $user->id,
            'role' => $user->role,
            'purpose' => 'Clinical Chart Review',
        ]);

        $patient->load(['consultations' => function ($query) {
            $query->orderBy('created_at', 'asc'); // Ascending for chronological chart
        }, 'consultations.doctor', 'consultations.nurse', 'consultations.ancillaryRequests.technician']);

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
        }, 'consultations.doctor', 'consultations.nurse', 'consultations.preTriage', 'consultations.ancillaryRequests.technician']);

        return view('doctor.patients.show', [
            'patient' => $patient,
            'vitalsChartData' => $vitalsData,
        ]);
    }

    public function storeAncillaryRequest(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->doctor_id && $consultation->doctor_id !== $user->id) {
            abort(403);
        }
        if (! $consultation->doctor_id) {
            $consultation->doctor_id = $user->id;
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

        // Move patient to "Waiting for Results" status
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
        if ($consultation->doctor_id && $consultation->doctor_id !== $user->id) {
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

        \App\Models\AuditLog::record('Diagnostic Test Re-ordered / Repeated', $repeatRequest, [
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
        if ($consultation->doctor_id && $consultation->doctor_id !== $user->id) {
            abort(403);
        }

        if ($ancillary->status === 'Done') {
            return back()->with('error', 'Cannot cancel an already completed diagnostic test.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:255',
        ]);

        $reason = $validated['cancellation_reason'] ?? 'Cancelled by physician';

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

        \App\Models\AuditLog::record('Diagnostic Request Cancelled by Doctor', $ancillary, ['reason' => $reason]);
        broadcast(new \App\Events\QueueUpdated('Diagnostic request cancelled', strtolower($ancillary->type)));

        return back()->with('success', "{$ancillary->test_name} request has been cancelled.");
    }

    /**
     * Cancel a prescription by the prescriber (doctor).
     */
    public function cancelByPrescriber(Request $request, Consultation $consultation)
    {
        $user = Auth::user();

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

    /**
     * Cancel an active or queued consultation (e.g. Patient Walked Out, Refused, or Referred).
     */
    public function cancelConsultation(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->doctor_id && $consultation->doctor_id !== $user->id) {
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
                'doctor_id' => $user->id,
            ]);
        });

        broadcast(new \App\Events\QueueUpdated('Consultation cancelled / patient walkout', 'general'));

        return redirect()->route('doctor.dashboard')->with('success', 'Consultation marked as cancelled / walked out.');
    }
}

