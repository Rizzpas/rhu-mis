<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\InventoryLog;
use App\Models\Patient;
use App\Rules\SecureImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $announcements = Announcement::latest()->take(5)->get();
        $recentLogs = AuditLog::with('user')->latest()->take(10)->get();

        $timeFilter = $request->get('time_filter', 'monthly'); // all, monthly, weekly

        // Base Query Scopes based on time filter
        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => \Carbon\Carbon::create(2000, 1, 1), // all time fallback
        };
        $previousStartDate = match ($timeFilter) {
            'today' => now()->subDay()->startOfDay(),
            'weekly' => now()->subWeek()->startOfWeek(),
            'monthly' => now()->subMonth()->startOfMonth(),
            'yearly' => now()->subYear()->startOfYear(),
            default => \Carbon\Carbon::create(2000, 1, 1),
        };
        $previousEndDate = match ($timeFilter) {
            'today' => now()->subDay()->endOfDay(),
            'weekly' => now()->subWeek()->endOfWeek(),
            'monthly' => now()->subMonth()->endOfMonth(),
            'yearly' => now()->subYear()->endOfYear(),
            default => \Carbon\Carbon::create(2000, 1, 1),
        };

        // Statistics (Initial Load)
        $totalDoctors = \App\Models\User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->count();
        $presentDoctors = \App\Models\User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->present()->count();
        $presentNurses = \App\Models\User::whereIn('role', ['nurse', 'clinical_nurse', 'vitals_nurse'])->present()->count();
        $todayAppointments = Appointment::whereDate('preferred_date', today())->count();
        $pendingDeletionCount = \App\Models\Patient::whereNotNull('expires_at')->where('expires_at', '<=', now())->count();

        // Trend Analytics: Current vs Previous Period
        $currentPeriodConsultations = \App\Models\Consultation::where('created_at', '>=', $startDate)->count();
        $previousPeriodConsultations = \App\Models\Consultation::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();

        $trendPercentage = 0;
        if ($previousPeriodConsultations > 0) {
            $trendPercentage = round((($currentPeriodConsultations - $previousPeriodConsultations) / $previousPeriodConsultations) * 100, 1);
        } elseif ($currentPeriodConsultations > 0) {
            $trendPercentage = 100; // 100% growth if previous was 0
        }

        // 1. Visit Volumes (Dynamic based on selected timeframe)
        $visitVolumeData = $this->getVisitVolumeData($timeFilter);

        // 2. Peak Hours (Filtered by Time)
        $peakHoursData = $this->getPeakHoursData($startDate);

        // 3. Top 5 Diagnoses (Filtered)
        $topDiagnoses = \App\Models\Consultation::whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->where('created_at', '>=', $startDate)
            ->select(\Illuminate\Support\Facades\DB::raw('diagnosis, COUNT(id) as count'))
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // 4. Classification Breakdown (Filtered)
        // Get patients who had a consultation in this period, and count by their classification
        $classificationData = \App\Models\Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->whereNotNull('patients.classification')
            ->select(\Illuminate\Support\Facades\DB::raw('patients.classification, COUNT(DISTINCT consultations.patient_id) as count'))
            ->groupBy('patients.classification')
            ->pluck('count', 'classification');

        // 5. Triage Severity (Filtered)
        $severityData = \App\Models\Consultation::select(\Illuminate\Support\Facades\DB::raw('severity, COUNT(id) as count'))
            ->whereNotNull('severity')
            ->where('created_at', '>=', $startDate)
            ->groupBy('severity')
            ->pluck('count', 'severity');

        // 6. Average Wait Time (Filtered)
        $avgWaitTimeRaw = \App\Models\Consultation::whereNotNull('consultation_start_time')
            ->where('created_at', '>=', $startDate)
            ->select(\Illuminate\Support\Facades\DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait_time'))
            ->value('avg_wait_time');

        $avgWaitTime = $avgWaitTimeRaw ? round($avgWaitTimeRaw) : 0;

        // 7. Barangay Heatmap (Filtered)
        $barangayData = $this->getBarangayData($startDate);

        // 8. Age-Sex Demographics (Filtered)
        $demographics = \Illuminate\Support\Facades\DB::select("
            SELECT 
                p.sex,
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 0 AND 12 THEN '0-12 (Pediatric)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 (Teen)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 18 AND 39 THEN '18-39 (Adult)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 40 AND 59 THEN '40-59 (Middle Age)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) >= 60 THEN '60+ (Senior)'
                    ELSE 'Unknown'
                END as age_group,
                COUNT(DISTINCT c.patient_id) as count
            FROM consultations c
            JOIN patients p ON c.patient_id = p.patient_id
            WHERE c.created_at >= ?
            AND p.sex IS NOT NULL
            AND p.dob IS NOT NULL
            GROUP BY p.sex, age_group
        ", [$startDate]);

        $demoData = [
            'labels' => ['0-12 (Pediatric)', '13-17 (Teen)', '18-39 (Adult)', '40-59 (Middle Age)', '60+ (Senior)'],
            'Male' => [0, 0, 0, 0, 0],
            'Female' => [0, 0, 0, 0, 0],
        ];

        foreach ($demographics as $d) {
            $idx = array_search($d->age_group, $demoData['labels']);
            if ($idx !== false && isset($demoData[$d->sex])) {
                $demoData[$d->sex][$idx] = $d->count;
            }
        }

        // 9. Doctor/Nurse Workload (Filtered)
        $workloadData = \App\Models\Consultation::select(\Illuminate\Support\Facades\DB::raw('
                COALESCE(doctor_id, nurse_id) as staff_id, 
                COUNT(id) as count
            '))
            ->where('created_at', '>=', $startDate)
            ->where(function ($q) {
                $q->whereNotNull('doctor_id')->orWhereNotNull('nurse_id');
            })
            ->groupBy('staff_id')
            ->get();

        $staffIds = $workloadData->pluck('staff_id')->filter();
        $staffNames = \App\Models\User::whereIn('id', $staffIds)->get()->pluck('formatted_name', 'id');

        $workloadFormatted = [];
        foreach ($workloadData as $row) {
            if ($row->staff_id && isset($staffNames[$row->staff_id])) {
                $workloadFormatted[$staffNames[$row->staff_id]] = $row->count;
            }
        }
        arsort($workloadFormatted);
        $workloadFormatted = array_slice($workloadFormatted, 0, 10);

        // 10. Follow-up Compliance (All Time up to 30 days ago)
        // Find consultations that required a follow-up
        $totalFollowupsNeeded = \App\Models\Consultation::where('is_followup_needed', true)
            ->where('created_at', '<', now()->subDays(30)) // Look at past data to give them 30 days to return
            ->count();

        $compliantFollowups = 0;
        if ($totalFollowupsNeeded > 0) {
            // Count how many of those patients actually had another consultation within 30 days
            $compliantFollowups = \Illuminate\Support\Facades\DB::select('
                SELECT COUNT(DISTINCT c1.id) as compliant
                FROM consultations c1
                JOIN consultations c2 ON c1.patient_id = c2.patient_id 
                    AND c2.created_at > c1.created_at 
                    AND c2.created_at <= DATE_ADD(c1.created_at, INTERVAL 30 DAY)
                WHERE c1.is_followup_needed = 1 
                AND c1.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
            ')[0]->compliant;
        }

        $complianceRate = $totalFollowupsNeeded > 0 ? round(($compliantFollowups / $totalFollowupsNeeded) * 100) : 0;

        // 11. Pharmacy Analytics: Top 10 Most Dispensed Medicines (last 30 days)
        $topDispensed = InventoryLog::where('action', 'Dispensed')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('medicine_id', DB::raw('SUM(ABS(quantity_changed)) as total_dispensed'))
            ->groupBy('medicine_id')
            ->orderByDesc('total_dispensed')
            ->limit(10)
            ->with('medicine:id,name,generic_name')
            ->get();

        // 12. Pharmacy Analytics: Monthly Dispensing Trend (last 6 months)
        $monthlyTrend = InventoryLog::where('action', 'Dispensed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(ABS(quantity_changed)) as total_dispensed')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $item->label = \Carbon\Carbon::create($item->year, $item->month)->format('M Y');
                return $item;
            });

        return view('admin.dashboard', compact(
            'announcements',
            'timeFilter',
            'trendPercentage',
            'currentPeriodConsultations',
            'totalDoctors',
            'presentDoctors',
            'todayAppointments',
            'pendingDeletionCount',
            'visitVolumeData',
            'peakHoursData',
            'presentNurses',
            'topDiagnoses',
            'classificationData',
            'severityData',
            'avgWaitTime',
            'barangayData',
            'demoData',
            'workloadFormatted',
            'complianceRate',
            'totalFollowupsNeeded',
            'topDispensed',
            'monthlyTrend'
        ));
    }

    public function analytics(Request $request)
    {
        $timeFilter = $request->get('time_filter', 'monthly'); // all, monthly, weekly, today

        // Base Query Scopes based on time filter
        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => \Carbon\Carbon::create(2000, 1, 1), // all time fallback
        };

        // 1. Visit Volumes (Dynamic based on selected timeframe)
        $visitVolumeData = $this->getVisitVolumeData($timeFilter);

        // 2. Peak Hours
        $peakHoursData = $this->getPeakHoursData($startDate);

        // 3. Top Diagnoses
        $topDiagnoses = \App\Models\Consultation::whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->where('created_at', '>=', $startDate)
            ->select(\Illuminate\Support\Facades\DB::raw('diagnosis, COUNT(id) as count'))
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // 4. Classification Breakdown
        $classificationData = \App\Models\Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->whereNotNull('patients.classification')
            ->select(\Illuminate\Support\Facades\DB::raw('patients.classification, COUNT(DISTINCT consultations.patient_id) as count'))
            ->groupBy('patients.classification')
            ->pluck('count', 'classification');

        // 5. Triage Severity
        $severityData = \App\Models\Consultation::select(\Illuminate\Support\Facades\DB::raw('severity, COUNT(id) as count'))
            ->whereNotNull('severity')
            ->where('created_at', '>=', $startDate)
            ->groupBy('severity')
            ->pluck('count', 'severity');

        // 6. Barangay Heatmap
        $barangayData = $this->getBarangayData($startDate);

        // 7. Age-Sex Demographics
        $demographics = \Illuminate\Support\Facades\DB::select("
            SELECT 
                p.sex,
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 0 AND 12 THEN '0-12 (Pediatric)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 (Teen)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 18 AND 39 THEN '18-39 (Adult)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 40 AND 59 THEN '40-59 (Middle Age)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) >= 60 THEN '60+ (Senior)'
                    ELSE 'Unknown'
                END as age_group,
                COUNT(DISTINCT c.patient_id) as count
            FROM consultations c
            JOIN patients p ON c.patient_id = p.patient_id
            WHERE c.created_at >= ?
            AND p.sex IS NOT NULL
            AND p.dob IS NOT NULL
            GROUP BY p.sex, age_group
        ", [$startDate]);

        $demoData = [
            'labels' => ['0-12 (Pediatric)', '13-17 (Teen)', '18-39 (Adult)', '40-59 (Middle Age)', '60+ (Senior)'],
            'Male' => [0, 0, 0, 0, 0],
            'Female' => [0, 0, 0, 0, 0],
        ];

        foreach ($demographics as $d) {
            $idx = array_search($d->age_group, $demoData['labels']);
            if ($idx !== false && isset($demoData[$d->sex])) {
                $demoData[$d->sex][$idx] = $d->count;
            }
        }

        // 8. Workload
        $workloadData = \App\Models\Consultation::select(\Illuminate\Support\Facades\DB::raw('
                COALESCE(doctor_id, nurse_id) as staff_id, 
                COUNT(id) as count
            '))
            ->where('created_at', '>=', $startDate)
            ->where(function ($q) {
                $q->whereNotNull('doctor_id')->orWhereNotNull('nurse_id');
            })
            ->groupBy('staff_id')
            ->get();

        $staffIds = $workloadData->pluck('staff_id')->filter();
        $staffNames = \App\Models\User::whereIn('id', $staffIds)->get()->pluck('formatted_name', 'id');

        $workloadFormatted = [];
        foreach ($workloadData as $row) {
            if ($row->staff_id && isset($staffNames[$row->staff_id])) {
                $workloadFormatted[$staffNames[$row->staff_id]] = $row->count;
            }
        }
        arsort($workloadFormatted);
        $workloadFormatted = array_slice($workloadFormatted, 0, 10);

        // Staff Productivity Base Data
        $staffList = \App\Models\User::whereIn('role', ['regular_doctor', 'pedia_doctor', 'nurse', 'clinical_nurse', 'vitals_nurse'])
            ->orderBy('name')
            ->get();
        $staffProductivity = $this->getStaffProductivityData('all', $startDate);

        return view('admin.analytics.index', compact(
            'timeFilter',
            'visitVolumeData',
            'peakHoursData',
            'topDiagnoses',
            'classificationData',
            'severityData',
            'barangayData',
            'demoData',
            'workloadFormatted',
            'staffList',
            'staffProductivity'
        ));
    }

    public function exportAnalyticsCsv(Request $request)
    {
        $timeFilter = $request->get('time_filter', 'monthly');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => \Carbon\Carbon::create(2000, 1, 1),
        };

        // Build the same query scope used by analytics dashboard
        $query = \App\Models\Consultation::query()
            ->where('consultations.created_at', '>=', $startDate)
            ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->leftJoin('users as doctors', 'consultations.doctor_id', '=', 'doctors.id')
            ->leftJoin('users as nurses', 'consultations.nurse_id', '=', 'nurses.id')
            ->leftJoin('pre_triages', 'consultations.pre_triage_id', '=', 'pre_triages.id')
            ->select([
                'consultations.id as consultation_id',
                'consultations.patient_id',
                'consultations.consultation_date',
                'consultations.created_at as consultation_created_at',
                'consultations.queue_number',
                'consultations.status',
                'consultations.severity',
                'consultations.diagnosis',
                'consultations.blood_pressure',
                'consultations.temperature',
                'consultations.weight',
                'consultations.height',
                'consultations.heart_rate',
                'consultations.respiratory_rate',
                'consultations.pulse_rate',
                'consultations.spo2',
                'consultations.consultation_start_time',
                'consultations.consultation_end_time',
                'consultations.is_followup_needed',
                'consultations.followup_date',
                'consultations.prescription',
                'consultations.medical_notes',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'patients.sex as patient_sex',
                'patients.dob as patient_dob',
                'patients.classification as patient_classification',
                'patients.barangay as patient_barangay',
                'patients.address as patient_address',
                'doctors.name as doctor_name',
                'nurses.name as nurse_name',
                'pre_triages.chief_complaint',
                'pre_triages.encoding_duration_seconds as triage_encoding_seconds',
            ])
            ->orderBy('consultations.created_at', 'desc');

        $count = $query->count();

        if ($count === 0) {
            return response()->json(['message' => 'No analytics data found for the selected timeframe.'], 404);
        }

        $timeFilterLabel = match ($timeFilter) {
            'today' => now()->format('Y-m-d'),
            'weekly' => now()->startOfWeek()->format('Y-m-d') . '_to_' . now()->endOfWeek()->format('Y-m-d'),
            'monthly' => now()->format('Y-m'),
            'yearly' => now()->format('Y'),
            default => 'all-time',
        };

        $filename = "detailed-analytics-{$timeFilterLabel}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store, no-cache',
        ];

        $csvHeaders = [
            'Consultation ID',
            'Patient ID',
            'Patient Name',
            'Sex',
            'Date of Birth',
            'Age',
            'Classification',
            'Barangay',
            'Consultation Date',
            'Created At',
            'Queue Number',
            'Status',
            'Severity',
            'Chief Complaint',
            'Diagnosis',
            'Prescription',
            'Clinical Notes / Addenda',
            'Doctor',
            'Nurse',
            'Consultation Start',
            'Consultation End',
            'Duration (min)',
            'Wait Time (min)',
            'Blood Pressure',
            'Temperature',
            'Weight (kg)',
            'Height (cm)',
            'Heart Rate',
            'Respiratory Rate',
            'Pulse Rate',
            'SpO2',
            'Follow-up Needed',
            'Follow-up Date',
            'Triage Encoding (sec)',
        ];

        $callback = function () use ($query, $csvHeaders) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, $csvHeaders);

            $query->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    // Calculate age from DOB
                    $age = '';
                    if ($row->patient_dob) {
                        try {
                            $age = \Carbon\Carbon::parse($row->patient_dob)->age;
                        } catch (\Exception $e) {
                            $age = '';
                        }
                    }

                    // Calculate consultation duration in minutes
                    $durationMin = '';
                    if ($row->consultation_start_time && $row->consultation_end_time) {
                        try {
                            $durationMin = round(
                                \Carbon\Carbon::parse($row->consultation_start_time)
                                    ->diffInMinutes(\Carbon\Carbon::parse($row->consultation_end_time)),
                                1
                            );
                        } catch (\Exception $e) {
                            $durationMin = '';
                        }
                    }

                    // Calculate wait time (created_at -> consultation_start_time)
                    $waitTimeMin = '';
                    if ($row->consultation_created_at && $row->consultation_start_time) {
                        try {
                            $waitTimeMin = round(
                                \Carbon\Carbon::parse($row->consultation_created_at)
                                    ->diffInMinutes(\Carbon\Carbon::parse($row->consultation_start_time)),
                                1
                            );
                        } catch (\Exception $e) {
                            $waitTimeMin = '';
                        }
                    }

                    // Normalize barangay using existing logic
                    $barangay = \App\Models\Patient::normalizeBarangay($row->patient_barangay, $row->patient_address);

                    $patientName = trim(($row->patient_first_name ?? '') . ' ' . ($row->patient_last_name ?? ''));

                    fputcsv($handle, [
                        $row->consultation_id,
                        $row->patient_id,
                        $patientName,
                        $row->patient_sex ?? '',
                        $row->patient_dob ? \Carbon\Carbon::parse($row->patient_dob)->format('Y-m-d') : '',
                        $age,
                        $row->patient_classification ?? '',
                        $barangay ?? '',
                        $row->consultation_date ? \Carbon\Carbon::parse($row->consultation_date)->format('Y-m-d') : '',
                        $row->consultation_created_at ? \Carbon\Carbon::parse($row->consultation_created_at)->format('Y-m-d H:i:s') : '',
                        $row->queue_number ?? '',
                        ucfirst($row->status ?? ''),
                        ucfirst($row->severity ?? ''),
                        $row->chief_complaint ?? '',
                        $row->diagnosis ?? '',
                        $row->prescription ?? '',
                        $row->medical_notes ?? '',
                        $row->doctor_name ?? '',
                        $row->nurse_name ?? '',
                        $row->consultation_start_time ? \Carbon\Carbon::parse($row->consultation_start_time)->format('Y-m-d H:i:s') : '',
                        $row->consultation_end_time ? \Carbon\Carbon::parse($row->consultation_end_time)->format('Y-m-d H:i:s') : '',
                        $durationMin,
                        $waitTimeMin,
                        $row->blood_pressure ?? '',
                        $row->temperature ?? '',
                        $row->weight ?? '',
                        $row->height ?? '',
                        $row->heart_rate ?? '',
                        $row->respiratory_rate ?? '',
                        $row->pulse_rate ?? '',
                        $row->spo2 ?? '',
                        $row->is_followup_needed ? 'Yes' : 'No',
                        $row->followup_date ? \Carbon\Carbon::parse($row->followup_date)->format('Y-m-d') : '',
                        $row->triage_encoding_seconds ?? '',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAnalyticsSummaryCsv(Request $request)
    {
        $timeFilter = $request->get('time_filter', 'monthly');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => \Carbon\Carbon::create(2000, 1, 1),
        };

        $timeFilterLabel = match ($timeFilter) {
            'today' => now()->format('Y-m-d'),
            'weekly' => now()->startOfWeek()->format('Y-m-d') . '_to_' . now()->endOfWeek()->format('Y-m-d'),
            'monthly' => now()->format('Y-m'),
            'yearly' => now()->format('Y'),
            default => 'all-time',
        };

        $humanTimeframe = match ($timeFilter) {
            'today' => 'Today (' . now()->format('F d, Y') . ')',
            'weekly' => 'This Week (' . now()->startOfWeek()->format('M d') . ' - ' . now()->endOfWeek()->format('M d, Y') . ')',
            'monthly' => 'This Month (' . now()->format('F Y') . ')',
            'yearly' => 'This Year (' . now()->format('Y') . ')',
            default => 'All Historical Records',
        };

        // 1. Visit Volume
        $visitVolumeData = $this->getVisitVolumeData($timeFilter, $startDate);

        // 2. Peak Hours
        $peakHoursData = $this->getPeakHoursData($startDate);

        // 3. Top Diagnoses
        $topDiagnoses = \App\Models\Consultation::whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->where('created_at', '>=', $startDate)
            ->select(\Illuminate\Support\Facades\DB::raw('diagnosis, COUNT(id) as count'))
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // 4. Patient Classification
        $classificationData = \App\Models\Patient::join('consultations', 'patients.patient_id', '=', 'consultations.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->whereNotNull('patients.classification')
            ->select('patients.classification', \Illuminate\Support\Facades\DB::raw('COUNT(consultations.id) as count'))
            ->groupBy('patients.classification')
            ->pluck('count', 'classification');

        // 5. Triage Severity
        $severityData = \App\Models\Consultation::select(\Illuminate\Support\Facades\DB::raw('severity, COUNT(id) as count'))
            ->whereNotNull('severity')
            ->where('created_at', '>=', $startDate)
            ->groupBy('severity')
            ->pluck('count', 'severity');

        // 6. Barangay Heatmap
        $barangayData = $this->getBarangayData($startDate);

        // 7. Demographics
        $demographics = \Illuminate\Support\Facades\DB::select("
            SELECT 
                p.sex,
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 0 AND 12 THEN '0-12 (Pediatric)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 (Teen)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 18 AND 39 THEN '18-39 (Adult)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 40 AND 59 THEN '40-59 (Middle Age)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) >= 60 THEN '60+ (Senior)'
                    ELSE 'Unknown'
                END as age_group,
                COUNT(DISTINCT c.patient_id) as count
            FROM consultations c
            JOIN patients p ON c.patient_id = p.patient_id
            WHERE c.created_at >= ?
            AND p.sex IS NOT NULL
            AND p.dob IS NOT NULL
            GROUP BY p.sex, age_group
        ", [$startDate]);

        $demoLabels = ['0-12 (Pediatric)', '13-17 (Teen)', '18-39 (Adult)', '40-59 (Middle Age)', '60+ (Senior)'];
        $demoData = [
            'Male' => [0, 0, 0, 0, 0],
            'Female' => [0, 0, 0, 0, 0],
        ];
        foreach ($demographics as $d) {
            $idx = array_search($d->age_group, $demoLabels);
            if ($idx !== false && isset($demoData[$d->sex])) {
                $demoData[$d->sex][$idx] = $d->count;
            }
        }

        // 8. Workload
        $workloadData = \App\Models\Consultation::select(\Illuminate\Support\Facades\DB::raw('
                COALESCE(doctor_id, nurse_id) as staff_id, 
                COUNT(id) as count
            '))
            ->where('created_at', '>=', $startDate)
            ->where(function ($q) {
                $q->whereNotNull('doctor_id')->orWhereNotNull('nurse_id');
            })
            ->groupBy('staff_id')
            ->get();

        $staffIds = $workloadData->pluck('staff_id')->filter();
        $staffNames = \App\Models\User::whereIn('id', $staffIds)->get()->pluck('formatted_name', 'id');

        $workloadFormatted = [];
        foreach ($workloadData as $row) {
            if ($row->staff_id && isset($staffNames[$row->staff_id])) {
                $workloadFormatted[$staffNames[$row->staff_id]] = $row->count;
            }
        }
        arsort($workloadFormatted);

        // 9. Staff Productivity & Triage Encoding
        $staffProductivity = $this->getStaffProductivityData('all', $startDate);

        $totalConsultations = \App\Models\Consultation::where('created_at', '>=', $startDate)->count();

        $filename = "analytics-summary-graphs-{$timeFilterLabel}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store, no-cache',
        ];

        return response()->stream(function () use (
            $humanTimeframe,
            $totalConsultations,
            $visitVolumeData,
            $peakHoursData,
            $topDiagnoses,
            $classificationData,
            $severityData,
            $barangayData,
            $demoLabels,
            $demoData,
            $workloadFormatted,
            $staffProductivity
        ) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Metadata banner
            fputcsv($handle, ['RHU MANAGEMENT INFORMATION SYSTEM - ANALYTICS SUMMARY REPORT']);
            fputcsv($handle, ['Report Type', 'Aggregated Dashboard Graphs & Epidemiological Indicators']);
            fputcsv($handle, ['Reporting Timeframe', $humanTimeframe]);
            fputcsv($handle, ['Generated At', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, ['Generated By', auth()->user() ? auth()->user()->name : 'System Administrator']);
            fputcsv($handle, ['Total Consultations Recorded', $totalConsultations]);
            fputcsv($handle, ['Average Consultation Duration', ($staffProductivity['avgDuration'] ?? 0) . ' minutes']);
            fputcsv($handle, ['Average Queue Wait Time', ($staffProductivity['avgWaitTime'] ?? 0) . ' minutes']);
            fputcsv($handle, ['Throughput (Patients / Hour)', ($staffProductivity['patientsPerHour'] ?? 0) . ' pts/hr']);
            fputcsv($handle, []);

            // SECTION 1: VISIT VOLUME
            fputcsv($handle, ['=== 1. VISIT VOLUME OVER TIME ===']);
            fputcsv($handle, ['Period / Date', 'Consultation Count', 'Share of Total']);
            $volLabels = $visitVolumeData['labels'] ?? [];
            $volCounts = $visitVolumeData['data'] ?? [];
            $volSum = array_sum($volCounts);
            foreach ($volLabels as $i => $lbl) {
                $c = $volCounts[$i] ?? 0;
                $pct = $volSum > 0 ? round(($c / $volSum) * 100, 1) . '%' : '0%';
                fputcsv($handle, [$lbl, $c, $pct]);
            }
            fputcsv($handle, ['Total Period Volume', $volSum, '100%']);
            fputcsv($handle, []);

            // SECTION 2: PEAK OPERATING HOURS
            fputcsv($handle, ['=== 2. HOURLY PATIENT INFLUX & PEAK TRAFFIC ===']);
            fputcsv($handle, ['Operating Time Slot', 'Patient Check-ins', 'Share of Daily Flow', 'Traffic Density']);
            $peakLabels = $peakHoursData['labels'] ?? [];
            $peakCounts = $peakHoursData['data'] ?? [];
            $peakTotal = array_sum($peakCounts);
            $maxPeak = count($peakCounts) > 0 ? max($peakCounts) : 0;
            foreach ($peakLabels as $i => $lbl) {
                $c = $peakCounts[$i] ?? 0;
                $pct = $peakTotal > 0 ? round(($c / $peakTotal) * 100, 1) . '%' : '0%';
                $density = 'Normal';
                if ($c > 0 && $c == $maxPeak) $density = 'Peak Traffic Window';
                elseif ($c > ($maxPeak * 0.7)) $density = 'Heavy Traffic';
                elseif ($c < ($maxPeak * 0.3)) $density = 'Light Traffic';
                fputcsv($handle, [$lbl, $c, $pct, $density]);
            }
            fputcsv($handle, ['Total Clinic Check-ins', $peakTotal, '100%', '']);
            fputcsv($handle, []);

            // SECTION 3: TOP DIAGNOSES
            fputcsv($handle, ['=== 3. TOP CLINICAL DIAGNOSES (MORBIDITY RANKING) ===']);
            fputcsv($handle, ['Rank', 'Diagnosis / Morbidity', 'Diagnosed Cases', 'Prevalence Share']);
            $diagSum = $topDiagnoses->sum('count');
            if ($topDiagnoses->isEmpty()) {
                fputcsv($handle, ['—', 'No clinical diagnoses recorded for this period', 0, '0%']);
            } else {
                foreach ($topDiagnoses as $i => $row) {
                    $c = $row->count ?? 0;
                    $pct = $diagSum > 0 ? round(($c / $diagSum) * 100, 1) . '%' : '0%';
                    fputcsv($handle, [$i + 1, $row->diagnosis, $c, $pct]);
                }
            }
            fputcsv($handle, ['Total Ranked Cases', $diagSum, '100%']);
            fputcsv($handle, []);

            // SECTION 4: PATIENT CLASSIFICATION
            fputcsv($handle, ['=== 4. PATIENT SOCIOECONOMIC CLASSIFICATION ===']);
            fputcsv($handle, ['Classification Group', 'Patient Count', 'Distribution Share']);
            $classSum = $classificationData->sum();
            if ($classificationData->isEmpty()) {
                fputcsv($handle, ['No classification data recorded', 0, '0%']);
            } else {
                foreach ($classificationData as $grp => $c) {
                    $pct = $classSum > 0 ? round(($c / $classSum) * 100, 1) . '%' : '0%';
                    fputcsv($handle, [ucwords($grp), $c, $pct]);
                }
            }
            fputcsv($handle, ['Total Patients Classified', $classSum, '100%']);
            fputcsv($handle, []);

            // SECTION 5: TRIAGE SEVERITY
            fputcsv($handle, ['=== 5. TRIAGE SEVERITY & URGENCY DISTRIBUTION ===']);
            fputcsv($handle, ['Urgency Level', 'Triage Encounters', 'Urgency Share']);
            $sevSum = $severityData->sum();
            if ($severityData->isEmpty()) {
                fputcsv($handle, ['No triage records recorded', 0, '0%']);
            } else {
                foreach ($severityData as $sev => $c) {
                    $pct = $sevSum > 0 ? round(($c / $sevSum) * 100, 1) . '%' : '0%';
                    fputcsv($handle, [ucfirst($sev), $c, $pct]);
                }
            }
            fputcsv($handle, ['Total Triaged Encounters', $sevSum, '100%']);
            fputcsv($handle, []);

            // SECTION 6: BARANGAY GEOGRAPHIC DISTRIBUTION
            fputcsv($handle, ['=== 6. BARANGAY CATCHMENT & GEOGRAPHIC INFLUX ===']);
            fputcsv($handle, ['Rank', 'Barangay Name', 'Consultation Volume', 'Catchment Share']);
            $brgySum = array_sum($barangayData);
            if (empty($barangayData)) {
                fputcsv($handle, ['—', 'No geographic data available', 0, '0%']);
            } else {
                $rank = 1;
                foreach ($barangayData as $brgy => $c) {
                    $pct = $brgySum > 0 ? round(($c / $brgySum) * 100, 1) . '%' : '0%';
                    fputcsv($handle, [$rank++, $brgy, $c, $pct]);
                }
            }
            fputcsv($handle, ['Total Geocoded Volume', $brgySum, '100%']);
            fputcsv($handle, []);

            // SECTION 7: DEMOGRAPHICS (AGE & SEX)
            fputcsv($handle, ['=== 7. AGE & BIOLOGICAL SEX DEMOGRAPHIC DISTRIBUTION ===']);
            fputcsv($handle, ['Age Bracket', 'Male Patients', 'Female Patients', 'Total Patients', 'Demographic Share']);
            $totalDemoAll = 0;
            foreach ($demoLabels as $idx => $lbl) {
                $m = $demoData['Male'][$idx] ?? 0;
                $f = $demoData['Female'][$idx] ?? 0;
                $totalDemoAll += ($m + $f);
            }
            foreach ($demoLabels as $idx => $lbl) {
                $m = $demoData['Male'][$idx] ?? 0;
                $f = $demoData['Female'][$idx] ?? 0;
                $tot = $m + $f;
                $pct = $totalDemoAll > 0 ? round(($tot / $totalDemoAll) * 100, 1) . '%' : '0%';
                fputcsv($handle, [$lbl, $m, $f, $tot, $pct]);
            }
            fputcsv($handle, ['Total Demographic Count', array_sum($demoData['Male']), array_sum($demoData['Female']), $totalDemoAll, '100%']);
            fputcsv($handle, []);

            // SECTION 8: CLINICAL WORKLOAD
            fputcsv($handle, ['=== 8. CLINICAL STAFF WORKLOAD (PATIENTS HANDLED) ===']);
            fputcsv($handle, ['Practitioner Name', 'Consultations Completed', 'Workload Share']);
            $workloadSum = array_sum($workloadFormatted);
            if (empty($workloadFormatted)) {
                fputcsv($handle, ['No practitioner consultations logged', 0, '0%']);
            } else {
                foreach ($workloadFormatted as $staff => $c) {
                    $pct = $workloadSum > 0 ? round(($c / $workloadSum) * 100, 1) . '%' : '0%';
                    fputcsv($handle, [$staff, $c, $pct]);
                }
            }
            fputcsv($handle, ['Total Staff Encounters', $workloadSum, '100%']);
            fputcsv($handle, []);

            // SECTION 9: DOCTOR CONSULTATION DURATION
            fputcsv($handle, ['=== 9. AVERAGE CONSULTATION DURATION PER DOCTOR ===']);
            fputcsv($handle, ['Physician Name', 'Average Duration (Minutes)', 'Efficiency Standard']);
            $durLabels = $staffProductivity['durationChartLabels'] ?? [];
            $durData = $staffProductivity['durationChartData'] ?? [];
            if (empty($durLabels)) {
                fputcsv($handle, ['No physician consultation timings logged', 0, 'N/A']);
            } else {
                foreach ($durLabels as $i => $doc) {
                    $mins = $durData[$i] ?? 0;
                    $status = $mins <= 15 ? 'Optimal (<= 15 min)' : ($mins <= 30 ? 'Standard' : 'Extended (> 30 min)');
                    fputcsv($handle, [$doc, $mins . ' min', $status]);
                }
            }
            fputcsv($handle, []);

            // SECTION 10: TRIAGE ENCODING SPEED
            fputcsv($handle, ['=== 10. TRIAGE NURSE VITALS ENCODING SPEED ===']);
            fputcsv($handle, ['Triage Nurse', 'Avg Encoding Time (Seconds)', 'Avg Encoding Time (Minutes)', 'Target Benchmark']);
            $encLabels = $staffProductivity['encodingChartLabels'] ?? [];
            $encData = $staffProductivity['encodingChartData'] ?? [];
            if (empty($encLabels)) {
                fputcsv($handle, ['No triage encoding timing records logged', 0, 0, 'N/A']);
            } else {
                foreach ($encLabels as $i => $nurse) {
                    $secs = $encData[$i] ?? 0;
                    $mins = round($secs / 60, 1);
                    $target = $secs <= 180 ? 'Within Target (<= 3 min)' : 'Needs Improvement (> 3 min)';
                    fputcsv($handle, [$nurse, $secs . 's', $mins . ' min', $target]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function apiChartData(Request $request, $chart)
    {
        $timeFilter = $request->get('time_filter', 'monthly');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => \Carbon\Carbon::create(2000, 1, 1),
        };

        if ($chart === 'volume') {
            return response()->json($this->getVisitVolumeData($timeFilter));
        }

        if ($chart === 'peak') {
            return response()->json($this->getPeakHoursData($startDate));
        }

        if ($chart === 'classification') {
            $data = \App\Models\Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
                ->where('consultations.created_at', '>=', $startDate)
                ->whereNotNull('patients.classification')
                ->select(\Illuminate\Support\Facades\DB::raw('patients.classification, COUNT(DISTINCT consultations.patient_id) as count'))
                ->groupBy('patients.classification')
                ->pluck('count', 'classification');

            return response()->json(['labels' => array_keys($data->toArray()), 'data' => array_values($data->toArray())]);
        }

        if ($chart === 'severity') {
            $data = \App\Models\Consultation::select(\Illuminate\Support\Facades\DB::raw('severity, COUNT(id) as count'))
                ->whereNotNull('severity')
                ->where('created_at', '>=', $startDate)
                ->groupBy('severity')
                ->pluck('count', 'severity');

            $labels = array_map(function ($l) {
                return ucfirst($l);
            }, array_keys($data->toArray()));

            return response()->json(['labels' => $labels, 'data' => array_values($data->toArray())]);
        }

        if ($chart === 'barangay') {
            $data = $this->getBarangayData($startDate);

            return response()->json(['labels' => array_keys($data), 'data' => array_values($data)]);
        }

        if ($chart === 'demographics') {
            $demographics = \Illuminate\Support\Facades\DB::select("
                SELECT 
                    p.sex,
                    CASE
                        WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 0 AND 12 THEN '0-12 (Pediatric)'
                        WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 (Teen)'
                        WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 18 AND 39 THEN '18-39 (Adult)'
                        WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 40 AND 59 THEN '40-59 (Middle Age)'
                        WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) >= 60 THEN '60+ (Senior)'
                        ELSE 'Unknown'
                    END as age_group,
                    COUNT(DISTINCT c.patient_id) as count
                FROM consultations c
                JOIN patients p ON c.patient_id = p.patient_id
                WHERE c.created_at >= ?
                AND p.sex IS NOT NULL
                AND p.dob IS NOT NULL
                GROUP BY p.sex, age_group
            ", [$startDate]);

            $demoData = [
                'labels' => ['0-12 (Pediatric)', '13-17 (Teen)', '18-39 (Adult)', '40-59 (Middle Age)', '60+ (Senior)'],
                'Male' => [0, 0, 0, 0, 0],
                'Female' => [0, 0, 0, 0, 0],
            ];

            foreach ($demographics as $d) {
                $idx = array_search($d->age_group, $demoData['labels']);
                if ($idx !== false && isset($demoData[$d->sex])) {
                    $demoData[$d->sex][$idx] = $d->count;
                }
            }

            return response()->json($demoData);
        }

        if ($chart === 'workload') {
            $workloadData = \App\Models\Consultation::select(\Illuminate\Support\Facades\DB::raw('
                    COALESCE(doctor_id, nurse_id) as staff_id, 
                    COUNT(id) as count
                '))
                ->where('created_at', '>=', $startDate)
                ->where(function ($q) {
                    $q->whereNotNull('doctor_id')->orWhereNotNull('nurse_id');
                })
                ->groupBy('staff_id')
                ->get();

            $staffIds = $workloadData->pluck('staff_id')->filter();
            $staffNames = \App\Models\User::whereIn('id', $staffIds)->get()->pluck('formatted_name', 'id');

            $workloadFormatted = [];
            foreach ($workloadData as $row) {
                if ($row->staff_id && isset($staffNames[$row->staff_id])) {
                    $workloadFormatted[$staffNames[$row->staff_id]] = $row->count;
                }
            }
            arsort($workloadFormatted);
            $workloadFormatted = array_slice($workloadFormatted, 0, 10);

            return response()->json(['labels' => array_keys($workloadFormatted), 'data' => array_values($workloadFormatted)]);
        }

        return response()->json(['error' => 'Invalid chart'], 400);
    }

    public function apiDashboardStats(Request $request)
    {
        $timeFilter = $request->get('time_filter', 'monthly');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => \Carbon\Carbon::create(2000, 1, 1),
        };
        $previousStartDate = match ($timeFilter) {
            'today' => now()->subDay()->startOfDay(),
            'weekly' => now()->subWeek()->startOfWeek(),
            'monthly' => now()->subMonth()->startOfMonth(),
            'yearly' => now()->subYear()->startOfYear(),
            default => \Carbon\Carbon::create(2000, 1, 1),
        };
        $previousEndDate = match ($timeFilter) {
            'today' => now()->subDay()->endOfDay(),
            'weekly' => now()->subWeek()->endOfWeek(),
            'monthly' => now()->subMonth()->endOfMonth(),
            'yearly' => now()->subYear()->endOfYear(),
            default => \Carbon\Carbon::create(2000, 1, 1),
        };

        // 1. Period Consultations & Trend
        $currentPeriodConsultations = \App\Models\Consultation::where('created_at', '>=', $startDate)->count();
        $previousPeriodConsultations = \App\Models\Consultation::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();

        $trendPercentage = 0;
        if ($previousPeriodConsultations > 0) {
            $trendPercentage = round((($currentPeriodConsultations - $previousPeriodConsultations) / $previousPeriodConsultations) * 100, 1);
        } elseif ($currentPeriodConsultations > 0) {
            $trendPercentage = 100;
        }

        // 2. Average Wait Time
        $avgWaitTimeRaw = \App\Models\Consultation::whereNotNull('consultation_start_time')
            ->where('created_at', '>=', $startDate)
            ->select(\Illuminate\Support\Facades\DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait_time'))
            ->value('avg_wait_time');
        $avgWaitTime = $avgWaitTimeRaw ? round($avgWaitTimeRaw) : 0;

        // 3. Compliance Rate
        $totalFollowupsNeeded = \App\Models\Consultation::where('is_followup_needed', true)
            ->where('created_at', '<', now()->subDays(30))
            ->count();

        $compliantFollowups = 0;
        if ($totalFollowupsNeeded > 0) {
            $compliantFollowups = \Illuminate\Support\Facades\DB::select('
                SELECT COUNT(DISTINCT c1.id) as compliant
                FROM consultations c1
                JOIN consultations c2 ON c1.patient_id = c2.patient_id 
                    AND c2.created_at > c1.created_at 
                    AND c2.created_at <= DATE_ADD(c1.created_at, INTERVAL 30 DAY)
                WHERE c1.is_followup_needed = 1 
                AND c1.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
            ')[0]->compliant;
        }
        $complianceRate = $totalFollowupsNeeded > 0 ? round(($compliantFollowups / $totalFollowupsNeeded) * 100) : 0;

        return response()->json([
            'currentPeriodConsultations' => number_format($currentPeriodConsultations),
            'currentPeriodConsultationsRaw' => $currentPeriodConsultations,
            'trendPercentage' => $trendPercentage,
            'avgWaitTime' => $avgWaitTime,
            'complianceRate' => $complianceRate,
            'totalFollowupsNeeded' => number_format($totalFollowupsNeeded),
        ]);
    }

    public function apiStaffProductivityData(Request $request)
    {
        $timeFilter = $request->get('time_filter', 'monthly');
        $staffId = $request->get('staff_id', 'all');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => \Carbon\Carbon::create(2000, 1, 1),
        };

        return response()->json($this->getStaffProductivityData($staffId, $startDate));
    }

    private function getStaffProductivityData($staffId, $startDate)
    {
        $query = \App\Models\Consultation::where('created_at', '>=', $startDate);
        $preTriageQuery = \App\Models\PreTriage::where('created_at', '>=', $startDate)
            ->whereNotNull('encoding_duration_seconds');

        if ($staffId !== 'all') {
            $query->where(function ($q) use ($staffId) {
                $q->where('doctor_id', $staffId)->orWhere('nurse_id', $staffId);
            });
            $preTriageQuery->where('recorded_by', $staffId);
        }

        // 1. Avg Consultation Duration (minutes)
        $avgDuration = $query->clone()
            ->whereNotNull('consultation_start_time')
            ->whereNotNull('consultation_end_time')
            ->select(\Illuminate\Support\Facades\DB::raw('AVG(TIMESTAMPDIFF(MINUTE, consultation_start_time, consultation_end_time)) as avg_duration'))
            ->value('avg_duration') ?? 0;

        // 2. Total Patients Handled (completed/done)
        $totalPatients = $query->clone()
            ->whereIn('status', ['completed', 'done'])
            ->count();

        // 3. Patients Per Hour Throughput
        $totalDurationMinutes = $query->clone()
            ->whereNotNull('consultation_start_time')
            ->whereNotNull('consultation_end_time')
            ->select(\Illuminate\Support\Facades\DB::raw('SUM(TIMESTAMPDIFF(MINUTE, consultation_start_time, consultation_end_time)) as total_duration'))
            ->value('total_duration') ?? 0;

        $patientsPerHour = $totalDurationMinutes > 0 ? round($totalPatients / ($totalDurationMinutes / 60), 1) : 0;

        // 4. Avg Queue Wait Time (minutes)
        $avgWaitTime = $query->clone()
            ->whereNotNull('consultation_start_time')
            ->select(\Illuminate\Support\Facades\DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait_time'))
            ->value('avg_wait_time') ?? 0;

        // 5. Cancellation Rate
        $totalForCancellation = $query->clone()->whereIn('status', ['completed', 'done', 'cancelled'])->count();
        $cancelledCount = $query->clone()->where('status', 'cancelled')->count();
        $cancellationRate = $totalForCancellation > 0 ? round(($cancelledCount / $totalForCancellation) * 100, 1) : 0;

        // Chart 1: Avg Consultation Duration per Doctor
        $durationPerStaffData = \App\Models\Consultation::where('created_at', '>=', $startDate)
            ->whereNotNull('consultation_start_time')
            ->whereNotNull('consultation_end_time')
            ->whereNotNull('doctor_id')
            ->select(\Illuminate\Support\Facades\DB::raw('
                doctor_id as staff_id, 
                AVG(TIMESTAMPDIFF(MINUTE, consultation_start_time, consultation_end_time)) as avg_duration
            '));

        if ($staffId !== 'all') {
            $durationPerStaffData->where('doctor_id', $staffId);
        }

        $durationPerStaffData = $durationPerStaffData->groupBy('staff_id')->get();

        $staffIdsForChart1 = $durationPerStaffData->pluck('staff_id')->filter();
        $staffNamesForChart1 = \App\Models\User::whereIn('id', $staffIdsForChart1)->get()->pluck('formatted_name', 'id');

        $durationChart = [];
        foreach ($durationPerStaffData as $row) {
            if ($row->staff_id && isset($staffNamesForChart1[$row->staff_id])) {
                $durationChart[$staffNamesForChart1[$row->staff_id]] = round($row->avg_duration, 1);
            }
        }
        arsort($durationChart);
        $durationChart = array_slice($durationChart, 0, 10);

        // Chart 2: Triage Encoding Speed per Nurse
        $encodingSpeedData = $preTriageQuery->clone()
            ->select(\Illuminate\Support\Facades\DB::raw('
                recorded_by as staff_id, 
                AVG(encoding_duration_seconds) as avg_speed
            '))
            ->groupBy('staff_id')
            ->get();

        $nurseIds = $encodingSpeedData->pluck('staff_id')->filter();
        $nurseNames = \App\Models\User::whereIn('id', $nurseIds)->get()->pluck('formatted_name', 'id');

        $encodingChart = [];
        foreach ($encodingSpeedData as $row) {
            if ($row->staff_id && isset($nurseNames[$row->staff_id])) {
                $encodingChart[$nurseNames[$row->staff_id]] = round($row->avg_speed);
            }
        }
        asort($encodingChart); // sort ascending because lower is better
        $encodingChart = array_slice($encodingChart, 0, 10);

        return [
            'avgDuration' => round($avgDuration),
            'totalPatients' => $totalPatients,
            'patientsPerHour' => $patientsPerHour,
            'avgWaitTime' => round($avgWaitTime),
            'cancellationRate' => $cancellationRate,
            'durationChartLabels' => array_keys($durationChart),
            'durationChartData' => array_values($durationChart),
            'encodingChartLabels' => array_keys($encodingChart),
            'encodingChartData' => array_values($encodingChart),
        ];
    }

    public function staffIndex(Request $request)
    {
        $query = \App\Models\User::whereIn('role', ['super_admin', 'admin', 'regular_doctor', 'pedia_doctor', 'laboratory', 'radiology', 'vitals_nurse', 'clinical_nurse', 'information_desk', 'pharmacy']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'Online') {
                // Use the same present() scope logic that the homepage uses
                $query->present();
            } elseif ($request->status === 'Offline') {
                // Everyone who is NOT present is offline
                $query->whereNotIn('status', ['Online', 'online'])
                      ->where(function ($q) {
                          $q->whereNull('last_activity_at')
                            ->orWhere('last_activity_at', '<', now()->subMinutes(10));
                      });
            } else {
                $query->where('status', $request->status);
            }
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 20, 50])) {
            $perPage = 10;
        }

        // Calculate total records and total pages
        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / $perPage));

        // Validate the page number before using it in the query
        $rawPage = $request->input('page', 1);
        $page = filter_var($rawPage, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'default' => 1]]);
        if ($page > $totalPages && $total > 0) {
            $page = $totalPages;
        }

        // Server-side pagination using prepared statements, LIMIT and OFFSET
        $staff = $query->with('practitionerSchedules')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        return view('admin.staff.index', compact('staff'));
    }

    public function auditLogs(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('view-audit-logs');

        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                    ->orWhere('model_type', 'like', "%$search%")
                    ->orWhere('model_id', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%");
                    });
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('action', 'like', "%{$request->type}%");
        }

        $perPage = $request->input('per_page', 10);
        $logs = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }

    public function exportAuditLogsCsv(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('view-audit-logs');

        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                    ->orWhere('model_type', 'like', "%$search%")
                    ->orWhere('model_id', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%");
                    });
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('action', 'like', "%{$request->type}%");
        }

        $count = $query->count();

        if ($count === 0) {
            return response()->json(['message' => 'No audit logs found matching the filter criteria.'], 404);
        }

        $filename = 'system-audit-trail-' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store, no-cache',
        ];

        $csvHeaders = [
            'Log ID',
            'Timestamp',
            'Action / Event',
            'Personnel Name',
            'Personnel Role',
            'Personnel Email',
            'Target Model',
            'Target ID',
            'IP Address',
            'User Agent',
            'Changes / Details',
        ];

        return response()->stream(function () use ($query, $csvHeaders) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, $csvHeaders);

            $query->latest()->chunk(250, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    $user = $log->user;
                    $changesSummary = $this->formatAuditChangesForCsv($log->changes);

                    fputcsv($handle, [
                        $log->id,
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A',
                        $log->action ?? 'N/A',
                        $user ? $user->name : 'System / Deleted User',
                        $user ? ucwords(str_replace('_', ' ', $user->role ?? '')) : 'N/A',
                        $user ? $user->email : 'N/A',
                        $log->model_type ? class_basename($log->model_type) : 'N/A',
                        $log->model_id ?? 'N/A',
                        $log->ip_address ?? 'N/A',
                        $log->user_agent ?? 'N/A',
                        $changesSummary,
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Format audit log changes array/JSON into human-friendly staff-readable text.
     */
    private function formatAuditChangesForCsv($changes): string
    {
        if (empty($changes)) {
            return 'No specific property changes recorded';
        }

        // If it's a JSON string, decode it first
        if (is_string($changes)) {
            $decoded = json_decode($changes, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $changes = $decoded;
            } else {
                return trim($changes);
            }
        }

        if (!is_array($changes)) {
            return (string) $changes;
        }

        $ignoredKeys = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password', 'email_verified_at', 'expires_at', 'philhealth_number'];

        // Special handling for Eloquent Model Audits with ['old' => ..., 'new' => ...]
        if (array_key_exists('old', $changes) && array_key_exists('new', $changes)) {
            $old = $changes['old'];
            $new = $changes['new'];

            if (is_null($old) && is_array($new)) {
                // Creation
                $createdParts = [];
                foreach ($new as $k => $v) {
                    if (in_array($k, $ignoredKeys) || is_null($v) || $v === '') continue;
                    $label = ucwords(str_replace(['_', '-'], ' ', $k));
                    $valStr = is_bool($v) ? ($v ? 'Yes' : 'No') : (is_array($v) ? implode(', ', $v) : (string)$v);
                    $createdParts[] = "{$label}: {$valStr}";
                }
                return 'Initial Creation (' . implode('; ', $createdParts) . ')';
            }

            if (is_array($old) && is_array($new)) {
                // Modification: compare old vs new
                $diffParts = [];
                $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
                foreach ($allKeys as $k) {
                    if (in_array($k, $ignoredKeys)) continue;
                    $oldV = $old[$k] ?? null;
                    $newV = $new[$k] ?? null;
                    if ($oldV !== $newV) {
                        $label = ucwords(str_replace(['_', '-'], ' ', $k));
                        $oldStr = is_null($oldV) || $oldV === '' ? 'Empty' : (is_bool($oldV) ? ($oldV ? 'Yes' : 'No') : (string)$oldV);
                        $newStr = is_null($newV) || $newV === '' ? 'Empty' : (is_bool($newV) ? ($newV ? 'Yes' : 'No') : (string)$newV);
                        $diffParts[] = "{$label}: \"{$oldStr}\" -> \"{$newStr}\"";
                    }
                }
                return !empty($diffParts) ? implode(' | ', $diffParts) : 'Record updated with no visible field changes';
            }
        }

        $formatted = [];
        foreach ($changes as $key => $val) {
            if (in_array($key, $ignoredKeys)) continue;
            $label = ucwords(str_replace(['_', '-'], ' ', $key));

            if (is_array($val)) {
                if (array_key_exists('old', $val) || array_key_exists('new', $val)) {
                    $oldVal = $val['old'] ?? null;
                    $newVal = $val['new'] ?? null;
                    $oldStr = is_null($oldVal) || $oldVal === '' ? 'Empty' : (is_bool($oldVal) ? ($oldVal ? 'Yes' : 'No') : (string)$oldVal);
                    $newStr = is_null($newVal) || $newVal === '' ? 'Empty' : (is_bool($newVal) ? ($newVal ? 'Yes' : 'No') : (string)$newVal);
                    $formatted[] = "{$label}: \"{$oldStr}\" -> \"{$newStr}\"";
                } else {
                    $subParts = [];
                    foreach ($val as $subK => $subV) {
                        if (in_array($subK, $ignoredKeys) || is_null($subV) || $subV === '') continue;
                        $subLabel = ucwords(str_replace(['_', '-'], ' ', $subK));
                        $subValStr = is_array($subV) ? implode(', ', $subV) : (is_bool($subV) ? ($subV ? 'Yes' : 'No') : (string)$subV);
                        $subParts[] = "{$subLabel}: {$subValStr}";
                    }
                    if (!empty($subParts)) {
                        $formatted[] = "{$label} (" . implode('; ', $subParts) . ")";
                    }
                }
            } else {
                if (is_bool($val)) $val = $val ? 'Yes' : 'No';
                if (is_null($val) || $val === '') continue;
                $formatted[] = "{$label}: {$val}";
            }
        }

        return !empty($formatted) ? implode(' | ', $formatted) : 'Updated';
    }

    public function getStats()
    {
        $stats = \Illuminate\Support\Facades\Cache::remember('admin.dashboard.stats', 15, function () {
            return [
                'totalDoctors' => \App\Models\User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->count(),
                'presentDoctors' => \App\Models\User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->present()->count(),
                'presentNurses' => \App\Models\User::whereIn('role', ['nurse', 'clinical_nurse', 'vitals_nurse'])->present()->count(),
                'todayAppointments' => \App\Models\Appointment::whereDate('preferred_date', today())->count(),
                'avgWaitTime' => round(\App\Models\Consultation::whereNotNull('consultation_start_time')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->select(\Illuminate\Support\Facades\DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait_time'))
                    ->value('avg_wait_time') ?? 0),
            ];
        });

        return response()->json($stats);
    }

    public function announcements(Request $request)
    {
        $query = Announcement::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $searchBy = $request->get('search_by', 'all');

            $query->where(function ($builder) use ($q, $searchBy) {
                if ($searchBy === 'title') {
                    $builder->where('title', 'like', "%$q%");
                } elseif ($searchBy === 'subheading') {
                    $builder->where('subheading', 'like', "%$q%");
                } elseif ($searchBy === 'content') {
                    $builder->where('content', 'like', "%$q%");
                } else {
                    $builder->where('title', 'like', "%$q%")
                        ->orWhere('content', 'like', "%$q%")
                        ->orWhere('subheading', 'like', "%$q%");
                }
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('date_posted') && ! $request->filled('date_from') && ! $request->filled('date_to')) {
            $query->whereDate('created_at', $request->date_posted);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = 10;

        // Calculate total records and total pages
        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / $perPage));

        // Validate the page number before using it in the query
        $rawPage = $request->input('page', 1);
        $page = filter_var($rawPage, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'default' => 1]]);
        if ($page > $totalPages && $total > 0) {
            $page = $totalPages;
        }

        // Server-side pagination using prepared statements, LIMIT and OFFSET
        $announcements = $query->latest()
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function createAnnouncement()
    {
        return view('admin.announcements.create');
    }

    public function editAnnouncement(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'content' => 'required|string',
            'content_align' => 'nullable|in:left,center,right,justify',

            'images' => 'nullable',
            'images.*' => ['nullable', 'file', 'max:51200', new SecureImage],
            'display_type' => 'nullable|in:list,carousel',
            'display_mode' => 'nullable|in:standard,infographic',
            'sections' => 'nullable|array',
            'sections.*.content' => 'nullable|string',
            'sections.*.image' => ['nullable', 'file', 'max:51200', new SecureImage],
            'sections.*.video_url' => 'nullable|url',
            'sections.*.layout' => 'nullable|in:left,right,middle',
            'sections.*.text_align' => 'nullable|in:left,center,right,justify',
        ]);

        $imagePath = null;
        $extraFiles = [];

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            // Ensure we handle both single file and array of files
            if (is_array($files) && count($files) > 0) {
                $imagePath = $files[0]->store('announcements', 'uploads');
                $extraFiles = array_slice($files, 1);
            } elseif ($files instanceof \Illuminate\Http\UploadedFile) {
                $imagePath = $files->store('announcements', 'uploads');
            }
        }

        $isSuperAdmin = Auth::user()->hasRole('super_admin');

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'subheading' => $validated['subheading'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'content' => $validated['content'],
            'content_align' => $validated['content_align'] ?? 'left',
            'image_path' => $imagePath,
            'status' => $request->has('status') ? $request->status : ($isSuperAdmin ? 'published' : 'pending'),
            'display_type' => $validated['display_type'] ?? 'list',
            'display_mode' => $validated['display_mode'] ?? 'standard',
        ]);

        // Store extra main images/videos as gallery/carousel slides
        foreach ($extraFiles as $index => $file) {
            $path = $file->store('announcements/gallery', 'uploads');
            $mime = $file->getMimeType();
            $mediaType = str_starts_with($mime, 'video/') ? 'video_upload' : 'image';

            $announcement->images()->create([
                'image_path' => $path, // Used for both image and video path
                'content' => null,
                'layout' => 'middle',
                'sort_order' => $index,
                'type' => 'slide',
                'media_type' => $mediaType,
            ]);
        }

        if ($request->has('sections')) {
            foreach ($request->sections as $index => $section) {
                $sectionMediaPath = null;
                $mediaType = 'image';
                $videoUrl = $section['video_url'] ?? null;

                // Handle file upload (Image or Video)
                if (isset($section['image']) && $section['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $sectionMediaPath = $section['image']->store('announcements/gallery', 'uploads');
                    $mime = $section['image']->getMimeType();
                    if (str_starts_with($mime, 'video/')) {
                        $mediaType = 'video_upload';
                    }
                } elseif ($videoUrl) {
                    $mediaType = 'video_link';
                }

                if ($sectionMediaPath || $videoUrl || ! empty($section['content'])) {
                    $announcement->images()->create([
                        'image_path' => $sectionMediaPath ?? '', // Empty string if only URL or Text
                        'video_url' => $videoUrl,
                        'content' => $section['content'] ?? null,
                        'text_align' => $section['text_align'] ?? 'left',
                        'layout' => $section['layout'] ?? 'left',
                        'sort_order' => $index,
                        'type' => 'section',
                        'media_type' => $mediaType,
                    ]);
                }
            }
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement posted successfully!');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'content' => 'required|string',
            'content_align' => 'nullable|in:left,center,right,justify',
            'images' => 'nullable',
            'images.*' => ['nullable', 'file', 'max:51200', new SecureImage],
            'display_type' => 'nullable|in:list,carousel',
            'display_mode' => 'nullable|in:standard,infographic',
            // Update existing sections
            'existing_sections' => 'nullable|array',
            'existing_sections.*.content' => 'nullable|string',
            'existing_sections.*.image' => ['nullable', 'file', 'max:10240', new SecureImage],
            'existing_sections.*.layout' => 'nullable|in:left,right,middle',
            'existing_sections.*.text_align' => 'nullable|in:left,center,right,justify',
            // Simple gallery append for now
            'new_sections' => 'nullable|array',
            'new_sections.*.content' => 'nullable|string',
            'new_sections.*.image' => ['nullable', 'file', 'max:10240', new SecureImage],
            'new_sections.*.layout' => 'nullable|in:left,right,middle',
            'new_sections.*.text_align' => 'nullable|in:left,center,right,justify',
            'status' => 'nullable|in:draft,pending,published',
        ]);

        $extraImages = [];

        if ($request->hasFile('images')) {
            $files = $request->file('images');

            // Clean up old main image if replacing
            if ($announcement->image_path) {
                Storage::disk('uploads')->delete($announcement->image_path);
            }

            // Clean up OLD slides (type='slide') to replace with new set
            $oldSlides = $announcement->images()->where('type', 'slide')->get();
            foreach ($oldSlides as $slide) {
                Storage::disk('uploads')->delete($slide->image_path);
                $slide->delete();
            }

            if (is_array($files) && count($files) > 0) {
                $announcement->image_path = $files[0]->store('announcements', 'uploads');
                $extraImages = array_slice($files, 1);
            } elseif ($files instanceof \Illuminate\Http\UploadedFile) {
                $announcement->image_path = $files->store('announcements', 'uploads');
            }
        }

        $announcement->update([
            'title' => $validated['title'],
            'subheading' => $validated['subheading'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'content' => $validated['content'],
            'content_align' => $validated['content_align'] ?? $announcement->content_align ?? 'left',
            'image_path' => $announcement->image_path,
            'display_type' => $validated['display_type'] ?? 'list',
            'display_mode' => $validated['display_mode'] ?? $announcement->display_mode,
            'status' => $request->has('status') ? $request->status : $announcement->status,
        ]);

        // Add extra main images as gallery items (Slides)
        $startingOrder = $announcement->images()->max('sort_order') + 1;
        foreach ($extraImages as $index => $file) {
            $path = $file->store('announcements/gallery', 'uploads');
            $announcement->images()->create([
                'image_path' => $path,
                'content' => null,
                'layout' => 'middle',
                'sort_order' => $startingOrder + $index,
                'type' => 'slide',
            ]);
        }

        // Handle updates to existing sections
        if ($request->has('existing_sections')) {
            foreach ($request->existing_sections as $id => $data) {
                $section = \App\Models\AnnouncementImage::find($id);
                if ($section && $section->announcement_id == $announcement->id) {
                    $updateData = [
                        'content' => $data['content'] ?? null,
                        'text_align' => $data['text_align'] ?? $section->text_align ?? 'left',
                        'layout' => $data['layout'] ?? $section->layout,
                        'video_url' => $data['video_url'] ?? $section->video_url,
                    ];

                    if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                        if ($section->image_path) {
                            Storage::disk('uploads')->delete($section->image_path);
                        }
                        $updateData['image_path'] = $data['image']->store('announcements/gallery', 'uploads');
                        $mime = $data['image']->getMimeType();
                        if (str_starts_with($mime, 'video/')) {
                            $updateData['media_type'] = 'video_upload';
                        } else {
                            $updateData['media_type'] = 'image';
                        }
                    } elseif (! empty($data['video_url'])) {
                        $updateData['media_type'] = 'video_link';
                    }

                    $section->update($updateData);
                }
            }
        }

        // Handle new sections added during edit
        if ($request->has('new_sections')) {
            $startingOrder = $announcement->images()->max('sort_order') + 1;
            foreach ($request->new_sections as $index => $section) {
                $sectionImagePath = null;
                $mediaType = 'image';
                $videoUrl = $section['video_url'] ?? null;

                if (isset($section['image']) && $section['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $sectionImagePath = $section['image']->store('announcements/gallery', 'uploads');
                    $mime = $section['image']->getMimeType();
                    if (str_starts_with($mime, 'video/')) {
                        $mediaType = 'video_upload';
                    }
                } elseif ($videoUrl) {
                    $mediaType = 'video_link';
                }

                if ($sectionImagePath || $videoUrl || ! empty($section['content'])) {
                    $announcement->images()->create([
                        'image_path' => $sectionImagePath ?? '',
                        'video_url' => $videoUrl,
                        'content' => $section['content'] ?? null,
                        'text_align' => $section['text_align'] ?? 'left',
                        'layout' => $section['layout'] ?? 'left',
                        'sort_order' => $startingOrder + $index,
                        'type' => 'section',
                        'media_type' => $mediaType,
                    ]);
                }
            }
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully!');
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-announcements');

        $announcement->delete();

        return back()->with('success', 'Announcement archived successfully!');
    }

    // New method for toggling status (Reposting updates timestamp)
    public function toggleAnnouncementStatus(Announcement $announcement, Request $request)
    {
        $newStatus = $request->input('status');

        if (! in_array($newStatus, ['draft', 'pending', 'published'])) {
            return back()->with('error', 'Invalid status requested.');
        }

        // Only super_admin can set to published directly
        if ($newStatus === 'published' && ! Auth::user()->hasRole('super_admin')) {
            $newStatus = 'pending';
        }

        $announcement->status = $newStatus;

        // If we are activating (reposting to published), update the creation time so it appears at the top
        if ($announcement->status === 'published') {
            $announcement->created_at = now();
        }

        $announcement->save();

        $statusMsg = $announcement->status === 'published' ? 'posted/re-uploaded' : "moved to {$announcement->status}";

        return back()->with('success', "Announcement has been $statusMsg successfully!");
    }

    public function deleteAnnouncementImage(\App\Models\AnnouncementImage $image)
    {
        if ($image->image_path) {
            Storage::disk('uploads')->delete($image->image_path);
        }
        $image->delete();

        return back()->with('success', 'Image removed from gallery.');
    }

    public function bulkDeleteImages(Request $request)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:announcement_images,id',
        ]);

        $images = \App\Models\AnnouncementImage::whereIn('id', $request->image_ids)->get();
        $count = $images->count();

        foreach ($images as $image) {
            if ($image->image_path) {
                Storage::disk('uploads')->delete($image->image_path);
            }
            $image->delete();
        }

        return back()->with('success', "$count sections deleted successfully!");
    }

    public function storeStaff(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,regular_doctor,pedia_doctor,laboratory,radiology,vitals_nurse,clinical_nurse,information_desk,pharmacy',
            'status' => 'required|string',
            'schedule' => 'nullable|string',
            'avatar' => ['nullable', 'file', 'max:2048', new SecureImage],
        ]);

        // Only super_admin can create admin/super_admin accounts
        if (in_array($validated['role'], ['admin', 'super_admin'])) {
            \Illuminate\Support\Facades\Gate::authorize('promote-admin');
        }

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('staff', 'uploads');
        }

        $name = ucwords(strtolower(trim(str_replace(['Dr. ', 'Dr '], '', $validated['name']))));

        $normalizedStatus = match(strtolower($validated['status'])) {
            'online', 'present' => 'Online',
            'offline', 'unavailable', 'out of office' => 'Offline',
            'occupied', 'seminar', 'in meeting' => 'Occupied',
            default => $validated['status']
        };

        $user = \App\Models\User::create([
            'name' => $name,
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'status' => $normalizedStatus,
            'last_activity_at' => $normalizedStatus === 'Online' ? now() : null,
            // 'schedule' string column is no longer used, we save to practitioner_schedules instead
            'avatar_path' => $avatarPath,
        ]);
        $user->role = $validated['role'];
        $user->save();

        if (! empty($validated['schedule'])) {
            $schedules = json_decode($validated['schedule'], true);
            if (is_array($schedules)) {
                foreach ($schedules as $sched) {
                    \App\Models\PractitionerSchedule::create([
                        'user_id' => $user->id,
                        'day_of_week' => $sched['day'],
                        'time_in' => $sched['time_in'],
                        'time_out' => $sched['time_out'],
                    ]);
                }
            }
        }

        return back()->with('success', 'Staff account created successfully!');
    }

    public function updateStaff(Request $request, \App\Models\User $user)
    {
        // Prevent regular admin from editing other admin or super_admin accounts
        if (in_array($user->role, ['admin', 'super_admin']) && $user->id !== auth()->id()) {
            \Illuminate\Support\Facades\Gate::authorize('manage-admins');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:super_admin,admin,regular_doctor,pedia_doctor,laboratory,radiology,vitals_nurse,clinical_nurse,information_desk,pharmacy',
            'status' => 'required|string',
            'schedule' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'avatar' => ['nullable', 'file', 'max:2048', new SecureImage],
        ]);

        // Only super_admin can assign admin/super_admin roles
        if (in_array($validated['role'], ['admin', 'super_admin'])) {
            \Illuminate\Support\Facades\Gate::authorize('promote-admin');
        }

        $name = ucwords(strtolower(trim(str_replace(['Dr. ', 'Dr '], '', $validated['name']))));

        $normalizedStatus = match(strtolower($validated['status'])) {
            'online', 'present' => 'Online',
            'offline', 'unavailable', 'out of office' => 'Offline',
            'occupied', 'seminar', 'in meeting' => 'Occupied',
            default => $validated['status']
        };

        $data = [
            'name' => $name,
            'email' => $validated['email'],
            'status' => $normalizedStatus,
            // 'schedule' string column is no longer used directly
        ];

        if ($normalizedStatus === 'Online') {
            $data['last_activity_at'] = now();
        } elseif ($normalizedStatus === 'Offline') {
            $data['last_activity_at'] = null;
        }

        if (! empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar from both disks (migration cleanup)
            if ($user->avatar_path) {
                $cleanPath = ltrim(str_replace(['uploads/', 'storage/'], '', $user->avatar_path), '/');
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cleanPath);
                \Illuminate\Support\Facades\Storage::disk('uploads')->delete($cleanPath);
            }
            $data['avatar_path'] = $request->file('avatar')->store('staff', 'uploads');
        }

        $user->update($data);
        
        if ($user->role !== $validated['role']) {
            $user->role = $validated['role'];
            $user->save();
        }

        // Handle Schedule Update
        if ($request->has('schedule')) {
            $user->practitionerSchedules()->delete(); // Clear old schedules
            if (! empty($validated['schedule'])) {
                $schedules = json_decode($validated['schedule'], true);
                if (is_array($schedules)) {
                    foreach ($schedules as $sched) {
                        \App\Models\PractitionerSchedule::create([
                            'user_id' => $user->id,
                            'day_of_week' => $sched['day'],
                            'time_in' => $sched['time_in'],
                            'time_out' => $sched['time_out'],
                        ]);
                    }
                }
            }
        }

        return back()->with('success', "{$user->name}'s profile has been updated successfully!");
    }

    public function destroyStaff(\App\Models\User $user)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-staff');

        // Prevent regular admin from deleting admin or super_admin accounts
        if (in_array($user->role, ['admin', 'super_admin'])) {
            \Illuminate\Support\Facades\Gate::authorize('manage-admins');
        }

        // Prevent deleting the last admin
        if ($user->role === 'admin' && \App\Models\User::where('role', 'admin')->count() === 1) {
            return back()->with('error', 'Cannot delete the only admin account.');
        }

        // Prevent deleting the last super_admin
        if ($user->role === 'super_admin' && \App\Models\User::where('role', 'super_admin')->count() === 1) {
            return back()->with('error', 'Cannot delete the only super admin account.');
        }

        $user->delete();

        return back()->with('success', 'Staff archived successfully!');
    }

    public function updateStaffStatus(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $normalizedStatus = match(strtolower($request->status)) {
            'online', 'present', 'active' => 'Online',
            'offline', 'unavailable', 'out of office' => 'Offline',
            'occupied', 'seminar', 'in meeting' => 'Occupied',
            default => $request->status
        };

        $updateData = ['status' => $normalizedStatus];

        // Set schedule_override to track manual status changes
        if ($normalizedStatus === 'Offline') {
            $updateData['schedule_override'] = 'manual_offline';
            $updateData['last_activity_at'] = null;
        } elseif ($normalizedStatus === 'Online') {
            $updateData['schedule_override'] = 'manual_online';
            $updateData['last_activity_at'] = now();
        } else {
            // Occupied
            $updateData['schedule_override'] = 'manual_online';
            $updateData['last_activity_at'] = now();
        }

        $user->update($updateData);

        return back()->with('success', "Staff status updated to {$normalizedStatus} successfully!");
    }


    public function promoteToAdmin(\App\Models\User $user)
    {
        \Illuminate\Support\Facades\Gate::authorize('promote-admin');

        // Only promote if they are actually staff
        if (in_array($user->role, ['regular_doctor', 'pedia_doctor', 'laboratory', 'radiology', 'clinical_nurse', 'vitals_nurse', 'information_desk', 'pharmacy'])) {
            $user->update(['role' => 'admin']);

            return back()->with('success', "{$user->name} has been promoted to Administrator!");
        }

        return back()->with('error', 'Invalid user role for promotion.');
    }

    public function retentionIndex()
    {
        // Get patients whose expires_at is past due (candidate for permanent deletion)
        $inactivePatients = \App\Models\Patient::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->orderBy('expires_at', 'asc')
            ->paginate(20);

        return view('admin.retention.index', compact('inactivePatients'));
    }

    public function extendRetention(\App\Models\Patient $patient)
    {
        // Touch the updated_at timestamp and push the expires_at timestamp into the future
        $patient->touch();

        return back()->with('success', "Data retention for patient {$patient->full_name} extended by 1 year.");
    }

    public function deleteRetention(\App\Models\Patient $patient)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-retention');

        // Soft delete (archive) the patient to prevent cascading data loss of clinical records
        $patient->delete();

        \App\Models\AuditLog::record("Archived Inactive Patient (Data Retention): {$patient->patient_id}", $patient);

        return back()->with('success', 'Inactive patient record archived safely.');
    }

    public function patientRecordsIndex(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('patient_id', 'like', "%$search%");
            });
        }

        if ($request->filled('classification') && $request->classification !== 'all') {
            $query->where('classification', $request->classification);
        }

        $perPage = 10;

        // Calculate total records and total pages
        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / $perPage));

        // Validate the page number before using it in the query
        $rawPage = $request->input('page', 1);
        $page = filter_var($rawPage, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'default' => 1]]);
        if ($page > $totalPages && $total > 0) {
            $page = $totalPages;
        }

        // Server-side pagination using prepared statements, LIMIT and OFFSET
        $patients = $query->withCount('consultations')
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        AuditLog::record('Viewed Patient Master List');

        return view('admin.patients.index', compact('patients'));
    }

    public function showPatient(Request $request, Patient $patient)
    {
        // Self-heal: ensure any completed consultations have an archived MedicalCase
        foreach ($patient->consultations()->where('status', 'completed')->with('preTriage')->get() as $c) {
            if (!\App\Models\MedicalCase::where('consultation_id', $c->id)->exists()) {
                $datePrefix = $c->created_at ? $c->created_at->format('Ymd') : now()->format('Ymd');
                $caseNumber = 'CASE-' . $datePrefix . '-' . str_pad($c->id, 5, '0', STR_PAD_LEFT);
                if (\App\Models\MedicalCase::where('case_number', $caseNumber)->exists()) {
                    $caseNumber .= '-C' . $c->id;
                }
                \App\Models\MedicalCase::create([
                    'case_number' => $caseNumber,
                    'patient_id' => $c->patient_id,
                    'consultation_id' => $c->id,
                    'pre_triage_id' => $c->pre_triage_id,
                    'diagnosis' => $c->diagnosis ?: 'Clinical Follow-up & Evaluation',
                    'prescription' => $c->prescription ?: '[]',
                    'vitals_snapshot' => [
                        'bp' => $c->preTriage->blood_pressure ?? $c->blood_pressure ?? null,
                        'temp' => $c->preTriage->temperature ?? $c->temperature ?? null,
                        'wt' => $c->preTriage->weight ?? $c->weight ?? null,
                        'ht' => $c->preTriage->height ?? $c->height ?? null,
                        'hr' => $c->preTriage->heart_rate ?? $c->heart_rate ?? null,
                        'rr' => $c->preTriage->respiratory_rate ?? $c->respiratory_rate ?? null,
                        'pr' => $c->preTriage->pulse_rate ?? $c->pulse_rate ?? null,
                        'spo2' => $c->preTriage->spo2 ?? $c->spo2 ?? ($c->preTriage->oxygen_saturation ?? null),
                    ],
                    'closed_at' => $c->consultation_end_time ?? $c->updated_at ?? now(),
                    'created_at' => $c->created_at ?? now(),
                    'updated_at' => $c->updated_at ?? now(),
                ]);
            }
        }

        $patient->load([
            'consultations' => function ($q) {
                $q->orderBy('consultation_date', 'desc')->orderBy('created_at', 'desc');
            },
            'consultations.doctor',
            'consultations.nurse',
            'medicalCases.consultation.ancillaryRequests.technician',
            'medicalCases.consultation.prescriptionRecord.items',
            'medicalCases.preTriage',
        ]);

        $isUnmasked = false;
        if ($request->has('unmask') && $request->get('unmask') == '1') {
            $reason = $request->input('override_reason', 'Administrative clinical record inspection');
            AuditLog::record("Admin Clinical PHI Override Accessed: {$reason}", $patient, [
                'admin_id' => auth()->id(),
                'reason' => $reason,
                'ip' => $request->ip(),
            ]);
            $isUnmasked = true;
        } else {
            AuditLog::record('Viewed Patient Demographics & Administrative Record', $patient);
        }

        return view('admin.patients.show', compact('patient', 'isUnmasked'));
    }

    public function bulkDeleteAnnouncements(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-announcements');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:announcements,id',
        ]);

        Announcement::whereIn('id', $request->ids)->delete();

        return back()->with('success', count($request->ids).' announcements archived successfully!');
    }

    public function bulkDeleteStaff(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-staff');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $ids = $request->ids;
        $usersToDelete = \App\Models\User::whereIn('id', $ids)->get();

        // Regular admin cannot bulk-delete admin or super_admin accounts
        $hasAdminTargets = $usersToDelete->whereIn('role', ['admin', 'super_admin'])->isNotEmpty();
        if ($hasAdminTargets) {
            \Illuminate\Support\Facades\Gate::authorize('manage-admins');
        }

        // Prevent deleting all admins
        $adminCountToDelete = $usersToDelete->where('role', 'admin')->count();
        $totalAdmins = \App\Models\User::where('role', 'admin')->count();
        if ($adminCountToDelete > 0 && $totalAdmins <= $adminCountToDelete) {
            return back()->with('error', 'Cannot delete all admin accounts.');
        }

        // Prevent deleting all super_admins
        $superAdminCountToDelete = $usersToDelete->where('role', 'super_admin')->count();
        $totalSuperAdmins = \App\Models\User::where('role', 'super_admin')->count();
        if ($superAdminCountToDelete > 0 && $totalSuperAdmins <= $superAdminCountToDelete) {
            return back()->with('error', 'Cannot delete all super admin accounts.');
        }

        \App\Models\User::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids).' staff members archived successfully!');
    }

    public function bulkUpdateStaffStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'status' => 'required|string|in:Online,Offline,Occupied',
        ]);

        $normalizedStatus = match(strtolower($request->status)) {
            'online', 'present', 'active' => 'Online',
            'offline', 'unavailable', 'out of office' => 'Offline',
            'occupied', 'seminar', 'in meeting' => 'Occupied',
            default => $request->status
        };

        $updateData = ['status' => $normalizedStatus];

        // Set schedule_override to track manual status changes
        if ($normalizedStatus === 'Offline') {
            $updateData['schedule_override'] = 'manual_offline';
            $updateData['last_activity_at'] = null;
        } elseif ($normalizedStatus === 'Online') {
            $updateData['schedule_override'] = 'manual_online';
            $updateData['last_activity_at'] = now();
        } else {
            // Occupied
            $updateData['schedule_override'] = 'manual_online';
            $updateData['last_activity_at'] = now();
        }

        \App\Models\User::whereIn('id', $request->ids)->update($updateData);

        return back()->with('success', count($request->ids)." staff members set to {$normalizedStatus} successfully!");
    }

    public function bulkPromoteStaff(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('promote-admin');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $promotableRoles = ['regular_doctor', 'pedia_doctor', 'laboratory', 'radiology', 'clinical_nurse', 'vitals_nurse', 'information_desk', 'pharmacy'];

        $promoted = \App\Models\User::whereIn('id', $request->ids)
            ->whereIn('role', $promotableRoles)
            ->update(['role' => 'admin']);

        if ($promoted === 0) {
            return back()->with('error', 'No eligible staff members found for promotion. Users who are already Admins or Super Admins cannot be promoted again.');
        }

        return back()->with('success', "{$promoted} staff member(s) promoted to Administrator successfully!");
    }

    public function bulkDeleteRetention(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete-retention');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:patients,patient_id',
        ]);

        \App\Models\Patient::whereIn('patient_id', $request->ids)->forceDelete();

        return back()->with('success', count($request->ids).' inactive patient records permanently deleted.');
    }

    public function bulkExtendRetention(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:patients,patient_id',
        ]);

        \App\Models\Patient::whereIn('patient_id', $request->ids)->update([
            'updated_at' => now(),
            'expires_at' => now()->addYears(10),
        ]);

        return back()->with('success', 'Data retention for '.count($request->ids).' patients extended.');
    }

    // Content Management
    public function contentIndex()
    {
        $groups = ['topbar', 'hero', 'footer', 'about', 'steps', 'faq', 'privacy'];
        $settings = [];
        foreach ($groups as $group) {
            $settings[$group] = \App\Models\SiteSetting::where('group', $group)->get()->keyBy('key');
        }

        $facilityUnits = \App\Models\FacilityUnit::ordered()->get();

        return view('admin.content.index', compact('settings', 'facilityUnits'));
    }

    public function contentUpdate(Request $request)
    {
        // Only super_admin or admin can modify system content settings
        \Illuminate\Support\Facades\Gate::authorize('manage-content');

        $request->validate([
            'settings' => 'required|array',
            'settings.footer_email' => 'nullable|email|max:255',
            'settings.footer_phone' => 'nullable|string|max:100',
            'settings.clinic_hours' => 'nullable|string|max:255',
            'settings.emergency_hotlines' => 'nullable|string|max:255',
            'hero_image_file' => ['nullable', 'file', 'max:10240', new SecureImage],
            'mission_image_file' => ['nullable', 'file', 'max:10240', new SecureImage],
            'step_images' => 'nullable|array',
            'step_images.*.*' => ['nullable', 'file', 'max:10240', new SecureImage],
        ]);

        $groupMap = [
            'hero_badge_text' => 'hero',
            'hero_title_line1' => 'hero',
            'hero_title_highlight' => 'hero',
            'hero_title_line2' => 'hero',
            'hero_description' => 'hero',
            'hero_image' => 'hero',
            'carousel_hero_title' => 'hero',
            'carousel_hero_subtitle' => 'hero',
            'topbar_republic' => 'topbar',
            'topbar_province' => 'topbar',
            'topbar_municipality' => 'topbar',
            'clinic_hours' => 'topbar',
            'emergency_hotlines' => 'topbar',
            'mission_headline' => 'about',
            'mission_subheadline' => 'about',
            'mission_title' => 'about',
            'mission_statement' => 'about',
            'mission_image' => 'about',
            'mission_image_caption' => 'about',
            'vision_headline' => 'about',
            'vision_statement' => 'about',
            'charter_title' => 'about',
            'charter_subtitle' => 'about',
            'footer_address_line1' => 'footer',
            'footer_address_line2' => 'footer',
            'footer_phone' => 'footer',
            'footer_email' => 'footer',
            'privacy_intro' => 'privacy',
            'privacy_footer' => 'privacy',
        ];

        if ($request->hasFile('hero_image_file')) {
            $oldHeroImage = \App\Models\SiteSetting::get('hero_image');
            if ($oldHeroImage && \Illuminate\Support\Str::startsWith($oldHeroImage, 'uploads/')) {
                $oldPath = str_replace('uploads/', '', $oldHeroImage);
                if (\Illuminate\Support\Facades\Storage::disk('uploads')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('uploads')->delete($oldPath);
                }
            }
            $path = $request->file('hero_image_file')->store('content', 'uploads');
            \App\Models\SiteSetting::set('hero_image', 'uploads/'.$path, 'hero', 'image');
        }

        if ($request->hasFile('mission_image_file')) {
            $oldMissionImage = \App\Models\SiteSetting::get('mission_image');
            if ($oldMissionImage && \Illuminate\Support\Str::startsWith($oldMissionImage, 'uploads/')) {
                $oldPath = str_replace('uploads/', '', $oldMissionImage);
                if (\Illuminate\Support\Facades\Storage::disk('uploads')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('uploads')->delete($oldPath);
                }
            }
            $path = $request->file('mission_image_file')->store('content/mission', 'uploads');
            \App\Models\SiteSetting::set('mission_image', 'uploads/'.$path, 'about', 'image');
        }

        foreach ($request->settings as $key => $value) {
            $group = $groupMap[$key] ?? 'general';
            $trimmedValue = is_string($value) ? trim($value) : $value;
            \App\Models\SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $trimmedValue, 'group' => $group, 'type' => 'text']
            );
        }

        if ($request->has('steps')) {
            foreach ($request->steps as $unitSlug => $unitSteps) {
                if (is_array($unitSteps)) {
                    $steps = array_values(array_filter($unitSteps, function ($s) {
                        return ! empty(trim($s['title'] ?? '')) || ! empty(trim($s['description'] ?? ''));
                    }));

                    // Handle step image uploads
                    foreach ($steps as $index => &$step) {
                        if ($request->hasFile("step_images.{$unitSlug}.{$index}")) {
                            if (! empty($step['image'])) {
                                $oldStepPath = str_replace('uploads/', '', $step['image']);
                                if (\Illuminate\Support\Facades\Storage::disk('uploads')->exists($oldStepPath)) {
                                    \Illuminate\Support\Facades\Storage::disk('uploads')->delete($oldStepPath);
                                }
                            }
                            $file = $request->file("step_images.{$unitSlug}.{$index}");
                            $path = $file->store('content/steps', 'uploads');
                            $step['image'] = $path;
                        }
                    }
                    unset($step);

                    \App\Models\SiteSetting::updateOrCreate(
                        ['key' => 'steps_data_'.$unitSlug],
                        ['group' => 'steps', 'value' => json_encode($steps), 'type' => 'json']
                    );
                }
            }
        }

        if ($request->has('guiding_principles')) {
            foreach ($request->guiding_principles as $p) {
                if (empty(trim($p['title'] ?? '')) || empty(trim($p['description'] ?? ''))) {
                    return back()->withInput()->with('error', 'Every guiding principle must have both a Title and a Charter Description.')->withErrors(['guiding_principles' => 'Every guiding principle must have both a Title and a Charter Description.']);
                }
            }
            $principles = array_values(array_filter($request->guiding_principles, function ($p) {
                return ! empty(trim($p['title'] ?? '')) && ! empty(trim($p['description'] ?? ''));
            }));
            \App\Models\SiteSetting::updateOrCreate(
                ['key' => 'guiding_principles'],
                ['group' => 'about', 'value' => json_encode($principles), 'type' => 'json']
            );
        }

        if ($request->has('mission_points') && is_array($request->mission_points)) {
            $points = array_values(array_filter(array_map('trim', $request->mission_points)));
            \App\Models\SiteSetting::updateOrCreate(
                ['key' => 'mission_points'],
                ['group' => 'about', 'value' => json_encode($points), 'type' => 'json']
            );
        }

        if ($request->has('vision_pillars') && is_array($request->vision_pillars)) {
            $pillars = array_values(array_filter($request->vision_pillars, function ($p) {
                return !empty(trim($p['title'] ?? '')) || !empty(trim($p['description'] ?? ''));
            }));
            \App\Models\SiteSetting::updateOrCreate(
                ['key' => 'vision_pillars'],
                ['group' => 'about', 'value' => json_encode($pillars), 'type' => 'json']
            );
        }

        if ($request->has('about_metrics') && is_array($request->about_metrics)) {
            $metrics = array_values(array_filter($request->about_metrics, function ($m) {
                return !empty(trim($m['value'] ?? '')) || !empty(trim($m['title'] ?? ''));
            }));
            \App\Models\SiteSetting::updateOrCreate(
                ['key' => 'about_metrics'],
                ['group' => 'about', 'value' => json_encode($metrics), 'type' => 'json']
            );
        }

        if ($request->has('faq')) {
            $faq = array_values(array_filter($request->faq, function ($f) {
                return ! empty(trim($f['question'] ?? '')) || ! empty(trim($f['answer'] ?? ''));
            }));
            \App\Models\SiteSetting::updateOrCreate(
                ['key' => 'faq_items'],
                ['group' => 'faq', 'value' => json_encode($faq), 'type' => 'json']
            );
        }

        if ($request->has('privacy_list')) {
            $items = array_values(array_filter(array_map('trim', $request->privacy_list)));
            \App\Models\SiteSetting::updateOrCreate(
                ['key' => 'privacy_items'],
                ['group' => 'privacy', 'value' => json_encode($items), 'type' => 'json']
            );
        }

        \App\Models\SiteSetting::clearCache();
        \Illuminate\Support\Facades\Cache::forget('global_facility_units');

        \App\Models\AuditLog::record('Updated Landing Page & CMS Content');

        return back()->with('success', 'Content updated successfully!');
    }

    public function printItr(Request $request, Patient $patient)
    {
        $caseIds = $request->input('cases', []);

        $query = $patient->medicalCases()
            ->with([
                'consultation.doctor',
                'consultation.nurse',
                'consultation.ancillaryRequests.technician',
                'consultation.prescriptionRecord.items',
                'preTriage',
            ])
            ->orderBy('created_at', 'desc');

        if (!empty($caseIds)) {
            if (is_string($caseIds)) {
                $caseIds = explode(',', $caseIds);
            }
            $query->whereIn('id', $caseIds);
        }

        $cases = $query->get();

        return view('admin.patients.itr', compact('patient', 'cases'));
    }

    public function printAncillary(Request $request, Patient $patient)
    {
        $ids = $request->input('ids', []);
        $caseId = $request->input('case_id');

        $query = \App\Models\AncillaryRequest::with([
                'consultation.doctor',
                'consultation.nurse',
                'consultation.patient',
                'technician',
                'amender',
                'collector',
            ])
            ->whereHas('consultation', function ($q) use ($patient) {
                $q->where('patient_id', $patient->patient_id)
                  ->orWhere('patient_id', (string)$patient->id);
            })
            ->where('status', 'Done');

        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = array_filter(explode(',', $ids));
            }
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        if ($caseId) {
            $case = \App\Models\MedicalCase::find($caseId);
            if ($case && $case->consultation_id) {
                $query->where('consultation_id', $case->consultation_id);
            }
        }

        $requests = $query->orderBy('completed_at', 'desc')->get();

        if ($requests->isEmpty()) {
            return back()->with('error', 'No completed diagnostic results found to print.');
        }

        return view('admin.ancillary.print', compact('patient', 'requests'));
    }

    public function printAncillarySingle(Request $request, \App\Models\AncillaryRequest $ancillary)
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
        if (!$patient) {
            return back()->with('error', 'No patient record linked to this diagnostic request.');
        }

        $requests = collect([$ancillary]);

        return view('admin.ancillary.print', compact('patient', 'requests'));
    }

    /**
     * Compute dynamic Peak Hours chart data covering active clinic intake.
     * Starts at 6:00 AM (early morning queue & triage window) through 6:00 PM,
     * dynamically expanding if consultations exist outside standard margins.
     */
    private function getPeakHoursData($startDate): array
    {
        $peakHours = \App\Models\Consultation::where('created_at', '>=', $startDate)
            ->select(\Illuminate\Support\Facades\DB::raw('HOUR(created_at) as hour, COUNT(id) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $minHour = 6;  // 6:00 AM
        $maxHour = 18; // 6:00 PM

        if ($peakHours->isNotEmpty()) {
            $recordedHours = $peakHours->keys()->map(fn($h) => (int)$h)->all();
            $earliest = min($recordedHours);
            $latest = max($recordedHours);
            if ($earliest < $minHour && $earliest >= 5) {
                $minHour = $earliest;
            }
            if ($latest > $maxHour && $latest <= 21) {
                $maxHour = $latest;
            }
        }

        $labels = [];
        $counts = [];
        for ($i = $minHour; $i <= $maxHour; $i++) {
            $labels[] = $i > 12 ? ($i - 12).' PM' : ($i == 12 ? '12 PM' : $i.' AM');
            $counts[] = (int) $peakHours->get($i, 0);
        }

        return ['labels' => $labels, 'data' => $counts];
    }

    /**
     * Compute dynamic Visit Volume chart data based on timeframe filter.
     */
    private function getVisitVolumeData(string $timeFilter): array
    {
        if ($timeFilter === 'today') {
            return $this->getPeakHoursData(now()->startOfDay());
        }

        if ($timeFilter === 'yearly' || $timeFilter === 'all') {
            $monthsToLookBack = $timeFilter === 'yearly' ? 11 : 23;
            $historyStart = now()->subMonths($monthsToLookBack)->startOfMonth();
            $visits = \App\Models\Consultation::where('created_at', '>=', $historyStart)
                ->select(\Illuminate\Support\Facades\DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(id) as count'))
                ->groupBy('month')
                ->pluck('count', 'month');

            $labels = [];
            $counts = [];
            for ($i = $monthsToLookBack; $i >= 0; $i--) {
                $dateObj = now()->subMonths($i);
                $labels[] = $dateObj->format('M Y');
                $counts[] = $visits->get($dateObj->format('Y-m'), 0);
            }

            return ['labels' => $labels, 'data' => $counts];
        }

        // Daily lookback (weekly = 6 days, monthly = 29 days)
        $daysToLookBack = $timeFilter === 'weekly' ? 6 : 29;
        $historyStart = now()->subDays($daysToLookBack)->startOfDay();
        $visits = \App\Models\Consultation::where('created_at', '>=', $historyStart)
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date, COUNT(id) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $counts = [];
        for ($i = $daysToLookBack; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $labels[] = $dateObj->format('M d');
            $counts[] = $visits->get($dateObj->format('Y-m-d'), 0);
        }

        return ['labels' => $labels, 'data' => $counts];
    }

    /**
     * Aggregated barangay consultation distribution with address fallback.
     */
    protected function getBarangayData($startDate)
    {
        $records = \App\Models\Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->select('consultations.id', 'patients.barangay', 'patients.address')
            ->get();

        $counts = [];
        foreach ($records as $r) {
            $normalized = \App\Models\Patient::normalizeBarangay($r->barangay, $r->address);
            if ($normalized) {
                $counts[$normalized] = ($counts[$normalized] ?? 0) + 1;
            }
        }

        arsort($counts);
        return array_slice($counts, 0, 10, true);
    }
}
