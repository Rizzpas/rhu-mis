<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Consultation;
use App\Models\User;
use Carbon\Carbon;

class NurseController extends Controller
{
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
        
        $regular = $waiting->filter(function($c) { return !in_array($c->classification, ['Senior Citizen', 'PWD']); })->values();
        $priority = $waiting->filter(function($c) { return in_array($c->classification, ['Senior Citizen', 'PWD']); })->values();

        $queue = collect();
        foreach($active as $a) $queue->push($a);

        $regIdx = 0;
        $prioIdx = 0;
        while ($regIdx < $regular->count() || $prioIdx < $priority->count()) {
            if ($prioIdx < $priority->count()) $queue->push($priority[$prioIdx++]);
            if ($prioIdx < $priority->count()) $queue->push($priority[$prioIdx++]);
            if ($regIdx < $regular->count()) $queue->push($regular[$regIdx++]);
        }
            
        $doctors = User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->get();

        $handledPatients = Consultation::where('nurse_id', $user->id)
            ->whereIn('status', ['completed', 'done', 'cancelled'])
            ->whereDate('consultation_end_time', today())
            ->with('patient')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->unique('patient_id')
            ->take(20);

        return view('nurse.dashboard', compact('user', 'queue', 'doctors', 'awaitingLabs', 'handledPatients'));
    }
    
    public function forwardToDoctor(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id'
        ]);
        
        $consultation->update([
            'doctor_id' => $validated['doctor_id'],
            'nurse_id' => null, // remove from nurse queue
            'status' => 'queued', // Reset status so doctor sees them as waiting
            'consultation_start_time' => null, // Clear the start time
            // 'created_at' => now(), // Uncomment this if you want them to go to the BACK of the line
        ]);
        
        return back()->with('success', 'Patient forwarded to the scheduled doctor successfully.');
    }



    public function startConsultation(Consultation $consultation)
    {
        $user = Auth::user();
        
        if ($consultation->nurse_id && $consultation->nurse_id !== $user->id) {
            return redirect()->route('nurse.dashboard')->with('error', 'Unauthorized access.');
        }

        if (!$consultation->nurse_id) {
            $consultation->nurse_id = $user->id;
        }

        if (in_array($consultation->status, ['queued', 'awaiting_results'])) {
            $consultation->status = 'active';
            if (!$consultation->consultation_start_time) {
                $consultation->consultation_start_time = now();
            }
            $consultation->save();
        }

        $patient = $consultation->patient;
        $consultation->load('preTriage');
        
        $pastConsultations = \App\Models\Consultation::where('patient_id', $patient->id)
            ->where('id', '!=', $consultation->id)
            ->whereIn('status', ['completed', 'done'])
            ->with('preTriage')
            ->orderBy('created_at', 'desc')
            ->get();

        // LOG ACCESS: Accountability rule for medical records
        \App\Models\AuditLog::record("Accessed Patient Medical Folder: {$patient->patient_id}", $patient);
        
        return view('nurse.consultation', compact('consultation', 'patient', 'pastConsultations'));
    }

    public function completeConsultation(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->nurse_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'diagnosis' => 'required|string',
            'prescription' => 'nullable|string',
            'medical_notes' => 'nullable|string',
            'is_followup_needed' => 'nullable|boolean',
            'followup_date' => 'nullable|required_if:is_followup_needed,1|date|after_or_equal:today',
            'followup_reason' => 'nullable|required_if:is_followup_needed,1|string|max:255',
        ]);

        $consultation->update([
            'diagnosis' => $validated['diagnosis'],
            'prescription' => $validated['prescription'],
            'medical_notes' => $validated['medical_notes'],
            'is_followup_needed' => $request->has('is_followup_needed'),
            'followup_date' => $request->has('is_followup_needed') ? $validated['followup_date'] : null,
            'followup_reason' => $request->has('is_followup_needed') ? $validated['followup_reason'] : null,
            'followup_doctor_id' => $request->has('is_followup_needed') ? $user->id : null,
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

        // ── Record Medical Case (Persistent Case Record) ──────────────────
        \App\Models\MedicalCase::create([
            'case_number'     => 'CASE-N' . now()->format('Ymd') . '-' . str_pad($consultation->id, 5, '0', STR_PAD_LEFT),
            'patient_id'      => $consultation->patient_id,
            'consultation_id' => $consultation->id,
            'pre_triage_id'   => $consultation->pre_triage_id,
            'diagnosis'       => $validated['diagnosis'],
            'prescription'    => $validated['prescription'],
            'vitals_snapshot' => [
                'bp'   => $consultation->preTriage->blood_pressure ?? null,
                'temp' => $consultation->preTriage->temperature ?? null,
                'wt'   => $consultation->preTriage->weight ?? null,
                'ht'   => $consultation->preTriage->height ?? null,
                'hr'   => $consultation->preTriage->heart_rate ?? null,
                'rr'   => $consultation->preTriage->respiratory_rate ?? null,
                'pr'   => $consultation->preTriage->pulse_rate ?? null,
                'spo2' => $consultation->preTriage->spo2 ?? ($consultation->preTriage->oxygen_saturation ?? null),
            ],
            'closed_at'       => now(),
        ]);

        // ── Audit Trail ──────────────────────────────────────────────────
        \App\Models\AuditLog::record(
            $request->has('is_followup_needed') ? 'Completed Consultation (Nurse - Follow-up Scheduled)' : 'Completed Consultation (Nurse)',
            $consultation,
            [
                'diagnosis' => $validated['diagnosis'],
                'is_followup' => $request->has('is_followup_needed'),
                'followup_date' => $request->has('is_followup_needed') ? $validated['followup_date'] : null
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
            if ($request->has('is_followup_needed')) {
                $patient->update([
                    'next_followup_date' => $validated['followup_date'],
                    'previous_doctor_id' => $user->id,
                ]);
            } else {
                $patient->update([
                    'next_followup_date' => null,
                    'previous_doctor_id' => null,
                ]);
            }
        }

        return redirect()->route('nurse.dashboard')->with('success', 'Consultation completed for ' . $consultation->patient->first_name);
    }

    public function storeAncillaryRequest(Request $request, Consultation $consultation)
    {
        $user = Auth::user();
        if ($consultation->nurse_id !== $user->id) {
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
            'remarks' => $validated['remarks'],
            'status' => 'Pending',
        ]);

        $consultation->update(['status' => 'awaiting_results']);

        return redirect()->route('nurse.dashboard')->with('success', $validated['type'] . ' request sent to Ancillary Queue. Consultation paused.');
    }
}
