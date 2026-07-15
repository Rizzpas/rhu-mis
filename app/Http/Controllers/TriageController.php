<?php

namespace App\Http\Controllers;

use App\Models\PreTriage;
use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TriageController extends Controller
{
    /**
     * Search existing patients (for returning patient identification at Triage).
     */
    public function searchPatient(Request $request)
    {
        $q = trim($request->query('q', ''));
        $dob = $request->query('dob');
        if (strlen($q) < 2 && !$dob) return response()->json([]);

        $terms = explode(' ', $q);
        $query = Patient::query();
        foreach ($terms as $term) {
            $query->where(function ($sq) use ($term) {
                $sq->where('first_name', 'like', "%{$term}%")
                   ->orWhere('last_name', 'like', "%{$term}%")
                   ->orWhere('patient_id', 'like', "%{$term}%")
                   ->orWhere('philhealth_number', 'like', "%{$term}%");
            });
        }

        if ($dob) {
            $query->where('dob', $dob);
        }

        $patients = $query->take(8)->get();

        return response()->json($patients->map(function ($p) {
            $lastVisit = Consultation::where('patient_id', $p->patient_id)
                ->orderByDesc('consultation_date')
                ->first();

            return [
                'id'              => $p->patient_id,
                'full_name'       => trim($p->first_name . ' ' . ($p->middle_name ? $p->middle_name . ' ' : '') . $p->last_name),
                'dob'             => Carbon::parse($p->dob)->format('M d, Y'),
                'dob_raw'         => $p->dob,
                'age'             => Carbon::parse($p->dob)->age,
                'classification'  => $p->classification ?? 'Regular Adult',
                'contact'         => $p->contact_number,
                'philhealth'      => $p->philhealth_number,
                'house_no'        => $p->house_no,
                'street'          => $p->street,
                'building'        => $p->building,
                'barangay'        => $p->barangay,
                'city_province'   => $p->city_province,
                'last_visit'      => $lastVisit ? Carbon::parse($lastVisit->consultation_date)->format('M d, Y') : 'No previous visit',
                'allergies'       => $lastVisit->past_medical_history ?? $p->past_medical_history ?? null,
                'last_medicine'   => $lastVisit->medicine_taken ?? null,
                'last_symptoms'   => $lastVisit->symptoms ?? null,
                'last_diagnosis'  => $lastVisit->diagnosis ?? null,
            ];
        }));
    }

    /**
     * Show the Vitals Nurse dashboard – the pre-triage queue.
     */
    public function dashboard()
    {
        // Show today's waiting pre-triage entries only
        $waiting = PreTriage::where('status', 'waiting')
            ->whereDate('created_at', today())
            ->with('recorder')
            ->orderBy('created_at', 'asc')
            ->get();

        $claimed = PreTriage::whereIn('status', ['claimed', 'completed'])
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->get();

        $totalToday = PreTriage::whereDate('created_at', today())->count();
        $leftToday = PreTriage::where('status', 'cancelled')->whereDate('updated_at', today())->count();

        $cancelled = PreTriage::where('status', 'cancelled')
            ->whereDate('updated_at', today())
            ->orderBy('updated_at', 'desc')
            ->get();

        // Appointments that have arrived at Info Desk but not yet triaged
        // We filter out any that already have a PreTriage record today to avoid duplicates
        $triagedAppointmentIds = PreTriage::whereDate('created_at', today())
            ->whereNotNull('appointment_id')
            ->pluck('appointment_id');

        $arrivedAppointments = \App\Models\Appointment::where('status', 'arrived')
            ->whereNotIn('id', $triagedAppointmentIds)
            ->whereDate('preferred_date', today())
            ->orderBy('updated_at', 'asc')
            ->get();

        return view('triage.dashboard', compact('waiting', 'claimed', 'totalToday', 'leftToday', 'arrivedAppointments', 'cancelled'));
    }

    /**
     * Get real-time stats for the dashboard.
     */
    public function getStats()
    {
        $waitingCount = PreTriage::where('status', 'waiting')->whereDate('created_at', today())->count();
        $claimedCount = PreTriage::whereIn('status', ['claimed', 'completed'])->whereDate('created_at', today())->count();
        $totalTodayCount = PreTriage::whereDate('created_at', today())->count();
        $leftTodayCount = PreTriage::where('status', 'cancelled')->whereDate('updated_at', today())->count();

        return response()->json([
            'waiting' => $waitingCount,
            'processed' => $claimedCount,
            'totalToday' => $totalTodayCount,
            'leftToday' => $leftTodayCount
        ]);
    }

    /**
     * Store a new pre-triage vitals record.
     */
    public function store(Request $request)
    {
        // ── 1. Normalize Names to Title Case ─────────────────────────────────
        $nameFields = ['patient_name', 'first_name', 'middle_name', 'last_name', 'suffix'];
        foreach ($nameFields as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $request->merge([$field => ucwords(strtolower($request->input($field)))]);
            }
        }

        $validated = $request->validate([
            'patient_id'          => 'nullable|exists:patients,patient_id',
            'appointment_id'      => 'nullable|exists:appointments,id',
            'patient_name'        => 'nullable|string|max:255',
            'first_name'          => 'nullable|string|max:255',
            'last_name'           => 'nullable|string|max:255',
            'middle_name'         => 'nullable|string|max:255',
            'suffix'              => 'nullable|string|max:20',
            'classification'      => 'required|in:Adult,Senior,Pediatric,PWD',
            'dob'                 => 'nullable|date|before_or_equal:today',
            'chief_complaint'     => 'nullable|string|max:500',
            'symptoms'            => 'required|string',
            'blood_pressure'      => [
                'nullable', 
                'string', 
                'regex:/^\d{2,3}\/\d{2,3}$/',
                function ($attribute, $value, $fail) {
                    if (strpos($value, '/') !== false) {
                        [$sys, $dia] = explode('/', $value);
                        if ($sys > 260 || $sys < 60) $fail("Systolic BP ($sys) is physically unlikely. Please verify.");
                        if ($dia > 160 || $dia < 30) $fail("Diastolic BP ($dia) is physically unlikely. Please verify.");
                        if ($sys <= $dia) $fail("Systolic pressure must be higher than Diastolic pressure.");
                    }
                }
            ],
            'temperature'         => 'nullable|numeric|between:34.0,43.0',
            'weight'              => 'nullable|numeric|between:0.5,400',
            'height'              => 'nullable|numeric|between:30,250',
            'heart_rate'          => 'nullable|integer|between:30,220',
            'respiratory_rate'    => 'nullable|integer|between:8,80',
            'pulse_rate'          => 'nullable|integer|between:30,220',
            'oxygen_saturation'   => 'nullable|integer|between:50,100',
            'spo2'                => 'nullable|string|regex:/^\d{2,3}$/',
            'past_medical_history'=> 'required|string',
            'medicine_taken'      => 'required|string',
            'known_allergies'     => 'required|string',
            'vitals_started_at'   => 'nullable|integer',
        ], [
            'blood_pressure.regex' => 'Blood pressure must be in "Systolic/Diastolic" format (e.g., 120/80).',
            'temperature.between' => 'Temperature must be between 34.0°C and 43.0°C.',
            'heart_rate.between' => 'Heart Rate seems physically unlikely (30-220 bpm range).',
            'spo2.regex' => 'SpO2 must be a number (e.g., 98).',
            'oxygen_saturation.between' => 'Oxygen saturation must be between 50% and 100%.',
        ]);

        // Reconstruct full name if split names were provided
        if (empty($validated['patient_name']) && !empty($validated['first_name']) && !empty($validated['last_name'])) {
            $validated['patient_name'] = trim($validated['first_name'] . ' ' . 
                (!empty($validated['middle_name']) ? $validated['middle_name'] . ' ' : '') . 
                $validated['last_name'] . 
                (!empty($validated['suffix']) ? ' ' . $validated['suffix'] : ''));
        }

        // Failsafe
        if (empty($validated['patient_name'])) {
            return response()->json(['success' => false, 'message' => 'Patient name is required.']);
        }

        // Check for duplicates in patients table if this is a "New Patient"
        if (empty($validated['patient_id']) && !empty($validated['first_name']) && !empty($validated['last_name']) && !empty($validated['dob'])) {
            $existing = Patient::where('first_name', trim($validated['first_name']))
                               ->where('last_name', trim($validated['last_name']))
                               ->where('dob', $validated['dob'])
                               ->first();
            if ($existing) {
                return response()->json([
                    'success' => false, 
                    'message' => 'A patient named ' . $validated['first_name'] . ' ' . $validated['last_name'] . ' with the same Date of Birth (' . \Carbon\Carbon::parse($validated['dob'])->format('M d, Y') . ') already exists in the system. Please use the search bar to select their existing record.'
                ]);
            }
        }

        // Check if an EXISTING patient is already in the triage queue for today
        if (!empty($validated['patient_id'])) {
            // 1. Check if they are waiting in PreTriage
            $waitingPreTriage = PreTriage::where('patient_id', $validated['patient_id'])
                ->where('status', 'waiting')
                ->whereDate('created_at', today())
                ->first();

            if ($waitingPreTriage) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'duplicate',
                    'message' => 'This patient is already waiting in the queue at the Information Desk. You cannot record vitals for them again.'
                ]);
            }
            
            // 2. Check if they have an active consultation today
            $activeConsultation = \App\Models\Consultation::where('patient_id', $validated['patient_id'])
                ->whereDate('consultation_date', today())
                ->whereNotIn('status', ['completed', 'cancelled', 'no_show', 'dispensed'])
                ->first();
                
            if ($activeConsultation) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'duplicate',
                    'message' => 'This patient is already queued or being processed for a consultation today. You cannot record vitals for them again.'
                ]);
            }
        }

        $validated['recorded_by'] = Auth::id();
        $validated['status']      = 'waiting';
        $validated['is_emergency'] = $request->boolean('is_emergency', false);

        // Enforce 5-Slot Pedia Walk-in Cap
        if ($validated['classification'] === 'Pediatric' && empty($validated['appointment_id']) && !$validated['is_emergency']) {
            $walkInCount = \App\Models\PreTriage::whereDate('created_at', today())
                ->where('classification', 'Pediatric')
                ->whereNull('appointment_id')
                ->count();
                
            if ($walkInCount >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Walk-in slots for Pediatrics are full for today (Maximum 5 slots). Check "Emergency Override" to bypass this limit.'
                ]);
            }
        }

        if ($request->has('encoding_duration_seconds')) {
            $validated['encoding_duration_seconds'] = $request->integer('encoding_duration_seconds');
        }

        // Normalize spo2 / oxygen_saturation so both columns are always populated
        if (!empty($validated['spo2']) && empty($validated['oxygen_saturation'])) {
            $validated['oxygen_saturation'] = intval($validated['spo2']);
        } elseif (!empty($validated['oxygen_saturation']) && empty($validated['spo2'])) {
            $validated['spo2'] = (string) $validated['oxygen_saturation'];
        }

        $preTriage = PreTriage::create($validated);

        // Update appointment status if linked
        if (!empty($validated['appointment_id'])) {
            \App\Models\Appointment::where('id', $validated['appointment_id'])->update(['status' => 'triaged']);
        }

        // ── Audit Logging ──────────────────────────────────────────────────────
        \App\Models\AuditLog::record('Vitals Recorded', $preTriage, [
            'patient_name' => $validated['patient_name'],
            'classification' => $validated['classification']
        ]);

        if ($request->wantsJson()) {
            // Load recorder relationship so the dashboard can display who submitted it
            $preTriage->load('recorder');
            return response()->json([
                'success' => true,
                'message' => 'Vitals recorded for ' . $validated['patient_name'],
                'preTriage' => $preTriage
            ]);
        }

        return back()->with('success', 'Vitals recorded for ' . $validated['patient_name'] . '. Patient may now proceed to the Information Desk.');
    }

    /**
     * Cancel / remove a pre-triage entry (e.g., patient left).
     */
    public function cancel(Request $request, PreTriage $preTriage)
    {
        $preTriage->update(['status' => 'cancelled']);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Patient entry cancelled and removed from queue.'
            ]);
        }
        return back()->with('success', 'Patient entry removed from queue.');
    }

    /**
     * Restore a cancelled pre-triage entry to the end of the queue.
     */
    public function restore(Request $request, PreTriage $preTriage)
    {
        $preTriage->update([
            'status' => 'waiting',
            'created_at' => now(), // Push to end of line
        ]);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Patient restored to the end of the queue.'
            ]);
        }
        return back()->with('success', 'Patient restored to queue.');
    }
}
