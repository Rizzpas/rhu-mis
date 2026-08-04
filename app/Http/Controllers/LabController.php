<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $type = $user->role === 'radiology' ? 'Radiology' : 'Laboratory';

        $allPendingRequests = \App\Models\AncillaryRequest::with(['consultation.patient', 'consultation.doctor'])
            ->where('type', $type)
            ->where('status', 'Pending')
            ->orderBy('id', 'asc')
            ->get();

        // Split into same-day active patients (consultation today) vs backlog (older requests)
        $sameDayRequests = $allPendingRequests->filter(function ($req) {
            return $req->consultation && $req->consultation->consultation_date && $req->consultation->consultation_date->isToday();
        })->values();

        $backlogRequests = $allPendingRequests->filter(function ($req) {
            return !$req->consultation || !$req->consultation->consultation_date || !$req->consultation->consultation_date->isToday();
        })->values();

        // Merge: same-day first, then backlog
        $pendingRequests = $sameDayRequests->merge($backlogRequests);

        // 1-Week History of Completed Requests
        $completedRequests = \App\Models\AncillaryRequest::with(['consultation.patient', 'consultation.doctor', 'technician'])
            ->where('type', $type)
            ->where('status', 'Done')
            ->where('completed_at', '>=', now()->subDays(7))
            ->orderBy('completed_at', 'desc')
            ->get();

        return view('lab.dashboard', compact('user', 'pendingRequests', 'completedRequests', 'type', 'sameDayRequests', 'backlogRequests'));
    }

    public function completeRequest(Request $request, \App\Models\AncillaryRequest $ancillary)
    {
        $request->validate([
            'results' => 'required|array',
            'result_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,dicom|max:10240',
        ]);

        $data = [
            'status' => 'Done',
            'result_data' => $request->results,
            'completed_by' => Auth::id(),
            'completed_at' => now(),
        ];

        if ($request->hasFile('result_file')) {
            $file = $request->file('result_file');
            $filename = 'ancillary_'.$ancillary->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('ancillary_results', $filename, 'public');
            $data['result_file_path'] = $path;
        }

        $ancillary->update($data);

        // Update consultation status to 'results_ready' if patient is currently waiting in clinic today
        $consultation = $ancillary->consultation;
        if ($consultation && $consultation->status === 'awaiting_results') {
            $hasPending = $consultation->ancillaryRequests()
                ->where('status', 'Pending')
                ->where('id', '!=', $ancillary->id)
                ->exists();

            if (! $hasPending) {
                $consultation->update(['status' => 'results_ready']);
            }
        }

        broadcast(new \App\Events\QueueUpdated('Diagnostic results submitted', 'laboratory'));

        return back()->with('success', 'Diagnostic results submitted successfully. The attending physician has been notified.');
    }
}
