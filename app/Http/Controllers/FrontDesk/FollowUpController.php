<?php

namespace App\Http\Controllers\FrontDesk;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    /**
     * Display a listing of scheduled patient follow-ups.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $search = trim($request->query('search', ''));
        $doctorId = $request->query('doctor_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $perPage = (int) $request->query('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        // Summary KPI counts (unfiltered by current search/tab for banner integrity)
        $overdueCount = Consultation::overdueFollowups()->count();
        $dueTodayCount = Consultation::dueTodayFollowups()->count();
        $upcoming7Count = Consultation::upcomingFollowups(7)->count();
        $fulfilledCount = Consultation::followups()->whereNotNull('followup_completed_at')->count();
        $totalFollowupsCount = Consultation::followups()->count();

        // Main Query
        $query = Consultation::followups()
            ->with(['patient', 'doctor', 'followupDoctor', 'preTriage']);

        // Tab Filtering
        switch ($tab) {
            case 'overdue':
                $query->whereNull('followup_completed_at')
                      ->whereDate('followup_date', '<', today());
                break;
            case 'due_today':
                $query->whereNull('followup_completed_at')
                      ->whereDate('followup_date', today());
                break;
            case 'upcoming_7':
                $query->whereNull('followup_completed_at')
                      ->whereDate('followup_date', '>', today())
                      ->whereDate('followup_date', '<=', today()->addDays(7));
                break;
            case 'upcoming_all':
                $query->whereNull('followup_completed_at')
                      ->whereDate('followup_date', '>', today());
                break;
            case 'fulfilled':
                $query->whereNotNull('followup_completed_at');
                break;
            case 'all':
            default:
                // No status restriction
                break;
        }

        // Doctor Filter
        if ($doctorId && is_numeric($doctorId)) {
            $query->where(function ($q) use ($doctorId) {
                $q->where('followup_doctor_id', $doctorId)
                  ->orWhere(function ($q2) use ($doctorId) {
                      $q2->whereNull('followup_doctor_id')
                         ->where('doctor_id', $doctorId);
                  });
            });
        }

        // Date Range Filter
        if ($dateFrom) {
            $query->whereDate('followup_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('followup_date', '<=', $dateTo);
        }

        // Search Filter
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('followup_reason', 'like', "%{$search}%")
                  ->orWhere('diagnosis', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('middle_name', 'like', "%{$search}%")
                         ->orWhere('patient_id', 'like', "%{$search}%")
                         ->orWhere('philhealth_id', 'like', "%{$search}%")
                         ->orWhere('contact_number', 'like', "%{$search}%");
                  });
            });
        }

        // Ordering
        if ($tab === 'overdue') {
            $query->orderBy('followup_date', 'asc'); // Oldest overdue first
        } elseif ($tab === 'due_today') {
            $query->orderBy('id', 'desc');
        } elseif ($tab === 'fulfilled') {
            $query->orderBy('followup_completed_at', 'desc');
        } else {
            // Unfulfilled first (sorted by date), then fulfilled
            $query->orderByRaw('followup_completed_at IS NOT NULL ASC')
                  ->orderBy('followup_date', 'asc');
        }

        $followups = $query->paginate($perPage)->withQueryString();

        // Doctors list for filter dropdown
        $doctors = User::whereIn('role', ['regular_doctor', 'pedia_doctor'])
            ->orderBy('name')
            ->get();

        AuditLog::record('Viewed Follow-Up Tracker (Front Desk)');

        return view('frontdesk.followups.index', compact(
            'followups',
            'overdueCount',
            'dueTodayCount',
            'upcoming7Count',
            'fulfilledCount',
            'totalFollowupsCount',
            'doctors',
            'tab'
        ));
    }

    /**
     * Reschedule the follow-up return date for a consultation.
     */
    public function reschedule(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'followup_date' => 'required|date|after_or_equal:today',
            'followup_reason' => 'nullable|string|max:255',
            'followup_doctor_id' => 'nullable|exists:users,id',
        ]);

        $oldDate = $consultation->followup_date ? $consultation->followup_date->format('M d, Y') : 'N/A';
        $newDate = \Carbon\Carbon::parse($validated['followup_date'])->format('M d, Y');

        $consultation->update([
            'followup_date' => $validated['followup_date'],
            'followup_reason' => $validated['followup_reason'] ?? $consultation->followup_reason,
            'followup_doctor_id' => $validated['followup_doctor_id'] ?? $consultation->followup_doctor_id,
            'followup_completed_at' => null, // Rescheduling clears completed status if previously fulfilled
        ]);

        // Sync with Patient record
        $patient = $consultation->patient;
        if ($patient) {
            $patient->update([
                'next_followup_date' => $validated['followup_date'],
                'previous_doctor_id' => $consultation->followup_doctor_id ?? $consultation->doctor_id,
            ]);
        }

        AuditLog::record('Rescheduled Patient Follow-Up', $consultation, [
            'patient_id' => $consultation->patient_id,
            'old_date' => $oldDate,
            'new_date' => $newDate,
            'reason' => $consultation->followup_reason,
        ]);

        return back()->with('success', "Follow-up for {$patient->full_name} rescheduled to {$newDate}.");
    }

    /**
     * Manually mark a follow-up as fulfilled/resolved.
     */
    public function fulfill(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'resolution_notes' => 'nullable|string|max:500',
        ]);

        $consultation->update([
            'followup_completed_at' => now(),
            'followup_reason' => $validated['resolution_notes'] 
                ? ($consultation->followup_reason . ' [Resolved: ' . $validated['resolution_notes'] . ']')
                : $consultation->followup_reason,
        ]);

        // Update patient's next_followup_date: if they have another pending follow-up, use that; else null
        $patient = $consultation->patient;
        if ($patient) {
            $nextPending = Consultation::where('patient_id', $consultation->patient_id)
                ->where('id', '!=', $consultation->id)
                ->where('is_followup_needed', true)
                ->whereNull('followup_completed_at')
                ->orderBy('followup_date', 'asc')
                ->first();

            $patient->update([
                'next_followup_date' => $nextPending ? $nextPending->followup_date : null,
            ]);
        }

        AuditLog::record('Marked Follow-Up Fulfilled (Manual)', $consultation, [
            'patient_id' => $consultation->patient_id,
            'notes' => $validated['resolution_notes'] ?? null,
        ]);

        return back()->with('success', "Follow-up for {$patient->full_name} marked as completed.");
    }
}
