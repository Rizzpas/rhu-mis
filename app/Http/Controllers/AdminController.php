<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\PatientRecordController;
use App\Http\Controllers\Admin\RetentionController;
use App\Http\Controllers\Admin\StaffController;
use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\InventoryLog;
use App\Models\Patient;
use App\Models\User;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    /**
     * Admin Dashboard view.
     */
    public function index(Request $request)
    {
        $announcements = Announcement::latest()->take(5)->get();
        $recentLogs = AuditLog::with('user')->latest()->take(10)->get();

        $timeFilter = $request->get('time_filter', 'monthly');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => Carbon::create(2000, 1, 1),
        };
        $previousStartDate = match ($timeFilter) {
            'today' => now()->subDay()->startOfDay(),
            'weekly' => now()->subWeek()->startOfWeek(),
            'monthly' => now()->subMonth()->startOfMonth(),
            'yearly' => now()->subYear()->startOfYear(),
            default => Carbon::create(2000, 1, 1),
        };
        $previousEndDate = match ($timeFilter) {
            'today' => now()->subDay()->endOfDay(),
            'weekly' => now()->subWeek()->endOfWeek(),
            'monthly' => now()->subMonth()->endOfMonth(),
            'yearly' => now()->subYear()->endOfYear(),
            default => Carbon::create(2000, 1, 1),
        };

        $totalDoctors = User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->count();
        $presentDoctors = User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->present()->count();
        $presentNurses = User::whereIn('role', ['nurse', 'clinical_nurse', 'vitals_nurse'])->present()->count();
        $todayAppointments = Appointment::whereDate('preferred_date', today())->count();
        $pendingDeletionCount = Patient::whereNotNull('expires_at')->where('expires_at', '<=', now())->count();

        $currentPeriodConsultations = Consultation::where('created_at', '>=', $startDate)->count();
        $previousPeriodConsultations = Consultation::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();

        $trendPercentage = 0;
        if ($previousPeriodConsultations > 0) {
            $trendPercentage = round((($currentPeriodConsultations - $previousPeriodConsultations) / $previousPeriodConsultations) * 100, 1);
        } elseif ($currentPeriodConsultations > 0) {
            $trendPercentage = 100;
        }

        $visitVolumeData = $this->analyticsService->getVisitVolumeData($timeFilter);
        $peakHoursData = $this->analyticsService->getPeakHoursData($startDate);

        $topDiagnoses = Consultation::whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('diagnosis, COUNT(id) as count'))
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $classificationData = Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->whereNotNull('patients.classification')
            ->select(DB::raw('patients.classification, COUNT(DISTINCT consultations.patient_id) as count'))
            ->groupBy('patients.classification')
            ->pluck('count', 'classification');

        $severityData = Consultation::select(DB::raw('severity, COUNT(id) as count'))
            ->whereNotNull('severity')
            ->where('created_at', '>=', $startDate)
            ->groupBy('severity')
            ->pluck('count', 'severity');

        $avgWaitTimeRaw = Consultation::whereNotNull('consultation_start_time')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait_time'))
            ->value('avg_wait_time');

        $avgWaitTime = $avgWaitTimeRaw ? round($avgWaitTimeRaw) : 0;
        $barangayData = $this->analyticsService->getBarangayData($startDate);

        $demographics = DB::select("
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

        $workloadData = Consultation::select(DB::raw('
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
        $staffNames = User::whereIn('id', $staffIds)->get()->pluck('formatted_name', 'id');

        $workloadFormatted = [];
        foreach ($workloadData as $row) {
            if ($row->staff_id && isset($staffNames[$row->staff_id])) {
                $workloadFormatted[$staffNames[$row->staff_id]] = $row->count;
            }
        }
        arsort($workloadFormatted);
        $workloadFormatted = array_slice($workloadFormatted, 0, 10);

        $totalFollowupsNeeded = Consultation::where('is_followup_needed', true)
            ->where('created_at', '<', now()->subDays(30))
            ->count();

        $compliantFollowups = 0;
        if ($totalFollowupsNeeded > 0) {
            $compliantFollowups = DB::select('
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

        $topDispensed = InventoryLog::where('action', 'Dispensed')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('medicine_id', DB::raw('SUM(ABS(quantity_changed)) as total_dispensed'))
            ->groupBy('medicine_id')
            ->orderByDesc('total_dispensed')
            ->limit(10)
            ->with('medicine:id,name,generic_name')
            ->get();

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
                $item->label = Carbon::create($item->year, $item->month)->format('M Y');

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

    /**
     * Dashboard live counter stats API.
     */
    public function getStats()
    {
        $stats = Cache::remember('admin.dashboard.stats', 15, function () {
            return [
                'totalDoctors' => User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->count(),
                'presentDoctors' => User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->present()->count(),
                'presentNurses' => User::whereIn('role', ['nurse', 'clinical_nurse', 'vitals_nurse'])->present()->count(),
                'todayAppointments' => Appointment::whereDate('preferred_date', today())->count(),
                'avgWaitTime' => round(Consultation::whereNotNull('consultation_start_time')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait_time'))
                    ->value('avg_wait_time') ?? 0),
            ];
        });

        return response()->json($stats);
    }

    /**
     * Dashboard stats data API.
     */
    public function apiDashboardStats(Request $request)
    {
        $timeFilter = $request->get('time_filter', 'monthly');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => Carbon::create(2000, 1, 1),
        };
        $previousStartDate = match ($timeFilter) {
            'today' => now()->subDay()->startOfDay(),
            'weekly' => now()->subWeek()->startOfWeek(),
            'monthly' => now()->subMonth()->startOfMonth(),
            'yearly' => now()->subYear()->startOfYear(),
            default => Carbon::create(2000, 1, 1),
        };
        $previousEndDate = match ($timeFilter) {
            'today' => now()->subDay()->endOfDay(),
            'weekly' => now()->subWeek()->endOfWeek(),
            'monthly' => now()->subMonth()->endOfMonth(),
            'yearly' => now()->subYear()->endOfYear(),
            default => Carbon::create(2000, 1, 1),
        };

        $currentPeriodConsultations = Consultation::where('created_at', '>=', $startDate)->count();
        $previousPeriodConsultations = Consultation::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();

        $trendPercentage = 0;
        if ($previousPeriodConsultations > 0) {
            $trendPercentage = round((($currentPeriodConsultations - $previousPeriodConsultations) / $previousPeriodConsultations) * 100, 1);
        } elseif ($currentPeriodConsultations > 0) {
            $trendPercentage = 100;
        }

        $avgWaitTimeRaw = Consultation::whereNotNull('consultation_start_time')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait_time'))
            ->value('avg_wait_time');
        $avgWaitTime = $avgWaitTimeRaw ? round($avgWaitTimeRaw) : 0;

        $totalFollowupsNeeded = Consultation::where('is_followup_needed', true)
            ->where('created_at', '<', now()->subDays(30))
            ->count();

        $compliantFollowups = 0;
        if ($totalFollowupsNeeded > 0) {
            $compliantFollowups = DB::select('
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

    // =========================================================================
    // Backward-Compatible Delegation Methods
    // =========================================================================

    public function analytics(Request $request)
    {
        return app(AnalyticsController::class)->index($request);
    }

    public function exportAnalyticsCsv(Request $request)
    {
        return app(AnalyticsController::class)->exportCsv($request);
    }

    public function exportAnalyticsSummaryCsv(Request $request)
    {
        return app(AnalyticsController::class)->exportSummaryCsv($request);
    }

    public function apiChartData(Request $request, $chart)
    {
        return app(AnalyticsController::class)->apiChartData($request, $chart);
    }

    public function apiStaffProductivityData(Request $request)
    {
        return app(AnalyticsController::class)->apiStaffProductivityData($request);
    }

    public function staffIndex(Request $request)
    {
        return app(StaffController::class)->index($request);
    }

    public function storeStaff(Request $request)
    {
        return app(StaffController::class)->store($request);
    }

    public function updateStaff(Request $request, User $user)
    {
        return app(StaffController::class)->update($request, $user);
    }

    public function destroyStaff(User $user)
    {
        return app(StaffController::class)->destroy($user);
    }

    public function updateStaffStatus(Request $request, User $user)
    {
        return app(StaffController::class)->updateStatus($request, $user);
    }

    public function promoteToAdmin(User $user)
    {
        return app(StaffController::class)->promote($user);
    }

    public function bulkDeleteStaff(Request $request)
    {
        return app(StaffController::class)->bulkDelete($request);
    }

    public function bulkUpdateStaffStatus(Request $request)
    {
        return app(StaffController::class)->bulkStatus($request);
    }

    public function bulkPromoteStaff(Request $request)
    {
        return app(StaffController::class)->bulkPromote($request);
    }

    public function announcements(Request $request)
    {
        return app(AnnouncementController::class)->index($request);
    }

    public function createAnnouncement()
    {
        return app(AnnouncementController::class)->create();
    }

    public function editAnnouncement(Announcement $announcement)
    {
        return app(AnnouncementController::class)->edit($announcement);
    }

    public function storeAnnouncement(Request $request)
    {
        return app(AnnouncementController::class)->store($request);
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        return app(AnnouncementController::class)->update($request, $announcement);
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        return app(AnnouncementController::class)->destroy($announcement);
    }

    public function toggleAnnouncementStatus(Announcement $announcement, Request $request)
    {
        return app(AnnouncementController::class)->toggle($announcement, $request);
    }

    public function deleteAnnouncementImage(\App\Models\AnnouncementImage $image)
    {
        return app(AnnouncementController::class)->deleteImage($image);
    }

    public function bulkDeleteImages(Request $request)
    {
        return app(AnnouncementController::class)->bulkDeleteImages($request);
    }

    public function bulkDeleteAnnouncements(Request $request)
    {
        return app(AnnouncementController::class)->bulkDelete($request);
    }

    public function retentionIndex()
    {
        return app(RetentionController::class)->index();
    }

    public function extendRetention(Patient $patient)
    {
        return app(RetentionController::class)->extend($patient);
    }

    public function deleteRetention(Patient $patient)
    {
        return app(RetentionController::class)->destroy($patient);
    }

    public function bulkDeleteRetention(Request $request)
    {
        return app(RetentionController::class)->bulkDelete($request);
    }

    public function bulkExtendRetention(Request $request)
    {
        return app(RetentionController::class)->bulkExtend($request);
    }

    public function patientRecordsIndex(Request $request)
    {
        return app(PatientRecordController::class)->index($request);
    }

    public function showPatient(Request $request, Patient $patient)
    {
        return app(PatientRecordController::class)->show($request, $patient);
    }

    public function printItr(Request $request, Patient $patient)
    {
        return app(PatientRecordController::class)->printItr($request, $patient);
    }

    public function printAncillary(Request $request, Patient $patient)
    {
        return app(PatientRecordController::class)->printAncillary($request, $patient);
    }

    public function printAncillarySingle(Request $request, \App\Models\AncillaryRequest $ancillary)
    {
        return app(PatientRecordController::class)->printAncillarySingle($request, $ancillary);
    }

    public function auditLogs(Request $request)
    {
        return app(AuditLogController::class)->index($request);
    }

    public function exportAuditLogsCsv(Request $request)
    {
        return app(AuditLogController::class)->exportCsv($request);
    }

    public function contentIndex()
    {
        return app(ContentController::class)->index();
    }

    public function contentUpdate(Request $request)
    {
        return app(ContentController::class)->update($request);
    }

    public function getPeakHoursData($startDate): array
    {
        return $this->analyticsService->getPeakHoursData($startDate);
    }

    public function getVisitVolumeData(string $timeFilter): array
    {
        return $this->analyticsService->getVisitVolumeData($timeFilter);
    }

    public function getBarangayData($startDate)
    {
        return $this->analyticsService->getBarangayData($startDate);
    }
}
