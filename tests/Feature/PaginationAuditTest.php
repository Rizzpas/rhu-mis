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
        $this->assertEquals(15, $staff->perPage());
    }

    public function test_archive_show_is_paginated(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.archive.show', 'ancillary'));
        $response->assertOk();

        $records = $response->viewData('records');
        $this->assertInstanceOf(LengthAwarePaginator::class, $records);
        $this->assertEquals(15, $records->perPage());
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
}
