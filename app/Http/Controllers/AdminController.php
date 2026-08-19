<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\InventoryLog;
use App\Models\Patient;
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
        $peakHours = \App\Models\Consultation::where('created_at', '>=', $startDate)
            ->select(\Illuminate\Support\Facades\DB::raw('HOUR(created_at) as hour, COUNT(id) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $hourLabels = [];
        $hourCounts = [];
        for ($i = 8; $i <= 17; $i++) { // Operating hours 8 AM - 5 PM
            $hourLabels[] = $i > 12 ? ($i - 12).' PM' : ($i == 12 ? '12 PM' : $i.' AM');
            $hourCounts[] = $peakHours->get($i, 0);
        }
        $peakHoursData = ['labels' => $hourLabels, 'data' => $hourCounts];

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
        $barangayData = \App\Models\Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->whereNotNull('patients.barangay')
            ->where('patients.barangay', '!=', '')
            ->select(\Illuminate\Support\Facades\DB::raw('patients.barangay, COUNT(consultations.id) as count'))
            ->groupBy('patients.barangay')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'barangay');

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
        $peakHours = \App\Models\Consultation::where('created_at', '>=', $startDate)
            ->select(\Illuminate\Support\Facades\DB::raw('HOUR(created_at) as hour, COUNT(id) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $hourLabels = [];
        $hourCounts = [];
        for ($i = 8; $i <= 17; $i++) {
            $hourLabels[] = $i > 12 ? ($i - 12).' PM' : ($i == 12 ? '12 PM' : $i.' AM');
            $hourCounts[] = $peakHours->get($i, 0);
        }
        $peakHoursData = ['labels' => $hourLabels, 'data' => $hourCounts];

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
        $barangayData = \App\Models\Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->whereNotNull('patients.barangay')
            ->where('patients.barangay', '!=', '')
            ->select(\Illuminate\Support\Facades\DB::raw('patients.barangay, COUNT(consultations.id) as count'))
            ->groupBy('patients.barangay')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'barangay');

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
            $peakHours = \App\Models\Consultation::where('created_at', '>=', $startDate)
                ->select(\Illuminate\Support\Facades\DB::raw('HOUR(created_at) as hour, COUNT(id) as count'))
                ->groupBy('hour')
                ->pluck('count', 'hour');

            $labels = [];
            $counts = [];
            for ($i = 8; $i <= 17; $i++) {
                $labels[] = $i > 12 ? ($i - 12).' PM' : ($i == 12 ? '12 PM' : $i.' AM');
                $counts[] = $peakHours->get($i, 0);
            }

            return response()->json(['labels' => $labels, 'data' => $counts]);
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
            $data = \App\Models\Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
                ->where('consultations.created_at', '>=', $startDate)
                ->whereNotNull('patients.barangay')
                ->where('patients.barangay', '!=', '')
                ->select(\Illuminate\Support\Facades\DB::raw('patients.barangay, COUNT(consultations.id) as count'))
                ->groupBy('patients.barangay')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'barangay');

            return response()->json(['labels' => array_keys($data->toArray()), 'data' => array_values($data->toArray())]);
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
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $staff = $query->with('practitionerSchedules')->orderBy('created_at', 'desc')->get();

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

        if ($request->ajax()) {
            return view('admin.audit.index', compact('logs'))->renderSections()['content'];
        }

        return view('admin.audit.index', compact('logs'));
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

        if ($request->filled('date_posted')) {
            $query->whereDate('created_at', $request->date_posted);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $announcements = $query->latest()->paginate(10);

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
            'start_time' => 'nullable|date_format:H:i',
            'content' => 'required|string',

            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,gif,mp4|max:51200', // Increased max size for video (50MB)
            'display_type' => 'required|in:list,carousel',
            'display_mode' => 'required|in:standard,infographic',
            'sections' => 'nullable|array',
            'sections.*.content' => 'nullable|string',
            'sections.*.image' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:51200',
            'sections.*.video_url' => 'nullable|url',
            'sections.*.layout' => 'nullable|in:left,right,middle',
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
            'start_time' => $validated['start_time'] ?? null,
            'content' => $validated['content'],
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
            'start_time' => 'nullable|date_format:H:i',
            'content' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'display_type' => 'required|in:list,carousel',
            // Update existing sections
            'existing_sections' => 'nullable|array',
            'existing_sections.*.content' => 'nullable|string',
            'existing_sections.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'existing_sections.*.layout' => 'nullable|in:left,right,middle',
            // Simple gallery append for now
            'new_sections' => 'nullable|array',
            'new_sections.*.content' => 'nullable|string',
            'new_sections.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'new_sections.*.layout' => 'nullable|in:left,right,middle',
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
            'start_time' => $validated['start_time'] ?? null,
            'content' => $validated['content'],
            'image_path' => $announcement->image_path,
            'display_type' => $validated['display_type'] ?? 'list',
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
            'avatar' => 'nullable|image|max:2048',
        ]);

        // Only super_admin can create admin/super_admin accounts
        if (in_array($validated['role'], ['admin', 'super_admin'])) {
            \Illuminate\Support\Facades\Gate::authorize('promote-admin');
        }

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('staff', 'public');
        }

        $name = ucwords(strtolower(trim(str_replace(['Dr. ', 'Dr '], '', $validated['name']))));

        $user = \App\Models\User::create([
            'name' => $name,
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'status' => $validated['status'],
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
        // Prevent regular admin from editing admin or super_admin accounts
        if (in_array($user->role, ['admin', 'super_admin'])) {
            \Illuminate\Support\Facades\Gate::authorize('manage-admins');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:super_admin,admin,regular_doctor,pedia_doctor,laboratory,radiology,vitals_nurse,clinical_nurse,information_desk,pharmacy',
            'status' => 'required|string',
            'schedule' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|max:2048',
        ]);

        // Only super_admin can assign admin/super_admin roles
        if (in_array($validated['role'], ['admin', 'super_admin'])) {
            \Illuminate\Support\Facades\Gate::authorize('promote-admin');
        }

        $name = ucwords(strtolower(trim(str_replace(['Dr. ', 'Dr '], '', $validated['name']))));

        $data = [
            'name' => $name,
            'email' => $validated['email'],
            'status' => $validated['status'],
            // 'schedule' string column is no longer used directly
        ];

        if (! empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($user->avatar_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_path);
            }
            $data['avatar_path'] = $request->file('avatar')->store('staff', 'public');
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

        $updateData = ['status' => $request->status];

        // If an admin forces someone to be "Present", artificially bump their heartbeat
        // so they instantly appear online without needing to log in.
        if (in_array($request->status, ['Present', 'Active', 'Online', 'Available', 'In Office'])) {
            $updateData['last_activity_at'] = now();
        }

        $user->update($updateData);

        return back()->with('success', 'Staff status updated successfully!');
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
        \Illuminate\Support\Facades\Gate::authorize('force-delete');

        // Permanently delete the patient (and their related records depending on cascading rules)
        $patient->forceDelete();

        return back()->with('success', 'Inactive patient record permanently deleted.');
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

        $patients = $query->withCount('consultations')->orderBy('last_name', 'asc')->orderBy('first_name', 'asc')->orderBy('created_at', 'desc')->paginate(15);

        AuditLog::record('Viewed Patient Master List');

        if ($request->ajax()) {
            return view('admin.patients.index', compact('patients'))->renderSections()['content'];
        }

        return view('admin.patients.index', compact('patients'));
    }

    public function showPatient(Patient $patient)
    {
        $patient->load([
            'consultations' => function ($q) {
                $q->orderBy('consultation_date', 'desc')->orderBy('created_at', 'desc');
            },
            'consultations.doctor',
            'consultations.nurse',
            'medicalCases.consultation.ancillaryRequests.technician',
            'medicalCases.consultation.prescriptionRecord.items',
        ]);

        AuditLog::record('Viewed Full Patient Record', $patient);

        return view('admin.patients.show', compact('patient'));
    }

    public function bulkDeleteAnnouncements(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:announcements,id',
        ]);

        Announcement::whereIn('id', $request->ids)->delete();

        return back()->with('success', count($request->ids).' announcements archived successfully!');
    }

    public function bulkDeleteStaff(Request $request)
    {
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

    public function bulkDeleteRetention(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('force-delete');

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

        return view('admin.content.index', compact('settings'));
    }

    public function contentUpdate(Request $request)
    {
        // Only super_admin can modify system content settings
        \Illuminate\Support\Facades\Gate::authorize('manage-content');

        $request->validate([
            'settings' => 'required|array',
            'hero_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if ($request->hasFile('hero_image_file')) {
            $path = $request->file('hero_image_file')->store('content', 'uploads');
            \App\Models\SiteSetting::set('hero_image', 'uploads/'.$path);
        }

        foreach ($request->settings as $key => $value) {
            \App\Models\SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if ($request->has('steps')) {
            foreach ($request->steps as $unitSlug => $unitSteps) {
                if (is_array($unitSteps)) {
                    $steps = array_values(array_filter($unitSteps, function ($s) {
                        return ! empty($s['title']) || ! empty($s['description']);
                    }));

                    // Handle step image uploads
                    foreach ($steps as $index => &$step) {
                        // Check for new uploaded image
                        if ($request->hasFile("step_images.{$unitSlug}.{$index}")) {
                            $file = $request->file("step_images.{$unitSlug}.{$index}");
                            $path = $file->store('content/steps', 'uploads');
                            $step['image'] = $path;
                        }
                        // If image was cleared (empty string), keep it empty
                        // Otherwise preserve the existing image path from hidden field
                    }
                    unset($step);

                    \App\Models\SiteSetting::updateOrCreate(
                        ['key' => 'steps_data_'.$unitSlug],
                        ['group' => 'steps', 'value' => json_encode($steps), 'type' => 'json']
                    );
                }
            }
        }

        if ($request->has('faq')) {
            $faq = array_values(array_filter($request->faq, function ($f) {
                return ! empty($f['question']) || ! empty($f['answer']);
            }));
            \App\Models\SiteSetting::where('key', 'faq_items')->update(['value' => json_encode($faq)]);
        }

        if ($request->has('privacy_list')) {
            $items = array_values(array_filter($request->privacy_list));
            \App\Models\SiteSetting::where('key', 'privacy_items')->update(['value' => json_encode($items)]);
        }

        \App\Models\SiteSetting::clearCache();

        \App\Models\AuditLog::record('Updated Landing Page Content');

        return back()->with('success', 'Content updated successfully!');
    }

    public function printItr(Request $request, Patient $patient)
    {
        $caseIds = $request->input('cases', []);

        if (empty($caseIds)) {
            // Print all if none specified
            $cases = $patient->medicalCases()->orderBy('created_at', 'desc')->get();
        } else {
            // Convert to array if it comes as a comma-separated string or just an array
            if (is_string($caseIds)) {
                $caseIds = explode(',', $caseIds);
            }
            $cases = $patient->medicalCases()->whereIn('id', $caseIds)->orderBy('created_at', 'desc')->get();
        }

        return view('admin.patients.itr', compact('patient', 'cases'));
    }

    /**
     * Compute dynamic Visit Volume chart data based on timeframe filter.
     */
    private function getVisitVolumeData(string $timeFilter): array
    {
        if ($timeFilter === 'today') {
            $startDate = now()->startOfDay();
            $visits = \App\Models\Consultation::where('created_at', '>=', $startDate)
                ->select(\Illuminate\Support\Facades\DB::raw('HOUR(created_at) as hour, COUNT(id) as count'))
                ->groupBy('hour')
                ->pluck('count', 'hour');

            $labels = [];
            $counts = [];
            for ($i = 8; $i <= 17; $i++) {
                $labels[] = $i > 12 ? ($i - 12).' PM' : ($i == 12 ? '12 PM' : $i.' AM');
                $counts[] = $visits->get($i, 0);
            }

            return ['labels' => $labels, 'data' => $counts];
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
}
