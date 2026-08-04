<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentConfirmation;
use App\Mail\OtpMail;
use App\Models\Announcement;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function index()
    {
        $doctors = \App\Models\User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->with('practitionerSchedules')->get();
        // Fetch up to 7 latest published announcements for the carousel (Total 8 slides with hero)
        $announcements = Announcement::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        return view('welcome', compact('doctors', 'announcements'));
    }

    public function about()
    {
        return view('about');
    }

    public function units()
    {
        // We'll define static units for now since there's no dynamic Model for units
        $units = [
            ['name' => 'Main Health Center', 'slug' => 'main-health-center', 'desc' => 'Comprehensive check-ups, diagnostics, and general medical consultations.'],
            ['name' => 'Lying-in Clinic', 'slug' => 'lying-in-clinic', 'desc' => '24/7 maternity care, safe delivery, and newborn screening services.'],
            ['name' => 'Dental Clinic', 'slug' => 'dental-clinic', 'desc' => 'Tooth extraction, oral prophylaxis, and general dental hygiene.'],
            ['name' => 'TB DOTS Facility', 'slug' => 'tb-dots-facility', 'desc' => 'Tuberculosis screening, medication, and full treatment monitoring.'],
            ['name' => 'Animal Bite Center', 'slug' => 'animal-bite-center', 'desc' => 'Immediate care and vaccination for rabies prevention.'],
        ];

        return view('units.index', compact('units'));
    }

    public function showUnit($slug)
    {
        $units = collect([
            ['name' => 'Main Health Center', 'slug' => 'main-health-center', 'desc' => 'Comprehensive check-ups, diagnostics, and general medical consultations.'],
            ['name' => 'Lying-in Clinic', 'slug' => 'lying-in-clinic', 'desc' => '24/7 maternity care, safe delivery, and newborn screening services.'],
            ['name' => 'Dental Clinic', 'slug' => 'dental-clinic', 'desc' => 'Tooth extraction, oral prophylaxis, and general dental hygiene.'],
            ['name' => 'TB DOTS Facility', 'slug' => 'tb-dots-facility', 'desc' => 'Tuberculosis screening, medication, and full treatment monitoring.'],
            ['name' => 'Animal Bite Center', 'slug' => 'animal-bite-center', 'desc' => 'Immediate care and vaccination for rabies prevention.'],
        ]);

        $unit = $units->firstWhere('slug', $slug);

        if (! $unit) {
            abort(404);
        }

        return view('units.show', compact('unit'));
    }

    public function checkHomeUpdates()
    {
        // Check for the latest update timestamp across all published announcements
        $latestAnnouncement = Announcement::where('status', 'published')
            ->latest('updated_at')
            ->first();

        // Check for the latest update timestamp across all doctors
        $latestDoctor = \App\Models\User::where('role', 'doctor')->latest('updated_at')->first();

        return response()->json([
            'last_announcement_modified' => $latestAnnouncement ? $latestAnnouncement->updated_at->toDateTimeString() : '',
            'last_doctor_modified' => $latestDoctor ? $latestDoctor->updated_at->toDateTimeString() : '',
            'announcement_count' => Announcement::where('status', 'published')->count(),
            'doctor_count' => \App\Models\User::where('role', 'doctor')->count(),
        ]);
    }

    public function showAnnouncement(Announcement $announcement)
    {
        return view('announcements.show', compact('announcement'));
    }

    public function checkAnnouncementUpdate(Announcement $announcement)
    {
        return response()->json([
            'updated_at' => $announcement->updated_at->toIso8601String(),
        ]);
    }

    public function getAnnouncementContent(Announcement $announcement)
    {
        // Render the view partial or full view - we will extract the relevant section in frontend or return a partial view
        // For simplicity, we can return the entire view and let Alpine/HTMX swap, OR we can make a partial.
        // A cleaner way for "everything dynamic" without full reload is to return the specific HTML for the article.
        // Let's return the full view for now but we will use a special header to indicate it's a fragment if we wanted,
        // but here we will just let the frontend regex/parse or just return a JSON with html.
        // Actually, returning JSON with HTML is easiest for Alpine to handle.

        $html = view('announcements.show', compact('announcement'))->render();

        return response()->json(['html' => $html]);
    }

    public function showService(\App\Models\Service $service)
    {
        return view('services.show', compact('service'));
    }

    public function createAppointment()
    {
        return view('appointments.create');
    }

    public function checkAvailability(Request $request)
    {
        $startDate = \Carbon\Carbon::parse($request->input('start'));
        $endDate = \Carbon\Carbon::parse($request->input('end'));
        $type = $request->input('type', 'pedia');
        $isFollowUp = $request->boolean('is_follow_up', false);

        $result = [];

        // We only enforce the 20-slot limit on New Pedia appointments
        if ($type === 'pedia' && ! $isFollowUp) {
            $bookedCounts = Appointment::where('type', 'pedia')
                ->whereIn('status', ['pending', 'approved', 'rescheduled', 'arrived'])
                ->whereBetween('preferred_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->select(\DB::raw('DATE(preferred_date) as date'), \DB::raw('count(*) as count'))
                ->groupBy('date')
                ->pluck('count', 'date');

            // Find if there's any active pedia doctor with a schedule
            $pediaDoctors = \App\Models\User::where('role', 'pedia_doctor')
                ->with('practitionerSchedules')
                ->get();

            // Iterate over the date range
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dateString = $date->toDateString();
                $dayShort = $date->format('D'); // "Mon", "Tue"

                $capacity = 0;
                // Check if any pedia doctor is scheduled for this day
                foreach ($pediaDoctors as $doctor) {
                    if ($doctor->practitionerSchedules->contains('day_of_week', $dayShort)) {
                        $capacity = 20; // Hardcoded clinic-wide capacity if at least 1 doctor is working
                        break;
                    }
                }

                $booked = $bookedCounts->get($dateString, 0);

                $result[$dateString] = [
                    'booked' => $booked,
                    'capacity' => $capacity,
                    'available' => max(0, $capacity - $booked),
                ];
            }
        }

        return response()->json($result);
    }

    /**
     * Get available 30-minute time slots for a given date based on doctor schedules.
     * For pedia: uses pedia_doctor schedule
     * For follow-up: uses the assigned doctor's schedule (passed via doctor_id param)
     */
    public function getTimeSlots(Request $request)
    {
        $date = \Carbon\Carbon::parse($request->input('date'));
        $dayShort = $date->format('D'); // "Mon", "Tue", etc.
        $type = $request->input('type', 'pedia');
        $doctorId = $request->input('doctor_id'); // For follow-ups

        $schedules = collect();

        if ($doctorId) {
            // Follow-up: get the specific doctor's schedule for this day
            $schedules = \App\Models\PractitionerSchedule::where('user_id', $doctorId)
                ->where('day_of_week', $dayShort)
                ->get();
        } else {
            // Pedia new: get all pedia doctor schedules for this day
            $pediaDoctorIds = \App\Models\User::where('role', 'pedia_doctor')->pluck('id');
            $schedules = \App\Models\PractitionerSchedule::whereIn('user_id', $pediaDoctorIds)
                ->where('day_of_week', $dayShort)
                ->get();
        }

        if ($schedules->isEmpty()) {
            return response()->json(['slots' => [], 'message' => 'No doctor available on this day.']);
        }

        // Find the earliest start and latest end across all matching schedules
        $earliest = $schedules->min('time_in');
        $latest = $schedules->max('time_out');

        $startTime = \Carbon\Carbon::parse($earliest);
        $endTime = \Carbon\Carbon::parse($latest);

        // Generate 30-minute slots
        $slots = [];
        $cursor = $startTime->copy();
        while ($cursor->lt($endTime)) {
            $slotEnd = $cursor->copy()->addMinutes(30);
            if ($slotEnd->gt($endTime)) {
                break;
            }

            $slots[] = [
                'label' => $cursor->format('g:i A').' - '.$slotEnd->format('g:i A'),
                'value' => $cursor->format('g:i A').' - '.$slotEnd->format('g:i A'),
            ];
            $cursor->addMinutes(30);
        }

        return response()->json(['slots' => $slots]);
    }

    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
        ]);

        $exists = Appointment::where('email', $request->email)
            ->where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->whereIn('status', ['pending', 'approved', 'rescheduled'])
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function verifyFollowUp(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'dob' => 'required|date',
        ]);

        $patient = \App\Models\Patient::where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->whereDate('dob', \Carbon\Carbon::parse($request->dob)->toDateString())
            ->first();

        if (! $patient) {
            return response()->json(['valid' => false, 'message' => 'No matching patient record found. Please verify your Name and Date of Birth.']);
        }

        $latestFollowUp = \App\Models\Consultation::where('patient_id', $patient->patient_id)
            ->where('is_followup_needed', true)
            ->whereNull('followup_completed_at') // Only unfulfilled follow-ups
            ->latest('created_at')
            ->first();

        if (! $latestFollowUp) {
            return response()->json(['valid' => false, 'message' => 'You do not have any active follow-up instructions from a doctor.']);
        }

        $followUpDate = \Carbon\Carbon::parse($latestFollowUp->followup_date)->startOfDay();
        $today = \Carbon\Carbon::today();

        // Follow up is invalidated after 3 months from the target date
        if ($today->gt($followUpDate->copy()->addMonths(3))) {
            return response()->json(['valid' => false, 'message' => 'Your follow-up target date has expired (exceeded 3 months). Please register as a regular patient.']);
        }

        // Get the assigned doctor's schedule for the calendar
        $doctorId = $latestFollowUp->followup_doctor_id ?? $latestFollowUp->doctor_id;
        $doctor = $doctorId ? \App\Models\User::with('practitionerSchedules')->find($doctorId) : null;

        // Fallback: If no doctor or no schedule, default to standard clinic weekdays to avoid soft-locking
        $doctorSchedule = ($doctor && $doctor->formatted_schedule) ? $doctor->formatted_schedule : 'Mon, Tue, Wed, Thu, Fri';
        $validDays = ($doctor && $doctor->practitionerSchedules->isNotEmpty())
            ? $doctor->practitionerSchedules->pluck('day_of_week')->unique()->values()->toArray()
            : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

        return response()->json([
            'valid' => true,
            'doctor_id' => $doctorId,
            'doctor_schedule' => $doctorSchedule,
            'valid_days' => $validDays,
            'doctor_name' => $doctor ? $doctor->formatted_name : 'Assigned Doctor',
        ]);
    }

    public function storeAppointment(Request $request)
    {
        $titleCaseFields = [
            'first_name',
            'middle_name',
            'last_name',
            'mothers_maiden_name',
            'address',
            'house_no',
            'street',
            'building',
            'barangay',
            'guardian_first_name',
            'guardian_middle_name',
            'guardian_last_name',
        ];
        foreach ($titleCaseFields as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $request->merge([$field => ucwords(strtolower($request->input($field)))]);
            }
        }

        $ipKey = 'booking_ip:'.$request->ip();
        $emailKey = 'booking_email:'.strtolower($request->email ?? '');

        if (RateLimiter::tooManyAttempts($emailKey, 5)) {
            $seconds = RateLimiter::availableIn($emailKey);

            return back()->with('error', "You have made too many appointment requests. Please try again in {$seconds} seconds.")->withInput();
        }

        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            $seconds = RateLimiter::availableIn($ipKey);

            return back()->with('error', "Too many appointments from your network. Try again in {$seconds} seconds.")->withInput();
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'suffix' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'sex' => 'required|in:Male,Female',
            'dob' => 'required|date',
            'civil_status' => 'nullable|string|max:50',
            'blood_type' => 'required|string|max:10',
            'address' => 'required|string|max:500',
            'house_no' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'city_province' => 'nullable|string|max:255',
            'philhealth_number' => ['required', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
            'education' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'mothers_maiden_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'],
            'classification' => 'nullable|string|in:Pediatric,Regular Adult,Senior Citizen,PWD',
            'type' => 'required|in:pedia,adult',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required|string|max:30',
            'complaint' => 'required|string|max:1000',
            'data_privacy_agreed' => 'accepted',
        ];

        if ($request->type === 'adult') {
            $rules['contact_number'] = ['required', 'regex:/^09\d{9}$/'];
        }

        // Validate guardian and is_follow_up if pedia
        if ($request->type === 'pedia') {
            $rules['guardian_first_name'] = ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'];
            $rules['guardian_last_name'] = ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'];
            $rules['guardian_middle_name'] = ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'];
            $rules['guardian_suffix'] = ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z\s\.\-ñÑ]+$/'];
            $rules['guardian_relation'] = 'required|string|in:Mother,Father,Grandparent,Sibling,Other';
            $rules['guardian_contact'] = ['required', 'regex:/^09\d{9}$/'];
            $rules['is_follow_up'] = 'boolean';
        }

        $isFollowUp = $request->type === 'adult' || ($request->type === 'pedia' && $request->is_follow_up);

        if ($isFollowUp) {
            $rules['blood_type'] = 'nullable|string|max:10';
            $rules['address'] = 'nullable|string|max:500';
            $rules['philhealth_number'] = 'nullable|regex:/^\d{2}-\d{9}-\d{1}$/';
            $rules['mothers_maiden_name'] = 'nullable|string|max:255';

            if ($request->type === 'adult') {
                $rules['contact_number'] = ['nullable', 'regex:/^09\d{9}$/'];
            }
            if ($request->type === 'pedia') {
                $rules['guardian_first_name'] = 'nullable|string|max:255';
                $rules['guardian_last_name'] = 'nullable|string|max:255';
                $rules['guardian_middle_name'] = 'nullable|string|max:255';
                $rules['guardian_suffix'] = 'nullable|string|max:20';
                $rules['guardian_relation'] = 'nullable|string';
                $rules['guardian_contact'] = ['nullable', 'regex:/^09\d{9}$/'];
            }
        }

        $request->validate($rules, [
            'guardian_contact.regex' => 'Guardian contact number must be a valid Philippine mobile number (09XXXXXXXXX).',
            'contact_number.regex' => 'Contact number must be a valid Philippine mobile number (09XXXXXXXXX).',
        ]);

        if ($request->type === 'pedia') {
            $age = \Carbon\Carbon::parse($request->dob)->age;
            if ($age > 12) {
                return back()->withErrors(['dob' => 'Pediatric patients must be 12 years old or below. Please select Adult Follow-up instead.'])->withInput();
            }
        } else {
            $age = \Carbon\Carbon::parse($request->dob)->age;
            if ($age <= 12) {
                return back()->withErrors(['dob' => 'Patients 12 years old and below must use the Pediatrics booking flow.'])->withInput();
            }
        }

        // Check for duplicate pending/approved/rescheduled appointments
        $existingAppointment = Appointment::where('email', $request->email)
            ->where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->whereIn('status', ['pending', 'approved', 'rescheduled'])
            ->exists();

        if (isset($existingAppointment) && $existingAppointment) {
            RateLimiter::hit($ipKey, 300);
            RateLimiter::hit($emailKey, 600);

            return back()->withErrors(['email' => 'You already have an active appointment (pending, approved, or rescheduled). Please wait for the current appointment to conclude before creating a new one.'])->withInput();
        }

        // ── 5. Follow-up Verification (Adult & Pedia) ────────────────────────
        if ($isFollowUp) {
            // Search for existing patient
            $patientMatch = \App\Models\Patient::where('first_name', $request->first_name)
                ->where('last_name', $request->last_name)
                ->where('dob', $request->dob)
                ->first();

            if (! $patientMatch) {
                return back()->withErrors([
                    'is_follow_up' => 'Patient record not found. Please register as a new patient at the clinic.',
                ])->withInput();
            }

            // Verify if the patient has a consultation flagged for follow-up (using model accessor)
            if (! $patientMatch->is_follow_up) {
                return back()->withErrors([
                    'is_follow_up' => 'No follow-up order found. Please book as a General Consultation instead.',
                ])->withInput();
            }
        }

        $data = $request->all();
        // Checkboxes return 'on' or '1', force boolean for DB
        $data['data_privacy_agreed'] = $request->has('data_privacy_agreed');

        if ($request->type === 'pedia') {
            if (! empty($data['philhealth_number'])) {
                $data['guardian_philhealth'] = $data['philhealth_number'];
                $data['philhealth_number'] = null;
            }
        }

        // Generate Unique Reference Number
        do {
            $ref = 'APT-'.strtoupper(auth()->id() ?? '').strtoupper(Str::random(8));
        } while (Appointment::where('reference_number', $ref)->exists());

        $data['reference_number'] = $ref;
        $data['status'] = 'approved'; // Auto-approve as per requirements

        $appointment = Appointment::create($data);

        // Send Email
        try {
            Mail::to($request->email)->send(new AppointmentConfirmation($appointment));
        } catch (\Exception $e) {
            // Log error or ignore for dev
        }

        RateLimiter::clear($ipKey);
        RateLimiter::clear($emailKey);

        return redirect()->route('welcome')->with('success', 'Appointment Request Submitted Successfully! Your Reference Number is '.$ref.'. Please check your email.');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        $lockoutKey = 'otp_lockout_'.$email;
        $attemptsKey = 'otp_attempts_'.$email;

        if (Cache::has($lockoutKey)) {
            $secondsRemaining = Cache::get($lockoutKey) - time();
            if ($secondsRemaining > 0) {
                $minutes = ceil($secondsRemaining / 60);

                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please try again in '.$minutes.' minute(s).',
                ], 429);
            }
            // Lockout expired but cache entry lingered — clear it
            Cache::forget($lockoutKey);
        }

        $otp = rand(100000, 999999);

        // Store in cache for 10 minutes
        Cache::put('otp_'.$email, $otp, 600);

        try {
            Mail::to($email)->send(new OtpMail($otp));

            // Progressive cooldown: first send has a short 1-min cooldown,
            // escalating on repeated requests to prevent abuse.
            // Attempt 0 = 1 min, 1 = 5 min, 2 = 10 min, 3 = 15 min, ...
            $penalties = [1, 5, 10, 15, 30, 60, 120, 300, 1440];
            $attempts = Cache::get($attemptsKey, 0);

            $penaltyMinutes = $penalties[min($attempts, count($penalties) - 1)];
            Cache::put($lockoutKey, time() + ($penaltyMinutes * 60), $penaltyMinutes * 60);
            Cache::put($attemptsKey, $attempts + 1, 1440 * 60 * 2);

            return response()->json(['success' => true, 'message' => 'OTP sent to your email.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please check your email address.'], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $cachedOtp = Cache::get('otp_'.$request->email);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            Cache::forget('otp_attempts_'.$request->email);
            Cache::forget('otp_lockout_'.$request->email);
            Cache::forget('otp_'.$request->email);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 400);
    }

    // Appointment Management
    public function manageAppointment(Request $request)
    {
        return view('appointments.manage-login');
    }

    public function loginAppointment(Request $request)
    {
        $request->validate([
            'reference_number' => 'required|string|exists:appointments,reference_number',
            'email' => 'required|email',
        ]);

        $appointment = Appointment::where('reference_number', $request->reference_number)
            ->where('email', $request->email)
            ->first();

        if (! $appointment) {
            return back()->withErrors(['email' => 'Details do not match our records.']);
        }

        // Store in session for simplicity/security
        session(['manage_appointment_id' => $appointment->id]);

        return redirect()->route('appointment.dashboard');
    }

    public function dashboardAppointment()
    {
        if (! session('manage_appointment_id')) {
            return redirect()->route('appointment.manage');
        }

        $appointment = Appointment::findOrFail(session('manage_appointment_id'));

        return view('appointments.dashboard', compact('appointment'));
    }

    public function cancelAppointment(Request $request)
    {
        if (! session('manage_appointment_id')) {
            return redirect()->route('appointment.manage');
        }

        $appointment = Appointment::findOrFail(session('manage_appointment_id'));
        $appointment->status = 'cancelled';
        $appointment->save();

        return back()->with('success', 'Appointment has been cancelled successfully.');
    }

    public function rescheduleAppointment(Request $request)
    {
        if (! session('manage_appointment_id')) {
            return redirect()->route('appointment.manage');
        }

        $request->validate([
            'new_date' => 'required|date',
            'new_time' => 'required|string',
        ]);

        $appointment = Appointment::findOrFail(session('manage_appointment_id'));
        $isFollowUp = $appointment->type === 'adult' || $appointment->is_follow_up;

        // Enforce Pedia Capacity
        if (! $isFollowUp && $appointment->type === 'pedia') {
            $capacity = \App\Models\Capacity::where('date', $request->new_date)->value('capacity') ?? 20;

            $booked = Appointment::where('preferred_date', $request->new_date)
                ->where('type', 'pedia')
                ->where('is_follow_up', false)
                ->whereIn('status', ['pending', 'approved', 'rescheduled'])
                ->count();

            if ($booked >= $capacity) {
                return back()->withErrors(['new_date' => 'The selected date is fully booked. Please choose another date.']);
            }
        }

        $appointment->preferred_date = $request->new_date;
        $appointment->preferred_time = $request->new_time;
        $appointment->status = 'rescheduled';
        $appointment->save();

        return back()->with('success', 'Appointment rescheduled successfully to '.\Carbon\Carbon::parse($request->new_date)->format('F d, Y').' at '.$request->new_time);
    }
}
