<?php

namespace App\Http\Controllers;

use App\Models\AncillaryRequest;
use App\Models\AuditLog;
use App\Rules\SecureImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $type = $user->role === 'radiology' ? 'Radiology' : 'Laboratory';

        // ── Tab 1: ACTIVE — Pending, Specimen Collected, and In Progress ──
        $allActiveRequests = AncillaryRequest::with(['consultation.patient', 'consultation.doctor', 'collector'])
            ->where('type', $type)
            ->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress'])
            ->whereNull('archived_at')
            ->orderBy('id', 'asc')
            ->get();

        // Split into same-day active patients vs backlog
        $sameDayRequests = $allActiveRequests->filter(function ($req) {
            return $req->consultation && $req->consultation->consultation_date && $req->consultation->consultation_date->isToday();
        })->values();

        $backlogRequests = $allActiveRequests->filter(function ($req) {
            return !$req->consultation || !$req->consultation->consultation_date || !$req->consultation->consultation_date->isToday();
        })->values();

        $pendingRequests = $sameDayRequests->merge($backlogRequests);

        // ── Tab 2: FINISHED — Keep records for at least 6 months ──────────
        $sixMonthsAgo = now()->subMonths(6)->startOfDay();
        $completedQuery = AncillaryRequest::with(['consultation.patient', 'consultation.doctor', 'technician', 'amender'])
            ->where('type', $type)
            ->where('status', 'Done')
            ->where(function ($q) use ($sixMonthsAgo) {
                $q->where('completed_at', '>=', $sixMonthsAgo)
                  ->orWhere(function ($sub) use ($sixMonthsAgo) {
                      $sub->whereNull('completed_at')
                          ->where('updated_at', '>=', $sixMonthsAgo);
                  });
            });

        if ($search = $request->input('search_finished')) {
            $completedQuery->where(function ($q) use ($search) {
                $q->where('test_name', 'like', "%{$search}%")
                  ->orWhereHas('consultation.patient', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('patient_id', 'like', "%{$search}%");
                  });
            });
        }

        $completedRequests = $completedQuery->orderBy('completed_at', 'desc')->paginate(15, ['*'], 'completed_page')->withQueryString();

        // ── Tab 3: ARCHIVE — No-shows, Cancelled, and Rejected ────────────
        // Visible for at least 6 months without premature 7-day cutoff
        $archivedQuery = AncillaryRequest::with(['consultation.patient', 'consultation.doctor', 'rejector', 'canceller'])
            ->where('type', $type)
            ->where(function ($q) {
                // Explicitly archived
                $q->whereNotNull('archived_at');
                // OR Cancelled or Rejected
                $q->orWhereIn('status', ['Cancelled', 'Rejected']);
                // OR: pending requests from past days (auto no-show)
                $q->orWhere(function ($sub) {
                    $sub->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress'])
                        ->whereNull('archived_at')
                        ->whereHas('consultation', function ($cq) {
                            $cq->whereDate('consultation_date', '<', now()->toDateString());
                        });
                });
            })
            ->where(function ($q) use ($sixMonthsAgo) {
                $q->where('created_at', '>=', $sixMonthsAgo)
                  ->orWhere('archived_at', '>=', $sixMonthsAgo)
                  ->orWhere('cancelled_at', '>=', $sixMonthsAgo)
                  ->orWhere('rejected_at', '>=', $sixMonthsAgo);
            });

        if ($searchArchive = $request->input('search_archive')) {
            $archivedQuery->where(function ($q) use ($searchArchive) {
                $q->where('test_name', 'like', "%{$searchArchive}%")
                  ->orWhereHas('consultation.patient', function ($pq) use ($searchArchive) {
                      $pq->where('first_name', 'like', "%{$searchArchive}%")
                         ->orWhere('last_name', 'like', "%{$searchArchive}%")
                         ->orWhere('patient_id', 'like', "%{$searchArchive}%");
                  });
            });
        }

        $archivedRequests = $archivedQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'archived_page')->withQueryString();

        return response()
            ->view('lab.dashboard', compact(
                'user', 'pendingRequests', 'completedRequests', 'archivedRequests',
                'type', 'sameDayRequests', 'backlogRequests'
            ))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Step 3: Specimen collection.
     */
    public function collectSpecimen(Request $request, AncillaryRequest $ancillary)
    {
        if ($ancillary->status !== 'Pending') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => "Cannot collect specimen for request in '{$ancillary->status}' status."], 422);
            }
            return back()->with('error', "Cannot collect specimen for request in '{$ancillary->status}' status.");
        }

        $ancillary->update([
            'status' => 'Specimen Collected',
            'specimen_collected_at' => now(),
            'specimen_collected_by' => Auth::id(),
        ]);

        AuditLog::record('Specimen Collected', $ancillary);
        broadcast(new \App\Events\QueueUpdated('Specimen collected', strtolower($ancillary->type)));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Specimen collected for {$ancillary->test_name}. Patient ready for processing.",
                'status' => $ancillary->status,
            ]);
        }

        return back()->with('success', "Specimen collected for {$ancillary->test_name}. Patient ready for processing.");
    }

    /**
     * Step 4: Processing / In Progress.
     */
    public function startProcessing(Request $request, AncillaryRequest $ancillary)
    {
        if (! in_array($ancillary->status, ['Pending', 'Specimen Collected'])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => "Cannot start processing for request in '{$ancillary->status}' status."], 422);
            }
            return back()->with('error', "Cannot start processing for request in '{$ancillary->status}' status.");
        }

        $ancillary->update([
            'status' => 'In Progress',
            'processing_started_at' => now(),
        ]);

        AuditLog::record('Diagnostic Processing Started', $ancillary);
        broadcast(new \App\Events\QueueUpdated('Processing started', strtolower($ancillary->type)));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$ancillary->test_name} is now in progress.",
                'status' => $ancillary->status,
            ]);
        }

        return back()->with('success', "{$ancillary->test_name} is now in progress.");
    }

    /**
     * Step 5: Complete request and submit diagnostic results.
     */
    public function completeRequest(Request $request, AncillaryRequest $ancillary)
    {
        $request->validate([
            'results' => 'required|array',
            'result_file' => ['nullable', 'file', 'max:10240', new SecureImage],
            'is_critical' => 'nullable|boolean',
            'critical_remarks' => 'nullable|string|max:500',
        ]);

        $isCritical = $request->boolean('is_critical')
            || ! empty($request->results['is_critical'])
            || (! empty($request->critical_alert) && $request->critical_alert);

        $criticalRemarks = $request->input('critical_remarks') ?? ($request->results['critical_remarks'] ?? null);

        $resultsData = $request->results;
        if ($isCritical) {
            $resultsData['_is_critical'] = true;
            if ($criticalRemarks) {
                $resultsData['_critical_remarks'] = $criticalRemarks;
            }
        }

        $data = [
            'status' => 'Done',
            'result_data' => $resultsData,
            'completed_by' => Auth::id(),
            'completed_at' => now(),
        ];

        if ($isCritical) {
            $alertPrefix = '[CRITICAL VALUE ALERT]' . ($criticalRemarks ? " - {$criticalRemarks}" : '');
            $data['remarks'] = empty($ancillary->remarks) ? $alertPrefix : $ancillary->remarks . ' | ' . $alertPrefix;
        }

        if ($request->hasFile('result_file')) {
            $file = $request->file('result_file');
            $filename = 'ancillary_'.$ancillary->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('ancillary_results', $filename, 'public');
            $data['result_file_path'] = $path;
        }

        $ancillary->update($data);

        AuditLog::record(
            $isCritical ? 'CRITICAL Diagnostic Results Completed - Alert Flagged' : 'Diagnostic Results Completed',
            $ancillary,
            ['is_critical' => $isCritical, 'critical_remarks' => $criticalRemarks]
        );

        if ($isCritical) {
            $consultation = $ancillary->consultation;
            $clinician = $consultation ? ($consultation->doctor ?? $consultation->nurse) : null;
            if ($clinician) {
                $clinician->notify(new \App\Notifications\CriticalLabResultNotification($ancillary, $criticalRemarks ?? 'Immediate clinical review recommended.'));
            }
        }

        $this->syncConsultationReadiness($ancillary);

        broadcast(new \App\Events\QueueUpdated('Diagnostic results submitted', strtolower($ancillary->type)));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $isCritical
                    ? 'CRITICAL ALERT: Diagnostic results submitted and attending physician notified of panic values.'
                    : 'Diagnostic results submitted successfully. Attending physician has been notified.',
                'status' => $ancillary->status,
                'is_critical' => $isCritical,
            ]);
        }

        return back()->with(
            $isCritical ? 'warning' : 'success',
            $isCritical
                ? 'CRITICAL ALERT: Diagnostic results submitted and attending physician notified of panic values.'
                : 'Diagnostic results submitted successfully. Attending physician has been notified.'
        );
    }

    /**
     * Correct / Amend an already completed result.
     */
    public function amendResult(Request $request, AncillaryRequest $ancillary)
    {
        if ($ancillary->status !== 'Done') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Only completed diagnostic results can be amended.'], 422);
            }
            return back()->with('error', 'Only completed diagnostic results can be amended.');
        }

        $request->validate([
            'results' => 'required|array',
            'amendment_reason' => 'required|string|max:500',
            'result_file' => ['nullable', 'file', 'max:10240', new SecureImage],
        ]);

        $updateData = [
            'previous_result_data' => $ancillary->result_data,
            'result_data' => $request->results,
            'is_amended' => true,
            'amendment_reason' => $request->amendment_reason,
            'amended_by' => Auth::id(),
            'amended_at' => now(),
        ];

        if ($request->hasFile('result_file')) {
            $file = $request->file('result_file');
            $filename = 'ancillary_'.$ancillary->id.'_amended_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('ancillary_results', $filename, 'public');
            $updateData['result_file_path'] = $path;
        }

        $ancillary->update($updateData);

        AuditLog::record('Diagnostic Result Amended', $ancillary, ['reason' => $request->amendment_reason]);
        broadcast(new \App\Events\QueueUpdated('Diagnostic result amended', strtolower($ancillary->type)));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Diagnostic result amended successfully. The audit trail has recorded previous values.',
                'status' => $ancillary->status,
            ]);
        }

        return back()->with('success', 'Diagnostic result amended successfully. The audit trail has recorded previous values.');
    }

    /**
     * Reject a laboratory sample or request (e.g. Hemolyzed, QNS, Clotted, Broken).
     */
    public function rejectRequest(Request $request, AncillaryRequest $ancillary)
    {
        if (! in_array($ancillary->status, ['Pending', 'Specimen Collected', 'In Progress'])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => "Cannot reject request in '{$ancillary->status}' status."], 422);
            }
            return back()->with('error', "Cannot reject request in '{$ancillary->status}' status.");
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $ancillary->update([
            'status' => 'Rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
        ]);

        AuditLog::record('Diagnostic Request Rejected', $ancillary, ['reason' => $validated['rejection_reason']]);

        $this->syncConsultationReadiness($ancillary);

        broadcast(new \App\Events\QueueUpdated('Diagnostic sample rejected', strtolower($ancillary->type)));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Request marked as Rejected ({$validated['rejection_reason']}). Physician can review or reorder.",
                'status' => $ancillary->status,
            ]);
        }

        return back()->with('success', "Request marked as Rejected ({$validated['rejection_reason']}). Physician can review or reorder.");
    }

    /**
     * Cancel an ancillary request.
     */
    public function cancelRequest(Request $request, AncillaryRequest $ancillary)
    {
        if ($ancillary->status === 'Done') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot cancel a test that is already completed. Use amendment instead.'], 422);
            }
            return back()->with('error', 'Cannot cancel a test that is already completed. Use amendment instead.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:255',
        ]);

        $reason = $validated['cancellation_reason'] ?? 'Cancelled by Laboratory/Radiology staff';

        $ancillary->update([
            'status' => 'Cancelled',
            'cancellation_reason' => $reason,
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
        ]);

        AuditLog::record('Diagnostic Request Cancelled', $ancillary, ['reason' => $reason]);

        $this->syncConsultationReadiness($ancillary);

        broadcast(new \App\Events\QueueUpdated('Diagnostic request cancelled', strtolower($ancillary->type)));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Diagnostic request has been cancelled.',
                'status' => $ancillary->status,
            ]);
        }

        return back()->with('success', 'Diagnostic request has been cancelled.');
    }

    /**
     * Manually archive a pending request (mark as no-show).
     */
    public function archiveRequest(Request $request, AncillaryRequest $ancillary)
    {
        $ancillary->update([
            'archived_at' => now(),
            'archived_reason' => 'manual',
        ]);

        AuditLog::record('Diagnostic Request Archived (No-Show)', $ancillary);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Request has been archived as no-show.',
                'status' => $ancillary->status,
            ]);
        }

        return back()->with('success', 'Request has been archived as no-show.');
    }

    /**
     * Restore an archived request back to pending queue.
     */
    public function restoreRequest(Request $request, AncillaryRequest $ancillary)
    {
        $ancillary->update([
            'archived_at' => null,
            'archived_reason' => null,
            'status' => 'Pending',
        ]);

        AuditLog::record('Diagnostic Request Restored', $ancillary);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Request has been restored to the pending queue.',
                'status' => $ancillary->status,
            ]);
        }

        return back()->with('success', 'Request has been restored to the pending queue.');
    }

    /**
     * Synchronize the patient's consultation status when diagnostic requests are finished, rejected, or cancelled.
     */
    protected function syncConsultationReadiness(AncillaryRequest $ancillary): void
    {
        $consultation = $ancillary->consultation;
        if (! $consultation || in_array($consultation->status, ['completed', 'done', 'cancelled'])) {
            return;
        }

        // Check if there are any remaining active uncompleted requests
        $hasActiveUnfinished = $consultation->ancillaryRequests()
            ->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress'])
            ->where('id', '!=', $ancillary->id)
            ->exists();

        if (! $hasActiveUnfinished) {
            $consultation->update(['status' => 'results_ready']);
        } else {
            $consultation->update(['status' => 'awaiting_results']);
        }
    }

    /**
     * Display printable official diagnostic report for laboratory/radiology personnel.
     */
    public function printReport(Request $request, AncillaryRequest $ancillary)
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
