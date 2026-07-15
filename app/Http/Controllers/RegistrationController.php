<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\PreTriage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $patients = collect();

        if ($search) {
            $terms = explode(' ', $search);
            $query = Patient::query();

            foreach ($terms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('first_name', 'like', "%{$term}%")
                        ->orWhere('middle_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('philhealth_number', 'like', "%{$term}%")
                        ->orWhere('patient_id', 'like', "%{$term}%");
                });
            }
            $patients = $query->take(10)->get();
        }

        $recentPatients = Patient::orderBy('created_at', 'desc')->paginate(10);

        $doctors = User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->get();
        $nurses = User::where('role', 'clinical_nurse')->get();

        // Pre-Triage waiting queue — patients from Vitals Station awaiting Info Desk
        $preTriageWaiting = PreTriage::where('status', 'waiting')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();

        $todayAppointments = \App\Models\Appointment::whereDate('preferred_date', Carbon::today())
            ->whereIn('status', ['approved', 'rescheduled', 'arrived'])
            ->orderBy('preferred_date', 'asc')
            ->get();

        // Patients with a consultation today (already triaged)
        $todaysPatients = \App\Models\Consultation::whereDate('created_at', Carbon::today())
            ->with(['patient', 'doctor', 'nurse'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Patient IDs that already have a consultation today (so we don't double-count)
        $triagedPatientIds = $todaysPatients->pluck('patient_id')->toArray();

        // Patients registered TODAY but NOT yet triaged (missing from the consultation queue)
        $pendingNewPatients = Patient::whereDate('created_at', Carbon::today())
            ->whereNotIn('patient_id', $triagedPatientIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Pre-triage passthrough for new patients from triage pool
        $newFromTriage = null;
        if ($request->filled('new_from_triage')) {
            $newFromTriage = PreTriage::find($request->input('new_from_triage'));
        }

        // Pre-triage id for returning patients (auto-carry vitals)
        $preTriageId = $request->input('pre_triage_id');

        // Determine what panel to show on the right side
        // (mirrors FrontDeskController logic — we replicate the variables here)
        $selectedPatient = null;
        $isNewPatient   = false;
        $prefillApt     = null;
        if ($request->filled('prefill_apt')) {
            $prefillApt = \App\Models\Appointment::find($request->input('prefill_apt'));
            $isNewPatient = true; // Force show the form
        } elseif ($newFromTriage && $newFromTriage->appointment_id) {
            $prefillApt = \App\Models\Appointment::find($newFromTriage->appointment_id);
            $isNewPatient = true;
        } elseif ($preTriageId) {
            $pt = \App\Models\PreTriage::find($preTriageId);
            if ($pt && $pt->appointment_id) {
                $prefillApt = \App\Models\Appointment::find($pt->appointment_id);
            }
        }

        $newlyRegisteredPatient = session('new_patient_id')
            ? Patient::where('patient_id', session('new_patient_id'))->first()
            : null;

        if ($request->filled('selected_id')) {
            $selectedPatient = Patient::where('patient_id', $request->input('selected_id'))->first();
        } elseif ($newFromTriage) {
            $isNewPatient = true;
        } elseif ($request->has('new_patient')) {
            $isNewPatient = true;
        }

        return view('frontdesk.registration.index', compact(
            'patients', 'search', 'doctors', 'nurses',
            'recentPatients', 'todayAppointments', 'todaysPatients',
            'pendingNewPatients', 'preTriageWaiting',
            'newFromTriage', 'preTriageId',
            'selectedPatient', 'isNewPatient', 'prefillApt', 'newlyRegisteredPatient'
        ));
    }

    public function checkIn(\App\Models\Appointment $appointment)
    {
        if (in_array($appointment->status, ['pending', 'approved', 'rescheduled'])) {
            $appointment->update(['status' => 'arrived']);
        }

        return redirect()->route('frontdesk.registration.index')
            ->with('success', 'Patient Checked In! Please instruct the patient to proceed to the Vitals Station.');
    }
    public function searchJson(Request $request)
    {
        $search = $request->query('query');
        if (!$search || strlen($search) < 1) {
            return response()->json([]);
        }

        $terms = explode(' ', $search);
        $query = Patient::query();

        foreach ($terms as $term) {
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('middle_name', 'like', "%{$term}%")
                    ->orWhere('philhealth_number', 'like', "%{$term}%")
                    ->orWhere('patient_id', 'like', "%{$term}%");
            });
        }

        $patients = $query->take(8)->get(['patient_id', 'first_name', 'middle_name', 'last_name', 'dob', 'contact_number', 'classification']);

        return response()->json($patients->map(function ($patient) {
            return [
                'id' => $patient->patient_id,
                'formatted_name' => $patient->full_name,
                'formatted_dob' => \Carbon\Carbon::parse($patient->dob)->format('M d, Y'),
                'contact_number' => $patient->contact_number,
                'classification' => $patient->classification ?? 'Unclassified',
                'age' => \Carbon\Carbon::parse($patient->dob)->age,
                'is_follow_up' => $patient->is_follow_up,
            ];
        }));
    }

    public function updatePatient(Request $request, Patient $patient)
    {
        $titleCaseFields = [
            'first_name',
            'middle_name',
            'last_name',
            'guardian_first_name',
            'guardian_middle_name',
            'guardian_last_name',
            'mothers_maiden_name',
            'address',
            'house_no',
            'street',
            'building',
            'barangay'
        ];
        foreach ($titleCaseFields as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $request->merge([$field => ucwords(strtolower($request->input($field)))]);
            }
        }

        $classification = $request->input('classification');
        if ($classification === 'Pediatric') {
            if ($request->filled('philhealth_number')) {
                // If the frontend sent philhealth_number but it's a pediatric patient, it is actually the guardian's!
                $request->merge([
                    'guardian_philhealth' => $request->input('philhealth_number')
                ]);
                $request->request->remove('philhealth_number'); // Remove so it doesn't fail 'required' validation for normal philhealth
            }
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'suffix' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'sex' => 'required|in:Male,Female',
            'dob' => 'required|date|before:today',
            'blood_type' => 'required|string|max:10',
            'mothers_maiden_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'philhealth_number' => ['nullable', 'required_unless:classification,Pediatric', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
            'address' => 'required|string',
            'house_no' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'city_province' => 'nullable|string|max:255',
            'contact_number' => ['nullable', 'required_unless:classification,Pediatric', 'regex:/^09\d{9}$/'],
            'email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'civil_status' => 'nullable|string|max:100',
            'classification' => 'required|string|in:Regular Adult,Senior Citizen,PWD,Pediatric',
            'education' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'guardian_first_name' => ['nullable', 'required_if:classification,Pediatric', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_last_name' => ['nullable', 'required_if:classification,Pediatric', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_suffix' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_relation' => 'nullable|required_if:classification,Pediatric|string|max:255',
            'guardian_contact' => ['nullable', 'required_if:classification,Pediatric', 'regex:/^09\d{9}$/'],
            'guardian_philhealth' => ['nullable', 'required_if:classification,Pediatric', 'string', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
        ]);

        if (isset($validated['classification'])) {
            $age = \Carbon\Carbon::parse($patient->dob)->age;
            if ($validated['classification'] === 'Pediatric' && $age > 12) {
                return back()->with('error', 'Pediatric classification is restricted to patients 12 years old and below.');
            }
            if ($validated['classification'] === 'Regular Adult' && $age < 13) {
                return back()->with('error', 'Adult classification requires the patient to be at least 13 years old.');
            }
            if ($validated['classification'] === 'Senior Citizen' && $age < 60) {
                return back()->with('error', 'Senior Citizen classification requires the patient to be at least 60 years old.');
            }
        }

        $patient->update($validated);

        if ($request->filled('appointment_id')) {
            $appointment = \App\Models\Appointment::find($request->appointment_id);
            if ($appointment) {
                // If patient info was updated using the appointment, it's considered registered
                $appointment->update(['status' => 'registered']);
            }
        } else {
            // Auto-detect appointment
            $appointment = \App\Models\Appointment::whereDate('preferred_date', \Carbon\Carbon::today())
                ->whereIn('status', ['approved', 'rescheduled', 'arrived'])
                ->where('first_name', $validated['first_name'])
                ->where('last_name', $validated['last_name'])
                ->where('dob', $validated['dob'])
                ->first();
            
            if ($appointment) {
                $appointment->update(['status' => 'registered']);
                $request->merge(['appointment_id' => $appointment->id]);
            }
        }

        return back()->with('success', 'Patient information updated successfully.');
    }
    public function storePatient(Request $request)
    {
        $titleCaseFields = [
            'first_name',
            'middle_name',
            'last_name',
            'guardian_first_name',
            'guardian_middle_name',
            'guardian_last_name',
            'mothers_maiden_name',
            'address',
            'house_no',
            'street',
            'building',
            'barangay'
        ];
        foreach ($titleCaseFields as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $request->merge([$field => ucwords(strtolower($request->input($field)))]);
            }
        }

        $classification = $request->input('classification');
        if ($classification === 'Pediatric') {
            if ($request->filled('philhealth_number')) {
                // If the frontend sent philhealth_number but it's a pediatric patient, it is actually the guardian's!
                $request->merge([
                    'guardian_philhealth' => $request->input('philhealth_number')
                ]);
                $request->request->remove('philhealth_number'); // Remove so it doesn't fail 'required' validation
            }
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'suffix' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'nullable|string|max:100',
            'blood_type' => 'required|string|max:10',
            'dob' => 'required|date|before:today',
            'contact_number' => ['nullable', 'required_unless:classification,Pediatric', 'regex:/^09\d{9}$/'],
            'email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'address' => 'required|string',
            'house_no' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'city_province' => 'nullable|string|max:255',
            'philhealth_number' => ['nullable', 'required_unless:classification,Pediatric', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
            'mothers_maiden_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'classification' => 'required|string|in:Regular Adult,Senior Citizen,PWD,Pediatric',
            'occupation' => 'nullable|string|max:255',
            'education' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:255',
            'guardian_first_name' => ['nullable', 'required_if:classification,Pediatric', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_last_name' => ['nullable', 'required_if:classification,Pediatric', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_suffix' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_relation' => 'nullable|required_if:classification,Pediatric|string|max:255',
            'guardian_contact' => ['nullable', 'required_if:classification,Pediatric', 'regex:/^09\d{9}$/'],
            'guardian_philhealth' => ['nullable', 'required_if:classification,Pediatric', 'string', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
        ]);

        $age = \Carbon\Carbon::parse($validated['dob'])->age;
        if ($validated['classification'] === 'Pediatric' && $age > 12) {
            return back()->with('error', 'Pediatric classification is restricted to patients 12 years old and below. Please choose another classification.')->withInput();
        }
        if ($validated['classification'] === 'Regular Adult' && $age < 13) {
            return back()->with('error', 'Adult classification requires the patient to be at least 13 years old. Please correct the classification or date of birth.')->withInput();
        }
        if ($validated['classification'] === 'Senior Citizen' && $age < 60) {
            return back()->with('error', 'Senior Citizen classification requires the patient to be at least 60 years old. Please correct the classification or date of birth.')->withInput();
        }

        if ($validated['classification'] === 'Pediatric') {
        }

        // Basic duplicate check before creating
        $existing = Patient::where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->where('dob', $validated['dob'])
            ->first();

        if ($existing) {
            return back()->with('error', "A patient record for {$validated['first_name']} {$validated['last_name']} with this Date of Birth already exists (ID: {$existing->patient_id}). Please register them as an Old Patient.")->withInput();
        }

        $patient = Patient::create($validated);

        if ($request->filled('appointment_id')) {
            $appointment = \App\Models\Appointment::find($request->appointment_id);
            if ($appointment) {
                $appointment->update(['status' => 'registered']);
            }
        } else {
            // Auto-detect appointment
            $appointment = \App\Models\Appointment::whereDate('preferred_date', \Carbon\Carbon::today())
                ->whereIn('status', ['approved', 'rescheduled', 'arrived'])
                ->where('first_name', $validated['first_name'])
                ->where('last_name', $validated['last_name'])
                ->where('dob', $validated['dob'])
                ->first();
            
            if ($appointment) {
                $appointment->update(['status' => 'registered']);
            }
        }

        return redirect()->route('frontdesk.registration.index')
            ->with('success', 'Patient registered successfully.')
            ->with('new_patient_id', $patient->id);
    }

    public function storeVisit(Request $request, Patient $patient)
    {
        // Pre-triage ID is always required — no manual vitals entry
        $validated = $request->validate([
            'pre_triage_id'    => 'required|exists:pre_triages,id',
            'consultation_date'=> 'required|date',
            'symptom_severity' => 'required|in:light,mild,severe',
        ]);

        $preTriage = PreTriage::findOrFail($validated['pre_triage_id']);

        if ($request->filled('appointment_id')) {
            $appointment = \App\Models\Appointment::find($request->appointment_id);
            if ($appointment) {
                $appointment->update(['status' => 'registered']);
            }
        } else {
            // Auto-detect appointment for existing patient
            $appointment = \App\Models\Appointment::whereDate('preferred_date', \Carbon\Carbon::today())
                ->whereIn('status', ['approved', 'rescheduled', 'arrived'])
                ->where('first_name', $patient->first_name)
                ->where('last_name', $patient->last_name)
                ->where('dob', $patient->dob)
                ->first();
            
            if ($appointment) {
                $appointment->update(['status' => 'registered']);
                $request->merge(['appointment_id' => $appointment->id]); // Important for queue numbering
            }
        }

        // Vitals are now pulled dynamically from the PreTriage relation.

        // Prevent Duplicate Queue for the day (only if there is an active/queued one)
        $existingVisit = \App\Models\Consultation::where('patient_id', $patient->patient_id)
            ->whereDate('consultation_date', \Carbon\Carbon::parse($validated['consultation_date'])->toDateString())
            ->whereIn('status', ['queued', 'active', 'awaiting_results'])
            ->first();

        if ($existingVisit) {
            return back()->with('error', 'This patient is already queued or currently in an active consultation for today.');
        }

        // Calculate BMI
        $bmi = null;
        if (!empty($validated['height']) && !empty($validated['weight'])) {
            $heightInMeters = $validated['height'] / 100;
            if ($heightInMeters > 0) {
                $bmi = number_format($validated['weight'] / ($heightInMeters * $heightInMeters), 2);
            }
        }

        // ── Check if Follow-up ──────────────────────────────────────
        // 1. Find the last consultation that explicitly marked follow-up
        $latestFollowUp = \App\Models\Consultation::where('patient_id', $patient->patient_id)
            ->where('is_followup_needed', true)
            ->whereNull('followup_completed_at') // Only unfulfilled follow-ups
            ->orderBy('consultation_date', 'desc')
            ->first();

        $isFollowUpWithinGracePeriod = false;
        $originalDoctorId = null;

        if ($latestFollowUp) {
            $referenceDate = $latestFollowUp->followup_date 
                ? \Carbon\Carbon::parse($latestFollowUp->followup_date)->startOfDay() 
                : \Carbon\Carbon::parse($latestFollowUp->consultation_date)->startOfDay();
                
            $today = \Carbon\Carbon::today();
            if ($today->copy()->subMonths(3)->lte($referenceDate)) {
                $isFollowUpWithinGracePeriod = true;
                $originalDoctorId = $latestFollowUp->followup_doctor_id ?? $latestFollowUp->doctor_id;
            }
        }

        // 2. Fallback: If no explicit follow-up was marked, but they booked a follow-up appointment
        if (!$isFollowUpWithinGracePeriod && isset($appointment) && $appointment->is_follow_up) {
            $lastConsultation = \App\Models\Consultation::where('patient_id', $patient->patient_id)
                ->whereNotNull('doctor_id')
                ->orderBy('consultation_date', 'desc')
                ->first();
                
            if ($lastConsultation) {
                $isFollowUpWithinGracePeriod = true;
                $originalDoctorId = $lastConsultation->doctor_id;
            }
        }

        // ── Auto-Triage / Follow-up Routing ──────────────────────────────
        $doctor_id = null;
        $nurse_id = null;
        $classification = $patient->classification ?? 'Regular Adult';

        // ── Age Validation ──────────────────────────────────────────
        $age = \Carbon\Carbon::parse($patient->dob)->age;
        if ($classification === 'Pediatric' && $age > 12)
            return back()->with('error', 'Pediatric classification is restricted to patients 12 years old and below.');
        if ($classification === 'Regular Adult' && $age < 13)
            return back()->with('error', 'Adult classification requires the patient to be at least 13 years old.');
        if ($classification === 'Senior Citizen' && $age < 60)
            return back()->with('error', 'Senior Citizen classification requires the patient to be at least 60 years old.');

        $isRoutingResolved = false;

        if ($isFollowUpWithinGracePeriod && $originalDoctorId && !$request->has('override_absent_doctor')) {
            $originalDoctor = \App\Models\User::find($originalDoctorId);
            if ($originalDoctor && $originalDoctor->status === 'Present') {
                $doctor_id = $originalDoctorId;
                $isRoutingResolved = true;
            } else {
                return back()->withInput()->with('doctor_absent', true)
                    ->with('absent_doctor_name', $originalDoctor->name ?? 'Unknown Doctor')
                    ->with('error', 'The requested Follow-up Doctor (' . ($originalDoctor->name ?? 'Unknown') . ') is currently absent.');
            }
        }

        if (!$isRoutingResolved) {
            if ($classification === 'Pediatric') {
                // Pediatric → Always Pedia Doctor
                $pediaDoctor = \App\Models\User::where('role', 'pedia_doctor')->present()
                    ->withCount(['consultationsAsDoctor' => fn($q) => $q->whereDate('created_at', today())])
                    ->orderBy('consultations_as_doctor_count')->first()
                    ?? \App\Models\User::where('role', 'pedia_doctor')->present()->inRandomOrder()->first();

                if (!$pediaDoctor) {
                    return back()->with('error', 'Cannot queue pediatric patient: No Pediatrician is currently available.');
                }
                $doctor_id = $pediaDoctor->id;
            } else {
            // Adult / Senior / PWD — route by severity
            $severity = $validated['symptom_severity'];

            $nurse = User::where('role', 'clinical_nurse')->present()
                ->withCount(['consultationsAsNurse' => fn($q) => $q->whereDate('created_at', today())])
                ->orderBy('consultations_as_nurse_count')->first();

            $doctor = User::where('role', 'regular_doctor')->present()
                ->withCount(['consultationsAsDoctor' => fn($q) => $q->whereDate('created_at', today())])
                ->orderBy('consultations_as_doctor_count')->first();

            if ($severity === 'light') {
                // Light → Always Nurse
                if ($nurse) { $nurse_id = $nurse->id; }
                else if ($doctor) { $doctor_id = $doctor->id; } // fallback
            } elseif ($severity === 'severe') {
                // Severe → Always Doctor
                if ($doctor) { $doctor_id = $doctor->id; }
                else if ($nurse) { $nurse_id = $nurse->id; } // fallback
            } else {
                // Mild → Load balance
                if ($nurse && $doctor) {
                    if ($nurse->consultations_as_nurse_count <= $doctor->consultations_as_doctor_count) {
                        $nurse_id = $nurse->id;
                    } else {
                        $doctor_id = $doctor->id;
                    }
                } else if ($nurse) {
                    $nurse_id = $nurse->id;
                } else if ($doctor) {
                    $doctor_id = $doctor->id;
                }
            }

                if (!$nurse_id && !$doctor_id) {
                    return back()->with('error', 'Cannot auto-triage: No Regular Doctors or Clinical Nurses are currently available.');
                }
            }
        }

        // ── Generate Queue Number (PED-001, PRI-001, REG-001, APED-001, PED-E-001) ──
        $prefixMap = [
            'PWD' => 'PRI', 'Senior Citizen' => 'PRI',
            'Regular Adult' => 'REG', 'Pediatric' => 'PED'
        ];
        
        $isAppointment = $request->filled('appointment_id');
        $isEmergency = $request->has('emergency_override');

        $prefix = $prefixMap[$classification] ?? 'REG';
        if ($classification === 'Pediatric') {
            if ($isEmergency) {
                $prefix = 'PED-E';
            } elseif ($isAppointment) {
                $prefix = 'APED';
            } else {
                $prefix = 'PED';
            }
        }

        $consultationDate = \Carbon\Carbon::parse($validated['consultation_date']);

        // ── Enforce 5-Slot Pedia Walk-in Cap ────────────────────────
        if ($classification === 'Pediatric' && !$isAppointment && !$isEmergency) {
            $walkInCount = \App\Models\Consultation::whereDate('consultation_date', $consultationDate->toDateString())
                ->where('queue_number', 'LIKE', 'PED-%')
                ->count();
            if ($walkInCount >= 5) {
                return back()->with('error', 'The Walk-in limit (5 patients) for Pediatrics has been reached for today. Please advise the patient to book an appointment for another date.');
            }
        }
        
        $queueNumber = \Illuminate\Support\Facades\DB::transaction(function () use ($consultationDate, $prefix) {
            $latest = \App\Models\Consultation::whereDate('consultation_date', $consultationDate->toDateString())
                ->where('queue_number', 'LIKE', $prefix . '-%')
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();
                
            $dayCount = $latest ? intval(substr($latest->queue_number, strlen($prefix) + 1)) + 1 : 1;
            return $prefix . '-' . str_pad($dayCount, 3, '0', STR_PAD_LEFT);
        });

        // ── Create Consultation ─────────────────────────────────────
        $consultation = \App\Models\Consultation::create([
            'patient_id'           => $patient->patient_id,
            'doctor_id'            => $doctor_id,
            'nurse_id'             => $nurse_id,
            'pre_triage_id'        => $preTriage->id,
            'consultation_date'    => $validated['consultation_date'],
            'queue_number'         => $queueNumber,
            'status'               => 'queued',
            'severity'             => $validated['symptom_severity'] ?? null,
            'blood_pressure'       => $preTriage->blood_pressure,
            'temperature'          => $preTriage->temperature,
            'weight'               => $preTriage->weight,
            'height'               => $preTriage->height,
            'heart_rate'           => $preTriage->heart_rate,
            'respiratory_rate'     => $preTriage->respiratory_rate,
            'pulse_rate'           => $preTriage->pulse_rate,
            'spo2'                 => $preTriage->spo2 ?? $preTriage->oxygen_saturation,
        ]);

        // ── Create Queue Entry (Workflow Tracking) ──────────────────
        \App\Models\Queue::create([
            'patient_id'    => $patient->patient_id,
            'queue_number'  => $queueNumber,
            'priority_type' => $isEmergency ? 'Emergency' : ($isAppointment ? 'Appointment' : (in_array($classification, ['Senior Citizen', 'PWD']) ? 'Priority' : 'Regular')),
            'service_type'  => 'Consultation',
            'status'        => 'Waiting',
        ]);

        // Mark the pre-triage record as claimed
        $preTriage->update(['status' => 'claimed', 'patient_id' => $patient->patient_id]);

        if ($request->filled('appointment_id')) {
            $appointment = \App\Models\Appointment::find($request->appointment_id);
            if ($appointment) {
                $appointment->update(['status' => 'registered']);
            }
        }

        // ── Audit Logging ──────────────────────────────────────────────────────
        \App\Models\AuditLog::record('Consultation Queued', $consultation, [
            'queue_number' => $queueNumber,
            'is_followup_routing' => $isFollowUpWithinGracePeriod ?? false
        ]);

        return redirect()->route('frontdesk.registration.index')
            ->with('print_queue_id', $consultation->id)
            ->with('success', 'Visit added successfully! Patient is now in queue.');
    }

    public function queueSlip(Consultation $visit)
    {
        $visit->load('patient', 'doctor', 'nurse');
        return view('frontdesk.registration.queue-slip', compact('visit'));
    }

    /**
     * NEW PATIENT via Pre-Triage: Create patient record + consultation + queue in one step.
     * Called when a new patient (no existing Patient record) is processed at the Info Desk
     * after their vitals were already recorded by the Vitals Nurse.
     */
    public function registerAndQueue(Request $request, PreTriage $preTriage)
    {
        // ── 1. Register the patient (same logic as storePatient) ───────────────
        $titleCaseFields = ['first_name','middle_name','last_name','address',
                            'mothers_maiden_name','guardian_first_name','guardian_middle_name','guardian_last_name','house_no','street','building','barangay'];
        foreach ($titleCaseFields as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $request->merge([$field => ucwords(strtolower($request->input($field)))]);
            }
        }

        // Pediatric philhealth swap
        if ($request->input('classification') === 'Pediatric' && $request->filled('philhealth_number')) {
            $request->merge(['guardian_philhealth' => $request->input('philhealth_number')]);
            $request->request->remove('philhealth_number');
        }

        $validated = $request->validate([
            'first_name'          => ['required','string','max:255','regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'last_name'           => ['required','string','max:255','regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'suffix'              => ['nullable','string','max:20'],
            'middle_name'         => ['nullable','string','max:255','regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'sex'                 => 'required|in:Male,Female',
            'civil_status'        => 'nullable|string|max:100',
            'blood_type'          => 'required|string|max:10',
            'dob'                 => 'required|date|before:today',
            'contact_number'      => ['nullable', 'required_unless:classification,Pediatric', 'regex:/^09\d{9}$/'],
            'email'               => ['nullable','email:rfc,dns','max:255'],
            'address'             => 'required|string',
            'house_no'           => 'nullable|string|max:255',
            'street'             => 'nullable|string|max:255',
            'building'           => 'nullable|string|max:255',
            'barangay'           => 'nullable|string|max:255',
            'city_province'      => 'nullable|string|max:255',
            'philhealth_number'   => ['nullable','required_unless:classification,Pediatric','regex:/^\d{2}-\d{9}-\d{1}$/'],
            'mothers_maiden_name' => ['required','string','max:255','regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'classification'      => 'required|string|in:Regular Adult,Senior Citizen,PWD,Pediatric',
            'occupation'          => 'nullable|string|max:255',
            'education'           => 'nullable|string|max:255',
            'religion'            => 'nullable|string|max:255',
            'guardian_first_name' => ['nullable','required_if:classification,Pediatric','string','max:255','regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_last_name'  => ['nullable','required_if:classification,Pediatric','string','max:255','regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_middle_name'=> ['nullable','string','max:255','regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_suffix'     => ['nullable','string','max:20','regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'guardian_relation'   => 'nullable|required_if:classification,Pediatric|string|max:255',
            'guardian_contact'    => ['nullable','required_if:classification,Pediatric','regex:/^09\d{9}$/'],
            'guardian_philhealth' => ['nullable','required_if:classification,Pediatric','string','regex:/^\d{2}-\d{9}-\d{1}$/'],
            'consultation_date'   => 'nullable|date',
            'symptom_severity'    => 'required|in:light,mild,severe',
        ]);

        // Default consultation_date to today
        $validated['consultation_date'] = $validated['consultation_date'] ?? date('Y-m-d');

        $age = Carbon::parse($validated['dob'])->age;
        if ($validated['classification'] === 'Pediatric' && $age > 12)
            return back()->with('error', 'Pediatric classification is restricted to patients 12 years old and below.')->withInput();
        if ($validated['classification'] === 'Regular Adult' && $age < 13)
            return back()->with('error', 'Adult classification requires the patient to be at least 13 years old.')->withInput();
        if ($validated['classification'] === 'Senior Citizen' && $age < 60)
            return back()->with('error', 'Senior Citizen classification requires the patient to be at least 60 years old.')->withInput();

        // Duplicate Check Strategy: Exact match on First Name, Last Name, and Date of Birth
        $existingPatient = Patient::where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->where('dob', $validated['dob'])
            ->first();

        if ($existingPatient) {
            return back()->with('error', "A patient record for {$validated['first_name']} {$validated['last_name']} with this Date of Birth already exists (ID: {$existingPatient->patient_id}). Please register them as an Old Patient.")->withInput();
        }

        // Patient creation deferred until after all validation and routing checks
        // $patient = Patient::create($validated);

        // ── 2. Pull vitals from the linked PreTriage record ────────────────────
        $bmi = null;
        if ($preTriage->height && $preTriage->weight) {
            $hm = $preTriage->height / 100;
            if ($hm > 0) $bmi = number_format($preTriage->weight / ($hm * $hm), 2);
        }

        // ── 3. Severity-Based Auto-Triage ──────────────────────────────────────
        $doctor_id = null;
        $nurse_id  = null;
        $classification = $validated['classification'];

        if ($classification === 'Pediatric') {
            // Pediatric → Always Pedia Doctor
            $pediaDoc = User::where('role','pedia_doctor')->present()
                ->withCount(['consultationsAsDoctor'=>fn($q)=>$q->whereDate('created_at',today())])
                ->orderBy('consultations_as_doctor_count')->first()
                ?? User::where('role','pedia_doctor')->inRandomOrder()->first();
            if (!$pediaDoc) return back()->with('error','No Pediatrician available.')->withInput();
            $doctor_id = $pediaDoc->id;
        } else {
            // Adult / Senior / PWD — route by severity
            $severity = $validated['symptom_severity'];

            $nurse = User::where('role','clinical_nurse')->present()
                ->withCount(['consultationsAsNurse'=>fn($q)=>$q->whereDate('created_at',today())])
                ->orderBy('consultations_as_nurse_count')->first();

            $doctor = User::where('role','regular_doctor')->present()
                ->withCount(['consultationsAsDoctor'=>fn($q)=>$q->whereDate('created_at',today())])
                ->orderBy('consultations_as_doctor_count')->first();

            if ($severity === 'light') {
                if ($nurse) { $nurse_id = $nurse->id; }
                else if ($doctor) { $doctor_id = $doctor->id; }
            } elseif ($severity === 'severe') {
                if ($doctor) { $doctor_id = $doctor->id; }
                else if ($nurse) { $nurse_id = $nurse->id; }
            } else {
                // Mild → Load balance
                if ($nurse && $doctor) {
                    if ($nurse->consultations_as_nurse_count <= $doctor->consultations_as_doctor_count) {
                        $nurse_id = $nurse->id;
                    } else {
                        $doctor_id = $doctor->id;
                    }
                } else if ($nurse) {
                    $nurse_id = $nurse->id;
                } else if ($doctor) {
                    $doctor_id = $doctor->id;
                }
            }

            if (!$nurse_id && !$doctor_id) {
                return back()->with('error', 'Cannot auto-triage: No Regular Doctors or Clinical Nurses are currently available.')->withInput();
            }
        }

        // ── 4. Generate Queue Number (PED-001, PRI-001, REG-001, APED-001, PED-E-001) ─────
        $prefixMap = ['PWD'=>'PRI','Senior Citizen'=>'PRI','Regular Adult'=>'REG','Pediatric'=>'PED'];
        
        $isAppointment = $request->filled('appointment_id');
        $isEmergency = $request->has('emergency_override');

        $prefix = $prefixMap[$classification] ?? 'REG';
        if ($classification === 'Pediatric') {
            if ($isEmergency) {
                $prefix = 'PED-E';
            } elseif ($isAppointment) {
                $prefix = 'APED';
            } else {
                $prefix = 'PED';
            }
        }
        
        $consultationDate = \Carbon\Carbon::parse($validated['consultation_date']);

        // ── Enforce 5-Slot Pedia Walk-in Cap ────────────────────────
        if ($classification === 'Pediatric' && !$isAppointment && !$isEmergency) {
            $walkInCount = Consultation::whereDate('consultation_date', $consultationDate->toDateString())
                ->where('queue_number', 'LIKE', 'PED-%')
                ->count();
            if ($walkInCount >= 5) {
                return back()->with('error', 'The Walk-in limit (5 patients) for Pediatrics has been reached for today. Please advise the patient to book an appointment for another date.')->withInput();
            }
        }

        // Create Patient after all validation and routing checks pass
        $patient = Patient::create($validated);

        $dayCount = \Illuminate\Support\Facades\DB::transaction(function () use ($consultationDate, $prefix) {
            $latest = Consultation::whereDate('consultation_date', $consultationDate->toDateString())
                ->where('queue_number', 'LIKE', $prefix . '-%')
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();
            return $latest ? intval(substr($latest->queue_number, strlen($prefix) + 1)) + 1 : 1;
        });
        
        $queueNumber = $prefix . '-' . str_pad($dayCount, 3, '0', STR_PAD_LEFT);

        // ── 5. Create Consultation ─────────────────────────────────────────────
        $consultation = Consultation::create([
            'patient_id'           => $patient->patient_id,
            'doctor_id'            => $doctor_id,
            'nurse_id'             => $nurse_id,
            'pre_triage_id'        => $preTriage->id,
            'consultation_date'    => $validated['consultation_date'],
            'queue_number'         => $queueNumber,
            'status'               => 'queued',
            'severity'             => $validated['symptom_severity'] ?? null,
            'blood_pressure'       => $preTriage->blood_pressure,
            'temperature'          => $preTriage->temperature,
            'weight'               => $preTriage->weight,
            'height'               => $preTriage->height,
            'heart_rate'           => $preTriage->heart_rate,
            'respiratory_rate'     => $preTriage->respiratory_rate,
            'pulse_rate'           => $preTriage->pulse_rate,
            'spo2'                 => $preTriage->spo2 ?? $preTriage->oxygen_saturation,
        ]);

        // ── 5a. Create Queue Entry ─────────────────────────────────────────────
        \App\Models\Queue::create([
            'patient_id'    => $patient->patient_id,
            'queue_number'  => $queueNumber,
            'priority_type' => $isEmergency ? 'Emergency' : ($isAppointment ? 'Appointment' : (in_array($classification, ['Senior Citizen', 'PWD']) ? 'Priority' : 'Regular')),
            'service_type'  => 'Consultation',
            'status'        => 'Waiting',
        ]);

        // ── 6. Mark PreTriage as claimed ──────────────────────────────────────
        $preTriage->update(['status' => 'claimed', 'patient_id' => $patient->patient_id]);

        // ── 7. Auto-detect & link appointment ─────────────────────────────────
        if ($request->filled('appointment_id')) {
            $appointment = \App\Models\Appointment::find($request->appointment_id);
            if ($appointment) {
                $appointment->update(['status' => 'registered']);
            }
        } else {
            // Auto-detect by name + DOB match
            $appointment = \App\Models\Appointment::whereDate('preferred_date', \Carbon\Carbon::today())
                ->whereIn('status', ['approved', 'rescheduled', 'arrived'])
                ->where('first_name', $validated['first_name'])
                ->where('last_name', $validated['last_name'])
                ->where('dob', $validated['dob'])
                ->first();

            if ($appointment) {
                $appointment->update(['status' => 'registered']);
            }
        }

        // ── 8. Audit Logging ──────────────────────────────────────────────────
        \App\Models\AuditLog::record('Patient Registered', $patient);
        \App\Models\AuditLog::record('Consultation Queued', $consultation, [
            'queue_number' => $queueNumber,
            'doctor_assigned' => $doctor_id ?? 'Nurse assigned'
        ]);

        return redirect()->route('frontdesk.registration.index')
            ->with('print_queue_id', $consultation->id)
            ->with('success', "Patient {$patient->first_name} {$patient->last_name} registered and added to queue as {$queueNumber}!");
    }
}

