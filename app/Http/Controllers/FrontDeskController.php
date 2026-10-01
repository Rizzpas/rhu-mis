<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FrontDeskController extends Controller
{
    /**
     * Display the Front Desk Dashboard (Calendar View).
     */
    public function dashboard()
    {
        $presentStaff = \App\Models\User::whereIn('role', ['pedia_doctor', 'regular_doctor', 'clinical_nurse', 'vitals_nurse'])
            ->present()
            ->get();

        $staffGroups = [
            'Doctors' => $presentStaff->filter(function ($staff) {
                return in_array($staff->role, ['pedia_doctor', 'regular_doctor']);
            }),
            'Clinical Nurses' => $presentStaff->filter(function ($staff) {
                return $staff->role === 'clinical_nurse';
            }),
            'Vitals Nurses' => $presentStaff->filter(function ($staff) {
                return $staff->role === 'vitals_nurse';
            }),
        ];

        $dashboardStats = $this->getDashboardMetrics();

        return view('frontdesk.dashboard', compact('staffGroups', 'dashboardStats'));
    }

    /**
     * API Endpoint to fetch dynamic dashboard stats including walk-ins.
     */
    public function getStats(Request $request)
    {
        $month = $request->query('month') ? (int) $request->query('month') : null;
        $year = $request->query('year') ? (int) $request->query('year') : null;

        return response()->json($this->getDashboardMetrics($month, $year));
    }

    /**
     * Calculate comprehensive operational and calendar stats (walk-ins + appointments).
     */
    private function getDashboardMetrics($month = null, $year = null)
    {
        $today = Carbon::today();
        $month = $month ?: $today->month;
        $year = $year ?: $today->year;

        // 1. Completed Consultations Today (discharged patients - both walk-ins & appointments)
        $completedToday = \App\Models\Consultation::whereDate('consultation_date', $today)
            ->where('status', 'completed')
            ->count();

        // 2. Currently Active in Pipeline (queued, in consult, awaiting labs)
        $activeQueue = \App\Models\Consultation::whereDate('consultation_date', $today)
            ->whereIn('status', ['queued', 'active', 'awaiting_results', 'results_ready', 'called'])
            ->count();

        // 3. Appointments scheduled for today
        $todayAppointments = Appointment::whereDate('preferred_date', $today)
            ->whereIn('status', ['approved', 'rescheduled', 'arrived', 'triaged', 'registered', 'done'])
            ->count();

        // 4. Walk-in Consultations Today (non-appointment consultations)
        $walkInsToday = \App\Models\Consultation::whereDate('consultation_date', $today)
            ->where(function ($q) {
                $q->whereNull('pre_triage_id')
                  ->orWhereDoesntHave('preTriage', function ($pt) {
                      $pt->whereNotNull('appointment_id');
                  });
            })
            ->count();

        $totalVisitsToday = $walkInsToday + $todayAppointments;

        // 5. Approved Bookings for the viewed month
        $approvedBookings = Appointment::whereYear('preferred_date', $year)
            ->whereMonth('preferred_date', $month)
            ->whereIn('status', ['approved', 'rescheduled'])
            ->count();

        // 6. Total Scheduled Bookings for the viewed month
        $totalScheduled = Appointment::whereYear('preferred_date', $year)
            ->whereMonth('preferred_date', $month)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        return [
            'today' => $totalVisitsToday,
            'approved' => $approvedBookings,
            'registered' => $activeQueue,
            'done' => $completedToday,
            'total' => $totalScheduled,
        ];
    }

    /**
     * API Endpoint to fetch appointments for FullCalendar.
     * Expects 'start' and 'end' query parameters from FullCalendar.
     */
    public function getAppointments(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $query = Appointment::query()
            ->whereIn('status', ['approved', 'rescheduled', 'registered', 'triaged', 'done', 'no_show', 'cancelled']);

        if ($start) {
            $query->whereDate('preferred_date', '>=', Carbon::parse($start));
        }

        if ($end) {
            $query->whereDate('preferred_date', '<=', Carbon::parse($end));
        }

        $appointments = tap($query->get(), function ($appts) {
            // log count if needed
        });

        $events = $appointments->map(function ($appointment) {
            // Determine event color based on status or type
            $color = '#0d9488'; // Default teal (Approved)
            if ($appointment->status === 'rescheduled') {
                $color = '#7c3aed'; // Violet-600
            } elseif ($appointment->status === 'registered' || $appointment->status === 'triaged') {
                $color = '#ca8a04'; // Yellow-600
            } elseif ($appointment->status === 'done') {
                $color = '#16a34a'; // Green-600
            } elseif ($appointment->status === 'no_show') {
                $color = '#e11d48'; // Rose-600
            } elseif ($appointment->status === 'cancelled') {
                $color = '#64748b'; // Slate-500
            }

            return [
                'id' => $appointment->id,
                'title' => $appointment->first_name.' '.$appointment->last_name.' ('.ucfirst($appointment->type).')',
                'start' => \Carbon\Carbon::parse($appointment->preferred_date)->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'patient_name' => $appointment->first_name.' '.$appointment->last_name,
                    'type' => ucfirst($appointment->type),
                    'classification' => current(explode(',', $appointment->classification)), // Handle cases where it might be array-like strings or just string
                    'status' => ucfirst(str_replace('_', ' ', $appointment->status)),
                    'raw_status' => $appointment->status,
                    'reason' => $appointment->complaint ?? 'Consultation',
                    'contact' => $appointment->guardian_contact ?? $appointment->contact_number ?? '',
                    'time' => $appointment->preferred_time ?: \Carbon\Carbon::parse($appointment->preferred_date)->format('h:i A'),
                    'email' => $appointment->email,
                    'reference_number' => $appointment->reference_number,
                    'cancellation_reason' => $appointment->cancellation_reason,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Display the Queue Overview Dashboard.
     */
    public function queueOverview()
    {
        $today = Carbon::today();

        // Fetch all active consultations for today, sorted by priority (Senior/PWD first) then arrival time
        $consultations = \App\Models\Consultation::select('consultations.*')
            ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->whereDate('consultations.created_at', $today)
            ->whereIn('consultations.status', ['queued', 'called', 'active', 'awaiting_results'])
            ->with(['patient', 'doctor', 'nurse', 'preTriage', 'ancillaryRequests'])
            ->orderByRaw("CASE WHEN patients.classification IN ('Senior Citizen', 'PWD') THEN 1 ELSE 2 END")
            ->orderBy('consultations.created_at', 'asc')
            ->get();

        // Group by Doctor/Nurse ID
        $queuesByStaff = [];
        $unassigned = [];

        foreach ($consultations as $consultation) {
            if ($consultation->doctor_id) {
                $staffId = 'doc_'.$consultation->doctor_id;
                if (! isset($queuesByStaff[$staffId])) {
                    $queuesByStaff[$staffId] = [
                        'staff' => $consultation->doctor,
                        'role' => 'Doctor',
                        'patients' => [],
                    ];
                }
                $queuesByStaff[$staffId]['patients'][] = $consultation;
            } elseif ($consultation->nurse_id) {
                $staffId = 'nurse_'.$consultation->nurse_id;
                if (! isset($queuesByStaff[$staffId])) {
                    $queuesByStaff[$staffId] = [
                        'staff' => $consultation->nurse,
                        'role' => 'Nurse',
                        'patients' => [],
                    ];
                }
                $queuesByStaff[$staffId]['patients'][] = $consultation;
            } else {
                $unassigned[] = $consultation;
            }
        }

        // Fetch all Ancillary Requests for today (Laboratory and Radiology)
        $ancillaryRequests = \App\Models\AncillaryRequest::select('ancillary_requests.*')
            ->join('consultations', 'ancillary_requests.consultation_id', '=', 'consultations.id')
            ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->whereDate('ancillary_requests.created_at', $today)
            ->with(['consultation.patient', 'consultation.doctor'])
            ->orderByRaw("CASE WHEN patients.classification IN ('Senior Citizen', 'PWD') THEN 1 ELSE 2 END")
            ->orderBy('ancillary_requests.created_at', 'asc')
            ->get();

        // Helper to group ancillary requests by patient (consultation_id)
        $buildPatientQueue = function ($requests) {
            return $requests->groupBy('consultation_id')->map(function ($patientRequests) {
                $first = $patientRequests->first();
                $consultation = $first->consultation;
                $patient = $consultation ? $consultation->patient : null;

                $activeRequests = $patientRequests->filter(function ($r) {
                    return in_array($r->status, ['Pending', 'Specimen Collected', 'In Progress']);
                })->values();

                $doneRequests = $patientRequests->filter(function ($r) {
                    return $r->status === 'Done';
                })->values();

                $dismissedRequests = $patientRequests->filter(function ($r) {
                    return in_array($r->status, ['Cancelled', 'Rejected']);
                })->values();

                // Determine primary overall status for patient badge display
                if ($activeRequests->contains(fn($r) => $r->status === 'In Progress')) {
                    $status = 'In Progress';
                } elseif ($activeRequests->contains(fn($r) => $r->status === 'Specimen Collected')) {
                    $status = 'Specimen Collected';
                } elseif ($activeRequests->isNotEmpty()) {
                    $status = 'Pending';
                } elseif ($doneRequests->isNotEmpty()) {
                    $status = 'Done';
                } else {
                    $status = $first->status;
                }

                $createdAt = $patientRequests->min('created_at');
                $waitMinutes = $createdAt ? Carbon::parse($createdAt)->diffInMinutes(now()) : 0;
                $isPriority = $patient && in_array($patient->classification, ['Senior Citizen', 'PWD']);

                return (object) [
                    'consultation_id'   => $first->consultation_id,
                    'consultation'      => $consultation,
                    'patient'           => $patient,
                    'queue_number'      => $consultation->queue_number ?? ($patient->patient_id ?? 'N/A'),
                    'p_id'              => $patient->patient_id ?? 'N/A',
                    'first_name'        => $patient ? $patient->first_name : '',
                    'last_name'         => $patient ? $patient->last_name : '',
                    'classification'    => $patient ? $patient->classification : 'Regular Adult',
                    'status'            => $status,
                    'created_at'        => $createdAt ? Carbon::parse($createdAt) : now(),
                    'wait_minutes'      => $waitMinutes,
                    'is_priority'       => $isPriority,
                    'active_requests'   => $activeRequests,
                    'done_requests'     => $doneRequests,
                    'dismissed_requests'=> $dismissedRequests,
                    'all_requests'      => $patientRequests,
                    'total_count'       => $patientRequests->count(),
                    'active_count'      => $activeRequests->count(),
                    'done_count'        => $doneRequests->count(),
                    'dismissed_count'   => $dismissedRequests->count(),
                ];
            })->values();
        };

        $labRequests = $ancillaryRequests->where('type', 'Laboratory');
        $radRequests = $ancillaryRequests->where('type', 'Radiology');

        $labQueue = $buildPatientQueue($labRequests);
        $radQueue = $buildPatientQueue($radRequests);

        // Fetch Pharmacy Prescriptions for today, grouped by patient
        $prescriptions = \App\Models\Prescription::select('prescriptions.*')
            ->join('consultations', 'prescriptions.consultation_id', '=', 'consultations.id')
            ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->whereDate('prescriptions.created_at', $today)
            ->with(['consultation.patient'])
            ->orderByRaw("CASE WHEN patients.classification IN ('Senior Citizen', 'PWD') THEN 1 ELSE 2 END")
            ->orderBy('prescriptions.created_at', 'asc')
            ->get();

        $pharmacyQueue = $prescriptions->groupBy('consultation_id')->map(function ($patientPrescriptions) {
            $first = $patientPrescriptions->first();
            $consultation = $first->consultation;
            $patient = $consultation ? $consultation->patient : null;
            $createdAt = $patientPrescriptions->min('created_at');
            $waitMinutes = $createdAt ? Carbon::parse($createdAt)->diffInMinutes(now()) : 0;
            $isPriority = $patient && in_array($patient->classification, ['Senior Citizen', 'PWD']);

            return (object) [
                'consultation_id'   => $first->consultation_id,
                'consultation'      => $consultation,
                'patient'           => $patient,
                'queue_number'      => $consultation->queue_number ?? ($patient->patient_id ?? 'N/A'),
                'p_id'              => $patient->patient_id ?? 'N/A',
                'first_name'        => $patient ? $patient->first_name : '',
                'last_name'         => $patient ? $patient->last_name : '',
                'classification'    => $patient ? $patient->classification : 'Regular Adult',
                'status'            => $first->status,
                'created_at'        => $createdAt ? Carbon::parse($createdAt) : now(),
                'wait_minutes'      => $waitMinutes,
                'is_priority'       => $isPriority,
                'prescriptions'     => $patientPrescriptions,
            ];
        })->values();

        return view('frontdesk.queue-overview', compact('queuesByStaff', 'unassigned', 'labQueue', 'radQueue', 'pharmacyQueue'));
    }
}
