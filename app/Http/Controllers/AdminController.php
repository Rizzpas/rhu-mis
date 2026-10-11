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
        $filterData = $this->resolveTimeFilter($request);
        $timeFilter = $filterData['timeFilter'];
        $startDate = $filterData['startDate'];
        $endDate = $filterData['endDate'];
        $previousStartDate = $filterData['previousStartDate'];
        $previousEndDate = $filterData['previousEndDate'];
        $dateRangeLabel = $filterData['dateRangeLabel'];

        // 1. Patient Census & Demographics
        $census = $this->analyticsService->getPatientCensus($startDate, $previousStartDate, $previousEndDate);

        // 2. Classification Breakdown (All-Time Registered & Period Served)
        $classification = $this->analyticsService->getClassificationBreakdown($startDate);

        // 3. Operational Action Items
        $inventoryAlerts = $this->analyticsService->getInventoryAlerts();
        $followupStats = $this->analyticsService->getFollowupStats();
        $pendingAnnouncements = Announcement::where('status', 'pending')->count();
        $pendingDeletionCount = Patient::whereNotNull('expires_at')->where('expires_at', '<=', now())->count();

        // 4. System Module Summaries
        $staffSummary = $this->analyticsService->getStaffSummary();
        $announcementsSummary = $this->analyticsService->getAnnouncementsSummary();
        $archiveCategories = app(\App\Http\Controllers\ArchiveController::class)->getCategories();
        $securityStats = $this->analyticsService->getSecurityAuditStats($startDate);

        // 5. Activity Feeds
        $recentLogs = AuditLog::with('user')->latest()->take(6)->get();
        $announcements = Announcement::latest()->take(5)->get();
        $todayAppointments = Appointment::whereDate('preferred_date', today())->count();

        // 6. Operational Traffic Snapshot (Daily Volume, Peak Hours, Queue Wait Time)
        $visitVolumeData = $this->analyticsService->getVisitVolumeData($timeFilter, $startDate, $endDate);
        $peakHoursData = $this->analyticsService->getPeakHoursData($startDate, $endDate);
        $avgWaitTime = round(Consultation::whereNotNull('consultation_start_time')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait_time'))
            ->value('avg_wait_time') ?? 0);
        $currentPeriodConsultations = $census['total_consultations'];
        $trendPercentage = $census['trend_percentage'];
        $presentDoctors = $staffSummary['doctors_present'];
        $presentNurses = $staffSummary['nurses_present'];
        $totalArchives = collect($archiveCategories)->sum('count');

        return view('admin.dashboard', compact(
            'timeFilter',
            'dateRangeLabel',
            'startDate',
            'endDate',
            'census',
            'classification',
            'inventoryAlerts',
            'followupStats',
            'pendingAnnouncements',
            'pendingDeletionCount',
            'staffSummary',
            'announcementsSummary',
            'archiveCategories',
            'securityStats',
            'recentLogs',
            'announcements',
            'todayAppointments',
            'visitVolumeData',
            'peakHoursData',
            'avgWaitTime',
            'currentPeriodConsultations',
            'trendPercentage',
            'presentDoctors',
            'presentNurses',
            'totalArchives'
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
        $filterData = $this->resolveTimeFilter($request);
        $timeFilter = $filterData['timeFilter'];
        $startDate = $filterData['startDate'];
        $endDate = $filterData['endDate'];
        $previousStartDate = $filterData['previousStartDate'];
        $previousEndDate = $filterData['previousEndDate'];
        $dateRangeLabel = $filterData['dateRangeLabel'];

        $census = $this->analyticsService->getPatientCensus($startDate, $previousStartDate, $previousEndDate);
        $classification = $this->analyticsService->getClassificationBreakdown($startDate);
        $securityStats = $this->analyticsService->getSecurityAuditStats($startDate);
        $avgWaitTime = round(Consultation::whereNotNull('consultation_start_time')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait_time'))
            ->value('avg_wait_time') ?? 0);

        $visitVolumeData = $this->analyticsService->getVisitVolumeData($timeFilter, $startDate, $endDate);
        $peakHoursData = $this->analyticsService->getPeakHoursData($startDate, $endDate);

        return response()->json([
            'timeFilter' => $timeFilter,
            'dateRangeLabel' => $dateRangeLabel,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'census' => $census,
            'classification' => $classification,
            'securityStats' => $securityStats,
            'currentPeriodConsultations' => number_format($census['total_consultations']),
            'currentPeriodConsultationsRaw' => $census['total_consultations'],
            'trendPercentage' => $census['trend_percentage'],
            'avgWaitTime' => $avgWaitTime,
            'visitVolumeData' => $visitVolumeData,
            'peakHoursData' => $peakHoursData,
        ]);
    }

    /**
     * Resolve time filter, start/end dates, and labels for dashboard analytics.
     */
    protected function resolveTimeFilter(Request $request): array
    {
        $timeFilter = $request->get('time_filter', 'monthly');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        if (($timeFilter === 'custom' || ($customStart && $customEnd)) && $customStart && $customEnd) {
            $timeFilter = 'custom';
            $startDate = Carbon::parse($customStart)->startOfDay();
            $endDate = Carbon::parse($customEnd)->endOfDay();
            $diffDays = max(1, $startDate->diffInDays($endDate));
            $previousStartDate = (clone $startDate)->subDays($diffDays)->startOfDay();
            $previousEndDate = (clone $startDate)->subSecond();
            $dateRangeLabel = 'Custom Range (' . $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y') . ')';
        } else {
            $startDate = match ($timeFilter) {
                'today' => now()->startOfDay(),
                'weekly' => now()->startOfWeek(),
                'monthly' => now()->startOfMonth(),
                'yearly' => now()->startOfYear(),
                default => Carbon::create(2000, 1, 1),
            };
            $endDate = now()->endOfDay();
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
            $dateRangeLabel = match ($timeFilter) {
                'today' => 'Today (' . now()->format('F d, Y') . ')',
                'weekly' => 'This Week (' . now()->startOfWeek()->format('M d') . ' – ' . now()->endOfWeek()->format('M d, Y') . ')',
                'monthly' => 'This Month (' . now()->format('F Y') . ')',
                'yearly' => 'This Year (' . now()->format('Y') . ')',
                default => 'All Time (Complete Historical Registry)',
            };
        }

        return [
            'timeFilter' => $timeFilter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'previousStartDate' => $previousStartDate,
            'previousEndDate' => $previousEndDate,
            'dateRangeLabel' => $dateRangeLabel,
        ];
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
