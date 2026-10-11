<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardAndAnalyticsAuditTest extends TestCase
{
    protected User $admin;
    protected AnalyticsService $analyticsService;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'rhu-mis',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::beginTransaction();

        $this->admin = User::where('role', 'admin')->first()
            ?? User::create([
                'name' => 'Audit Test Admin',
                'email' => 'audit_test_admin_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('Password123!'),
                'role' => 'admin',
                'status' => 'Online',
            ]);

        $this->analyticsService = app(AnalyticsService::class);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_patient_is_pwd_column_and_classification_overlap(): void
    {
        $patient = Patient::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'dob' => now()->subYears(65)->format('Y-m-d'),
            'sex' => 'Male',
            'civil_status' => 'Single',
            'contact_number' => '09123456789',
            'barangay' => 'Biga I',
            'address' => 'Sample Address',
            'is_pwd' => true,
        ]);

        $this->assertTrue($patient->is_pwd);
        $this->assertEquals('Senior Citizen', $patient->classification);

        $patient->refresh();
        $this->assertTrue((bool) $patient->is_pwd);
        $this->assertEquals('Senior Citizen', $patient->classification);
    }

    public function test_login_and_failed_login_events_are_recorded_in_audit_logs(): void
    {
        // 1. Successful Login
        event(new Login('web', $this->admin, false));

        $loginLog = AuditLog::where('user_id', $this->admin->id)
            ->where('action', 'login')
            ->latest('id')
            ->first();

        $this->assertNotNull($loginLog);
        $this->assertEquals('login', $loginLog->action);
        $this->assertEquals($this->admin->email, $loginLog->changes['email'] ?? null);

        // 2. Failed Login
        event(new Failed('web', null, ['email' => 'intruder@test.com']));

        $failedLog = AuditLog::where('action', 'failed_login')
            ->latest('id')
            ->first();

        $this->assertNotNull($failedLog);
        $this->assertEquals('failed_login', $failedLog->action);
        $this->assertEquals('intruder@test.com', $failedLog->changes['email'] ?? null);
    }

    public function test_analytics_service_methods_return_complete_census_and_matrices(): void
    {
        $startDate = now()->startOfMonth();

        // Census
        $census = $this->analyticsService->getPatientCensus($startDate);
        $this->assertArrayHasKey('total_registered', $census);
        $this->assertArrayHasKey('new_registered', $census);
        $this->assertArrayHasKey('unique_served', $census);
        $this->assertArrayHasKey('unique_patients_served', $census);
        $this->assertArrayHasKey('total_consultations', $census);
        $this->assertArrayHasKey('male_registered', $census);
        $this->assertArrayHasKey('female_registered', $census);
        $this->assertArrayHasKey('male_percentage', $census);
        $this->assertArrayHasKey('female_percentage', $census);
        $this->assertArrayHasKey('philhealth_registered', $census);
        $this->assertArrayHasKey('philhealth_count', $census);
        $this->assertArrayHasKey('philhealth_percentage', $census);
        $this->assertArrayHasKey('trend_percentage', $census);

        // Classification Breakdown
        $classification = $this->analyticsService->getClassificationBreakdown($startDate);
        $this->assertArrayHasKey('total_registered', $classification);
        $this->assertArrayHasKey('unique_served_total', $classification);
        $this->assertArrayHasKey('categories', $classification);
        $this->assertArrayHasKey('pediatric', $classification['categories']);
        $this->assertArrayHasKey('regular_adult', $classification['categories']);
        $this->assertArrayHasKey('senior_citizen', $classification['categories']);
        $this->assertArrayHasKey('registered_pct', $classification['categories']['pediatric']);
        $this->assertArrayHasKey('served_pct', $classification['categories']['pediatric']);
        $this->assertArrayHasKey('pwd', $classification);
        $this->assertArrayHasKey('registered_count', $classification['pwd']);
        $this->assertArrayHasKey('donut_chart', $classification);

        // Top Diagnoses
        $top = $this->analyticsService->getTopDiagnoses($startDate, 10);
        $this->assertNotNull($top);

        // Inventory Alerts
        $inv = $this->analyticsService->getInventoryAlerts();
        $this->assertArrayHasKey('total_medicines', $inv);
        $this->assertArrayHasKey('out_of_stock', $inv);
        $this->assertArrayHasKey('low_stock', $inv);
        $this->assertArrayHasKey('expiring_soon', $inv);

        // Staff Summary
        $staff = $this->analyticsService->getStaffSummary();
        $this->assertArrayHasKey('total', $staff);
        $this->assertArrayHasKey('on_duty', $staff);
        $this->assertArrayHasKey('total_doctors', $staff);
        $this->assertArrayHasKey('total_nurses', $staff);

        // Security Audit Stats
        $sec = $this->analyticsService->getSecurityAuditStats($startDate);
        $this->assertArrayHasKey('total_events', $sec);
        $this->assertArrayHasKey('logins', $sec);
        $this->assertArrayHasKey('failed_logins', $sec);
    }

    public function test_admin_dashboard_and_stats_data_endpoint(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Municipal Patient Census &amp; Service Utilization', false);
        $response->assertSee('Patient Classification &amp; Demographics Breakdown', false);
        $response->assertSee('Action Needed &bull; Immediate Operational Alerts', false);
        $response->assertSee('Facility Influx &amp; Daily Traffic Pulse', false);
        $response->assertSee('id="visitVolumeChart"', false);
        $response->assertSee('id="peakHoursChart"', false);
        $response->assertSee('cdn.jsdelivr.net/npm/chart.js', false);
        $response->assertSee('Core Module Telemetry &amp; System Health', false);

        $statsResponse = $this->actingAs($this->admin)->get(route('admin.dashboard.statsData', ['time_filter' => 'monthly']));
        $statsResponse->assertStatus(200);
        $statsResponse->assertJsonStructure([
            'census' => ['total_registered', 'unique_patients_served', 'male_percentage', 'female_percentage', 'philhealth_percentage'],
            'classification' => ['total_registered', 'unique_served_total', 'categories', 'pwd', 'donut_chart'],
            'securityStats' => ['total_events', 'logins', 'failed_logins'],
            'currentPeriodConsultations',
            'trendPercentage',
            'visitVolumeData' => ['labels', 'data'],
            'peakHoursData' => ['labels', 'data'],
        ]);
    }

    public function test_admin_dashboard_custom_range_and_print_dossier(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('printDashboardReport', false);
        $response->assertSee('window.printIsolated', false);
        $response->assertSee('Custom Range', false);
        $response->assertSee('RHU Executive Management Dossier', false);

        $customStatsResponse = $this->actingAs($this->admin)->get(route('admin.dashboard.statsData', [
            'time_filter' => 'custom',
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->toDateString(),
        ]));
        $customStatsResponse->assertStatus(200);
        $customStatsResponse->assertJsonStructure([
            'dateRangeLabel',
            'timeFilter',
            'startDate',
            'endDate',
            'census',
            'classification',
            'securityStats',
            'visitVolumeData',
            'peakHoursData',
        ]);
        $json = $customStatsResponse->json();
        $this->assertEquals('custom', $json['timeFilter']);
        $this->assertNotEmpty($json['dateRangeLabel']);
    }

    public function test_admin_analytics_and_export_summary_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.analytics'));
        $response->assertStatus(200);
        $response->assertSee('Total Registered Patients', false);
        $response->assertSee('Patient Classification Matrix', false);
        $response->assertSee('Top 10 Diagnoses (Morbidity Surveillance)', false);

        $chartResp = $this->actingAs($this->admin)->get(route('admin.analytics.chart', ['chart' => 'classification', 'time_filter' => 'monthly']));
        $chartResp->assertStatus(200);
        $chartResp->assertJsonStructure(['labels', 'data', 'total', 'categories', 'pwd']);

        $csvResp = $this->actingAs($this->admin)->get(route('admin.analytics.export-summary-csv', ['time_filter' => 'monthly']));
        $csvResp->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $csvResp->headers->get('Content-Type'));
        $content = $csvResp->streamedContent();
        $this->assertStringContainsString('ANALYTICS SUMMARY REPORT', $content);
        $this->assertStringContainsString('=== PATIENT CENSUS & POPULATION OVERVIEW ===', $content);
        $this->assertStringContainsString('=== 3. TOP CLINICAL DIAGNOSES (MORBIDITY RANKING) ===', $content);
        $this->assertStringContainsString('=== 4. PATIENT CLASSIFICATION & VULNERABILITY BREAKDOWN ===', $content);
        $this->assertStringContainsString('Pediatric (0-12 yrs)', $content);
        $this->assertStringContainsString('Senior Citizen (60+ yrs)', $content);
        $this->assertStringContainsString('PWD (Persons with Disability)', $content);
    }
}
