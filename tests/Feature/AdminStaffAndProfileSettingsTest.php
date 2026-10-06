<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminStaffAndProfileSettingsTest extends TestCase
{
    protected User $admin;
    protected User $superAdmin;
    protected User $doctor;

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

        $this->superAdmin = User::create([
            'name' => 'Super Admin Test',
            'email' => 'super_admin_test_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->doctor = User::create([
            'name' => 'Doctor Test',
            'email' => 'doctor_test_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
        parent::tearDown();
    }

    public function test_staff_management_scroll_is_removed_and_per_page_options_work(): void
    {
        // Test default per_page is 10
        $response = $this->actingAs($this->admin)->get(route('admin.staff.index'));
        $response->assertOk();

        $staff = $response->viewData('staff');
        $this->assertInstanceOf(LengthAwarePaginator::class, $staff);
        $this->assertEquals(10, $staff->perPage());

        $content = $response->getContent();
        // Inner scroll with max-h-[600px] should NOT be present on the roster table container
        $this->assertStringNotContainsString('max-h-[600px]', $content);
        $this->assertStringNotContainsString('overflow-y-auto max-h-[600px]', $content);

        // Per page 20
        $response20 = $this->actingAs($this->admin)->get(route('admin.staff.index', ['per_page' => 20]));
        $response20->assertOk();
        $this->assertEquals(20, $response20->viewData('staff')->perPage());

        // Per page 50
        $response50 = $this->actingAs($this->superAdmin)->get(route('admin.staff.index', ['per_page' => 50]));
        $response50->assertOk();
        $this->assertEquals(50, $response50->viewData('staff')->perPage());

        // Invalid per_page falls back to 10
        $responseInvalid = $this->actingAs($this->admin)->get(route('admin.staff.index', ['per_page' => 999]));
        $responseInvalid->assertOk();
        $this->assertEquals(10, $responseInvalid->viewData('staff')->perPage());
    }

    public function test_settings_labels_editable_details_and_administrative_information_are_removed(): void
    {
        // Admin view
        $adminSettings = $this->actingAs($this->admin)->get(route('profile.edit'));
        $adminSettings->assertOk();
        $adminContent = $adminSettings->getContent();

        $this->assertStringNotContainsString('Editable Details', $adminContent);
        $this->assertStringNotContainsString('Administrative Information', $adminContent);

        // Doctor view
        $doctorSettings = $this->actingAs($this->doctor)->get(route('profile.edit'));
        $doctorSettings->assertOk();
        $doctorContent = $doctorSettings->getContent();

        $this->assertStringNotContainsString('Editable Details', $doctorContent);
        $this->assertStringNotContainsString('Administrative Information', $doctorContent);
    }

    public function test_admin_and_super_admin_can_edit_profile_in_settings(): void
    {
        $newEmail = 'updated_admin_' . uniqid() . '@rhu.gov.ph';
        $newName = 'Updated Admin Name';
        $newSchedule = 'Mon - Thu • 9:00 AM - 6:00 PM';

        $response = $this->actingAs($this->admin)->patch(route('profile.update'), [
            'name' => $newName,
            'email' => $newEmail,
            'schedule' => $newSchedule,
        ]);

        $response->assertSessionHas('success');
        $this->admin->refresh();
        $this->assertEquals($newName, $this->admin->name);
        $this->assertEquals($newEmail, $this->admin->email);
        $this->assertEquals($newSchedule, $this->admin->schedule);
    }

    public function test_other_roles_cannot_edit_email_or_schedule_in_settings(): void
    {
        $originalEmail = $this->doctor->email;
        $originalSchedule = $this->doctor->schedule;

        $response = $this->actingAs($this->doctor)->patch(route('profile.update'), [
            'name' => 'Doctor Attempt Update',
            'email' => 'hacked_email_' . uniqid() . '@example.com',
            'schedule' => 'Sun • 12:00 AM - 1:00 AM',
        ]);

        $response->assertSessionHas('success');
        $this->doctor->refresh();

        // Email and schedule should NOT change for non-admin
        $this->assertEquals($originalEmail, $this->doctor->email);
        $this->assertEquals($originalSchedule, $this->doctor->schedule);
    }

    public function test_administrator_privileges_notice_banner_is_removed_for_admin_and_super_admin(): void
    {
        // Admin
        $adminResponse = $this->actingAs($this->admin)->get(route('profile.edit'));
        $adminResponse->assertOk();
        $adminContent = $adminResponse->getContent();
        $this->assertStringNotContainsString('Administrator Privileges:', $adminContent);
        $this->assertStringNotContainsString('have permission to manage and update your official account credentials', $adminContent);

        // Super Admin
        $superAdminResponse = $this->actingAs($this->superAdmin)->get(route('profile.edit'));
        $superAdminResponse->assertOk();
        $superAdminContent = $superAdminResponse->getContent();
        $this->assertStringNotContainsString('Administrator Privileges:', $superAdminContent);
        $this->assertStringNotContainsString('have permission to manage and update your official account credentials', $superAdminContent);

        // Non-admin (doctor) should still have the locked notice banner
        $doctorResponse = $this->actingAs($this->doctor)->get(route('profile.edit'));
        $doctorResponse->assertOk();
        $doctorContent = $doctorResponse->getContent();
        $this->assertStringContainsString('Administrative Fields Locked:', $doctorContent);
    }

    public function test_assigned_schedule_has_duty_days_and_time_picker_without_specific_date(): void
    {
        $adminResponse = $this->actingAs($this->admin)->get(route('profile.edit'));
        $adminResponse->assertOk();
        $content = $adminResponse->getContent();

        // Picker UI elements
        $this->assertStringContainsString('adminSchedulePicker', $content);
        $this->assertStringContainsString('Duty Days & Time Picker', $content);
        $this->assertStringContainsString('Duty Time In', $content);
        $this->assertStringContainsString('Duty Time Out', $content);

        // Specific Date & mode toggle should be removed
        $this->assertStringNotContainsString('Specific Date', $content);
        $this->assertStringNotContainsString('Weekly Days', $content);

        // Submitting schedule and schedule_payload
        $scheduleString = 'Mon, Wed, Fri • 8:00 AM - 5:00 PM';
        $payload = json_encode([
            ['day' => 'Mon', 'time_in' => '08:00', 'time_out' => '17:00'],
            ['day' => 'Wed', 'time_in' => '08:00', 'time_out' => '17:00'],
            ['day' => 'Fri', 'time_in' => '08:00', 'time_out' => '17:00'],
        ]);

        $updateResponse = $this->actingAs($this->admin)->patch(route('profile.update'), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'schedule' => $scheduleString,
            'schedule_payload' => $payload,
        ]);

        $updateResponse->assertSessionHas('success');
        $this->admin->refresh();
        $this->assertEquals($scheduleString, $this->admin->schedule);
        $this->assertEquals(3, $this->admin->practitionerSchedules()->count());
    }

    public function test_staff_names_in_staff_management_are_in_first_middle_last_order(): void
    {
        $doc = User::create([
            'name' => 'Dan Irylle Sotomayor Isuga',
            'email' => 'dan_isuga_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);

        // Formatted name must be in First Middle Last order with prefix
        $this->assertEquals('Dr. Dan Irylle Sotomayor Isuga', $doc->formatted_name);

        // Comma separated legacy names should also normalize to First Middle Last
        $nurse = User::create([
            'name' => 'Isuga, Dan Irylle Sotomayor',
            'email' => 'dan_nurse_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'clinical_nurse',
        ]);
        $this->assertEquals('Nurse Dan Irylle Sotomayor Isuga', $nurse->formatted_name);

        // In Staff Management page, verify rendered order
        $response = $this->actingAs($this->admin)->get(route('admin.staff.index', ['search' => 'Dan Irylle']));
        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Dan Irylle Sotomayor Isuga', $content);
        $this->assertStringNotContainsString('Isuga, Dan Irylle', $content);

        // Color legends and colored prefixes in staff roster must be removed
        $this->assertStringNotContainsString('<!-- Premium Legend -->', $content);
        $this->assertStringNotContainsString('tracking-widest text-slate-600 dark:text-slate-300">Doctor</span>', $content);
        $this->assertStringNotContainsString('tracking-widest text-slate-600 dark:text-slate-300">Pedia</span>', $content);
        $this->assertStringNotContainsString('tracking-widest text-slate-600 dark:text-slate-300">Nurse</span>', $content);
        $this->assertStringNotContainsString('tracking-widest text-slate-600 dark:text-slate-300">Lab</span>', $content);
        $this->assertStringNotContainsString('tracking-widest text-slate-600 dark:text-slate-300">Radio</span>', $content);
        $this->assertStringNotContainsString('tracking-widest text-slate-600 dark:text-slate-300">Admin</span>', $content);
    }

    public function test_clearing_duty_days_reflects_no_schedule_set_and_staff_management_shows_red_text(): void
    {
        // 1. Assign a schedule to admin
        $this->admin->schedule = 'Mon, Wed, Fri • 8:00 AM - 5:00 PM';
        $this->admin->save();
        \App\Models\PractitionerSchedule::create([
            'user_id' => $this->admin->id,
            'day_of_week' => 'Mon',
            'time_in' => '08:00',
            'time_out' => '17:00',
        ]);
        $this->assertEquals(1, $this->admin->practitionerSchedules()->count());

        // 2. Clear duty days: submitting 'No Schedule Set' with empty schedule_payload
        $updateResponse = $this->actingAs($this->admin)->patch(route('profile.update'), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'schedule' => 'No Schedule Set',
            'schedule_payload' => '',
        ]);
        $updateResponse->assertSessionHas('success');
        $this->admin->refresh();
        $this->assertEquals('No Schedule Set', $this->admin->schedule);
        $this->assertEquals(0, $this->admin->practitionerSchedules()->count());
        $this->assertNull($this->admin->formatted_schedule);

        // 3. Verify settings blade contains the updated logic
        $settingsResponse = $this->actingAs($this->admin)->get(route('profile.edit'));
        $settingsContent = $settingsResponse->getContent();
        $this->assertStringContainsString("if (selected.length === 0) return 'No Schedule Set';", $settingsContent);
        $this->assertStringNotContainsString("if (selected.length === 0) return 'Flexible Hours';", $settingsContent);
        $this->assertStringContainsString("scheduleText === 'No Schedule Set' ? 'font-bold text-red-600 dark:text-red-400'", $settingsContent);

        // 4. Verify Staff Management table renders 'No Schedule Set' in red text for staff without a schedule
        $staffIndexResponse = $this->actingAs($this->admin)->get(route('admin.staff.index', ['search' => $this->admin->email]));
        $staffIndexContent = $staffIndexResponse->getContent();
        $this->assertStringContainsString('No Schedule Set', $staffIndexContent);
        $this->assertStringContainsString('text-red-600 dark:text-red-400 font-semibold', $staffIndexContent);
    }
}
