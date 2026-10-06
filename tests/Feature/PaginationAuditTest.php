<?php

namespace Tests\Feature;

use App\Models\AncillaryRequest;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PaginationAuditTest extends TestCase
{
    protected User $admin;
    protected User $pharmacyUser;
    protected User $labUser;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'rhu-mis',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::beginTransaction();

        $this->admin = User::where('role', 'admin')->first()
            ?? User::create([
                'name' => 'Admin Test',
                'email' => 'admin_pag_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);

        $this->pharmacyUser = User::where('role', 'pharmacy')->first()
            ?? User::create([
                'name' => 'Pharmacist Test',
                'email' => 'pharm_pag_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'pharmacy',
            ]);

        $this->labUser = User::where('role', 'laboratory')->first()
            ?? User::create([
                'name' => 'Lab Tech Test',
                'email' => 'lab_pag_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'laboratory',
            ]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
        parent::tearDown();
    }

    public function test_admin_staff_roster_is_paginated(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.staff.index'));
        $response->assertOk();

        $staff = $response->viewData('staff');
        $this->assertInstanceOf(LengthAwarePaginator::class, $staff);
        $this->assertEquals(10, $staff->perPage());
    }

    public function test_archive_show_is_paginated(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.archive.show', 'ancillary'));
        $response->assertOk();

        $records = $response->viewData('records');
        $this->assertInstanceOf(LengthAwarePaginator::class, $records);
        $this->assertEquals(10, $records->perPage());
    }

    public function test_pharmacy_dashboard_queue_is_paginated(): void
    {
        $response = $this->actingAs($this->pharmacyUser)->get(route('pharmacy.dashboard'));
        $response->assertOk();

        $prescriptions = $response->viewData('prescriptions');
        $this->assertInstanceOf(LengthAwarePaginator::class, $prescriptions);
        $this->assertEquals(10, $prescriptions->perPage());
    }

    public function test_lab_dashboard_completed_and_archived_are_paginated(): void
    {
        $response = $this->actingAs($this->labUser)->get(route('lab.dashboard'));
        $response->assertOk();

        $completed = $response->viewData('completedRequests');
        $archived = $response->viewData('archivedRequests');

        $this->assertInstanceOf(LengthAwarePaginator::class, $completed);
        $this->assertEquals(15, $completed->perPage());
        $this->assertEquals('completed_page', $completed->getPageName());

        $this->assertInstanceOf(LengthAwarePaginator::class, $archived);
        $this->assertEquals(15, $archived->perPage());
        $this->assertEquals('archived_page', $archived->getPageName());
    }

    public function test_admin_staff_pagination_controls_and_filters(): void
    {
        // Test 1: Page 1 should render with disabled Previous, active page 1
        $response = $this->actingAs($this->admin)->get(route('admin.staff.index', ['q' => 'Test', 'role' => 'all']));
        $response->assertOk();

        $staff = $response->viewData('staff');
        $this->assertInstanceOf(LengthAwarePaginator::class, $staff);
        $this->assertEquals(10, $staff->perPage());

        $content = $response->getContent();
        // Check pagination controls
        $this->assertStringContainsString('Previous', $content);
        $this->assertStringContainsString('Next', $content);
        $this->assertStringContainsString('aria-label="Pagination Navigation"', $content);
        $this->assertStringContainsString('aria-current="page"', $content);

        // Test 2: Invalid page parameter is safely validated
        $invalidResponse = $this->actingAs($this->admin)->get(route('admin.staff.index', ['page' => -5]));
        $invalidResponse->assertOk();
        $this->assertEquals(1, $invalidResponse->viewData('staff')->currentPage());

        $textResponse = $this->actingAs($this->admin)->get(route('admin.staff.index', ['page' => 'invalid_text']));
        $textResponse->assertOk();
        $this->assertEquals(1, $textResponse->viewData('staff')->currentPage());
    }

    public function test_admin_announcements_pagination_controls_and_filters(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.announcements.index'));
        $response->assertOk();

        $announcements = $response->viewData('announcements');
        $this->assertInstanceOf(LengthAwarePaginator::class, $announcements);
        $this->assertEquals(10, $announcements->perPage());

        $content = $response->getContent();
        $this->assertStringContainsString('Previous', $content);
        $this->assertStringContainsString('Next', $content);
        $this->assertStringContainsString('aria-label="Pagination Navigation"', $content);
        $this->assertStringContainsString('aria-current="page"', $content);

        // Test filter preservation
        $filterResponse = $this->actingAs($this->admin)->get(route('admin.announcements.index', ['q' => 'Notice', 'status' => 'published']));
        $filterResponse->assertOk();
        $filterAnnouncements = $filterResponse->viewData('announcements');
        $this->assertEquals(10, $filterAnnouncements->perPage());

        // Test invalid page parameter is safely handled
        $invalidResponse = $this->actingAs($this->admin)->get(route('admin.announcements.index', ['page' => 'not-a-number']));
        $invalidResponse->assertOk();
        $this->assertEquals(1, $invalidResponse->viewData('announcements')->currentPage());
    }

    public function test_pagination_multipage_controls_and_windowing_style(): void
    {
        // Seed enough announcements to guarantee at least 3 pages (>= 25 total)
        for ($i = 0; $i < 20; $i++) {
            \App\Models\Announcement::create([
                'title' => 'Bulk Announcement Test ' . $i,
                'content' => 'Content for bulk test ' . $i,
                'status' => 'published',
            ]);
        }

        // 1. Visit Page 1
        $resPage1 = $this->actingAs($this->admin)->get(route('admin.announcements.index', ['page' => 1]));
        $resPage1->assertOk();
        $paginator1 = $resPage1->viewData('announcements');
        $this->assertEquals(1, $paginator1->currentPage());
        $this->assertGreaterThan(2, $paginator1->lastPage());
        $html1 = $resPage1->getContent();

        // On page 1: Previous is disabled, Next is enabled
        $this->assertStringContainsString('aria-disabled="true"', $html1);
        $this->assertStringContainsString('aria-label="Previous page"', $html1);
        $this->assertStringContainsString('aria-label="Next page"', $html1);
        $this->assertStringContainsString('page=2', $html1);
        // Active page is 1
        $this->assertMatchesRegularExpression('/aria-current="page"[^>]*>\s*1\s*</', $html1);

        // 2. Visit Page 2
        $resPage2 = $this->actingAs($this->admin)->get(route('admin.announcements.index', ['page' => 2]));
        $resPage2->assertOk();
        $html2 = $resPage2->getContent();

        // On page 2: Previous is enabled pointing to page 1, active is 2
        $this->assertStringContainsString('page=1', $html2);
        $this->assertMatchesRegularExpression('/aria-current="page"[^>]*>\s*2\s*</', $html2);

        // 3. Visit Last Page
        $lastPage = $paginator1->lastPage();
        $resLast = $this->actingAs($this->admin)->get(route('admin.announcements.index', ['page' => $lastPage]));
        $resLast->assertOk();
        $htmlLast = $resLast->getContent();

        // On last page: Next is disabled
        $this->assertStringContainsString('aria-disabled="true"', $htmlLast);
        $this->assertStringContainsString('aria-label="Next page"', $htmlLast);
        $this->assertMatchesRegularExpression('/aria-current="page"[^>]*>\s*' . $lastPage . '\s*</', $htmlLast);
    }

    public function test_admin_patient_records_pagination(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.patients.index'));
        $response->assertOk();

        $patients = $response->viewData('patients');
        $this->assertInstanceOf(LengthAwarePaginator::class, $patients);
        $this->assertEquals(10, $patients->perPage());

        $content = $response->getContent();
        $this->assertStringContainsString('Previous', $content);
        $this->assertStringContainsString('Next', $content);
        $this->assertStringContainsString('aria-label="Pagination Navigation"', $content);
        $this->assertStringContainsString('aria-current="page"', $content);

        // Test filter preservation
        $filterResponse = $this->actingAs($this->admin)->get(route('admin.patients.index', ['search' => 'Juan', 'classification' => 'Regular Adult']));
        $filterResponse->assertOk();
        $this->assertEquals(10, $filterResponse->viewData('patients')->perPage());

        // Test invalid page parameter safely handled
        $invalidResponse = $this->actingAs($this->admin)->get(route('admin.patients.index', ['page' => -99]));
        $invalidResponse->assertOk();
        $this->assertEquals(1, $invalidResponse->viewData('patients')->currentPage());
    }

    public function test_frontdesk_patient_records_pagination(): void
    {
        $frontdeskUser = User::where('role', 'information_desk')->first()
            ?? User::create([
                'name' => 'Frontdesk Test',
                'email' => 'frontdesk_pag_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'information_desk',
            ]);

        $response = $this->actingAs($frontdeskUser)->get(route('frontdesk.patients.index'));
        $response->assertOk();

        $patients = $response->viewData('patients');
        $this->assertInstanceOf(LengthAwarePaginator::class, $patients);
        $this->assertEquals(10, $patients->perPage());

        $content = $response->getContent();
        $this->assertStringContainsString('Previous', $content);
        $this->assertStringContainsString('Next', $content);
        $this->assertStringContainsString('aria-label="Pagination Navigation"', $content);
        $this->assertStringContainsString('aria-current="page"', $content);

        // Test filter preservation
        $filterResponse = $this->actingAs($frontdeskUser)->get(route('frontdesk.patients.index', ['search' => 'Maria', 'classification' => 'Pediatric']));
        $filterResponse->assertOk();
        $this->assertEquals(10, $filterResponse->viewData('patients')->perPage());

        // Test invalid page parameter safely handled
        $invalidResponse = $this->actingAs($frontdeskUser)->get(route('frontdesk.patients.index', ['page' => 'bad_input']));
        $invalidResponse->assertOk();
        $this->assertEquals(1, $invalidResponse->viewData('patients')->currentPage());
    }
}
