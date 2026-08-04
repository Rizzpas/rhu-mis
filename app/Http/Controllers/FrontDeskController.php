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

        return view('frontdesk.dashboard', compact('staffGroups'));
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
            ->whereIn('status', ['approved', 'rescheduled', 'registered', 'triaged', 'done']);

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
                    'reason' => $appointment->complaint ?? 'Consultation',
                    'contact' => $appointment->guardian_contact ?? '',
                    'time' => $appointment->preferred_time ?: \Carbon\Carbon::parse($appointment->preferred_date)->format('h:i A'),
                    'email' => $appointment->email,
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
            ->whereIn('consultations.status', ['queued', 'active', 'awaiting_results'])
            ->with(['patient', 'doctor', 'nurse', 'preTriage'])
            ->orderByRaw("CASE WHEN patients.classification IN ('Senior Citizen', 'PWD') THEN 1 ELSE 2 END")
            ->orderBy('consultations.created_at', 'asc')
            ->get();

        // Group by Doctor/Nurse ID
        // Note: some consultations might be pending assignment.
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
        // Fetch Laboratory and Radiology Queues
        $ancillaryQueues = \App\Models\AncillaryRequest::select('ancillary_requests.*', 'patients.first_name', 'patients.last_name', 'patients.patient_id as p_id', 'patients.classification', 'consultations.queue_number')
            ->join('consultations', 'ancillary_requests.consultation_id', '=', 'consultations.id')
            ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->whereDate('ancillary_requests.created_at', $today)
            ->whereIn('ancillary_requests.status', ['pending', 'in_progress'])
            ->orderByRaw("CASE WHEN patients.classification IN ('Senior Citizen', 'PWD') THEN 1 ELSE 2 END")
            ->orderBy('ancillary_requests.created_at', 'asc')
            ->get();

        $labQueue = $ancillaryQueues->where('department', 'laboratory')->values();
        $radQueue = $ancillaryQueues->where('department', 'radiology')->values();

        // Fetch Pharmacy Queues
        $pharmacyQueue = \App\Models\Prescription::select('prescriptions.*', 'patients.first_name', 'patients.last_name', 'patients.patient_id as p_id', 'patients.classification', 'consultations.queue_number')
            ->join('consultations', 'prescriptions.consultation_id', '=', 'consultations.id')
            ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->whereDate('prescriptions.created_at', $today)
            ->where('prescriptions.status', 'pending')
            ->orderByRaw("CASE WHEN patients.classification IN ('Senior Citizen', 'PWD') THEN 1 ELSE 2 END")
            ->orderBy('prescriptions.created_at', 'asc')
            ->get();

        return view('frontdesk.queue-overview', compact('queuesByStaff', 'unassigned', 'labQueue', 'radQueue', 'pharmacyQueue'));
    }
}
