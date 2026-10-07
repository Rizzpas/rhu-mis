<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\FacilityUnit;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminSuperAdminRoleDifferentiationTest extends TestCase
{
    protected User $superAdmin;
    protected User $regularAdmin;

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

        $this->superAdmin = User::where('role', 'super_admin')->first()
            ?? User::create([
                'name' => 'Super Admin Test',
                'email' => 'super_admin_' . uniqid() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'super_admin',
                'status' => 'Online',
            ]);

        $this->regularAdmin = User::where('role', 'admin')->first()
            ?? User::create([
                'name' => 'Admin Test',
                'email' => 'admin_' . uniqid() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'status' => 'Online',
            ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    /** Security Audit Trails */
    public function test_super_admin_can_access_audit_logs(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.audit.index'));
        $response->assertStatus(200);
    }

    public function test_regular_admin_is_forbidden_from_audit_logs_and_export(): void
    {
        $response = $this->actingAs($this->regularAdmin)->get(route('admin.audit.index'));
        $response->assertStatus(403);

        $export = $this->actingAs($this->regularAdmin)->get(route('admin.audit.export-csv'));
        $export->assertStatus(403);
    }

    /** Staff Management */
    public function test_both_admin_and_super_admin_can_view_staff_index(): void
    {
        $this->actingAs($this->superAdmin)->get(route('admin.staff.index'))->assertStatus(200);
        $this->actingAs($this->regularAdmin)->get(route('admin.staff.index'))->assertStatus(200);
    }

    public function test_regular_admin_can_create_regular_staff_but_cannot_create_admin(): void
    {
        // Creating doctor works
        $doctorEmail = 'doctor_diff_' . uniqid() . '@example.com';
        $doctorData = [
            'name' => 'Dr. Jane Smith',
            'email' => $doctorEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'regular_doctor',
            'status' => 'Online',
        ];
        $this->actingAs($this->regularAdmin)->post(route('admin.staff.store'), $doctorData)
            ->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => $doctorEmail]);

        // Creating admin fails with 403
        $adminData = [
            'name' => 'Admin Bob',
            'email' => 'admin_bob_' . uniqid() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
            'status' => 'Online',
        ];
        $this->actingAs($this->regularAdmin)->post(route('admin.staff.store'), $adminData)
            ->assertStatus(403);
    }

    public function test_only_super_admin_can_promote_staff_to_admin(): void
    {
        $nurse = User::create([
            'name' => 'Test Nurse',
            'email' => 'nurse_promo_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'clinical_nurse',
            'status' => 'Online',
        ]);

        // Regular admin fails
        $this->actingAs($this->regularAdmin)->post(route('admin.staff.promote', $nurse->id))
            ->assertStatus(403);
        $this->assertEquals('clinical_nurse', $nurse->fresh()->role);

        // Super admin succeeds
        $this->actingAs($this->superAdmin)->post(route('admin.staff.promote', $nurse->id))
            ->assertRedirect();
        $this->assertEquals('admin', $nurse->fresh()->role);
    }

    public function test_only_super_admin_can_delete_staff_account(): void
    {
        $targetStaff = User::create([
            'name' => 'Staff To Delete',
            'email' => 'pharm_del_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'pharmacy',
            'status' => 'Online',
        ]);

        // Regular admin forbidden
        $this->actingAs($this->regularAdmin)->delete(route('admin.staff.destroy', $targetStaff->id))
            ->assertStatus(403);
        $this->assertNull($targetStaff->fresh()->deleted_at);

        // Super admin succeeds (soft deletes / archives)
        $this->actingAs($this->superAdmin)->delete(route('admin.staff.destroy', $targetStaff->id))
            ->assertRedirect();
        $this->assertNotNull($targetStaff->fresh()->deleted_at);
    }

    public function test_only_super_admin_can_bulk_delete_staff(): void
    {
        $staff1 = User::create([
            'name' => 'Lab Staff 1',
            'email' => 'lab1_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'laboratory',
            'status' => 'Online',
        ]);
        $staff2 = User::create([
            'name' => 'Rad Staff 2',
            'email' => 'rad2_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'radiology',
            'status' => 'Online',
        ]);

        // Regular admin forbidden
        $this->actingAs($this->regularAdmin)->delete(route('admin.staff.bulk-delete'), [
            'ids' => [$staff1->id, $staff2->id],
        ])->assertStatus(403);

        // Super admin succeeds
        $this->actingAs($this->superAdmin)->delete(route('admin.staff.bulk-delete'), [
            'ids' => [$staff1->id, $staff2->id],
        ])->assertRedirect();

        $this->assertNotNull($staff1->fresh()->deleted_at);
        $this->assertNotNull($staff2->fresh()->deleted_at);
    }

    /** Announcements */
    public function test_both_admin_and_super_admin_can_create_and_manage_announcement_content(): void
    {
        $uniqueTitle = 'Community Health Advisory ' . uniqid();
        $response = $this->actingAs($this->regularAdmin)->post(route('admin.announcements.store'), [
            'title' => $uniqueTitle,
            'content' => 'Free immunization available tomorrow.',
            'category' => 'General Notice',
            'priority' => 'normal',
            'status' => 'published',
        ]);
        $response->assertRedirect();

        $announcement = Announcement::where('title', $uniqueTitle)->first();
        $this->assertNotNull($announcement);

        // Regular admin can toggle status
        $this->actingAs($this->regularAdmin)->post(route('admin.announcements.toggle', $announcement->id), [
            'status' => 'draft',
        ])->assertRedirect();
        $this->assertEquals('draft', $announcement->fresh()->status);
    }

    public function test_only_super_admin_can_delete_announcements(): void
    {
        $announcement = Announcement::create([
            'title' => 'Important Test Announcement ' . uniqid(),
            'content' => 'Content here',
            'author_id' => $this->superAdmin->id,
            'category' => 'General Notice',
            'priority' => 'normal',
            'status' => 'published',
        ]);

        // Regular admin forbidden
        $this->actingAs($this->regularAdmin)->delete(route('admin.announcements.destroy', $announcement->id))
            ->assertStatus(403);
        $this->assertNull($announcement->fresh()->deleted_at);

        // Super admin succeeds
        $this->actingAs($this->superAdmin)->delete(route('admin.announcements.destroy', $announcement->id))
            ->assertRedirect();
        $this->assertNotNull($announcement->fresh()->deleted_at);
    }

    public function test_only_super_admin_can_bulk_delete_announcements(): void
    {
        $a1 = Announcement::create([
            'title' => 'Announcement 1 ' . uniqid(),
            'content' => 'Body 1',
            'author_id' => $this->superAdmin->id,
            'category' => 'General Notice',
            'priority' => 'normal',
            'status' => 'published',
        ]);
        $a2 = Announcement::create([
            'title' => 'Announcement 2 ' . uniqid(),
            'content' => 'Body 2',
            'author_id' => $this->superAdmin->id,
            'category' => 'General Notice',
            'priority' => 'normal',
            'status' => 'published',
        ]);

        // Regular admin forbidden
        $this->actingAs($this->regularAdmin)->delete(route('admin.announcements.bulk-delete'), [
            'ids' => [$a1->id, $a2->id],
        ])->assertStatus(403);

        // Super admin succeeds
        $this->actingAs($this->superAdmin)->delete(route('admin.announcements.bulk-delete'), [
            'ids' => [$a1->id, $a2->id],
        ])->assertRedirect();

        $this->assertNotNull($a1->fresh()->deleted_at);
        $this->assertNotNull($a2->fresh()->deleted_at);
    }

    /** Retention Purging */
    public function test_regular_admin_can_extend_retention_but_cannot_delete_or_purge(): void
    {
        $patient = Patient::create([
            'patient_id' => 'RHU-TEST-' . strtoupper(substr(uniqid(), 0, 6)),
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'dob' => '1990-01-01',
            'sex' => 'Female',
            'civil_status' => 'Single',
            'address' => 'Sample Address',
            'barangay' => 'San Nicolas',
            'contact_number' => '09123456789',
            'expires_at' => now()->subDay(),
        ]);

        // Regular admin can extend
        $this->actingAs($this->regularAdmin)->post(route('admin.retention.extend', $patient->patient_id))
            ->assertRedirect();

        // Regular admin cannot delete retention record
        $this->actingAs($this->regularAdmin)->delete(route('admin.retention.delete', $patient->patient_id))
            ->assertStatus(403);

        // Super admin can delete retention record
        $this->actingAs($this->superAdmin)->delete(route('admin.retention.delete', $patient->patient_id))
            ->assertRedirect();
        $this->assertNotNull($patient->fresh()->deleted_at);
    }

    /** Facilities CMS */
    public function test_only_super_admin_can_manage_facility_units(): void
    {
        $uniqueName = 'Barangay Health Station ' . uniqid();
        $facilityData = [
            'name' => $uniqueName,
            'category' => 'General Medicine',
            'description' => 'Local satellite clinic',
            'location' => 'Zone 1',
            'is_active' => 1,
        ];

        // Regular admin forbidden from creating
        $this->actingAs($this->regularAdmin)->post(route('admin.facilities.store'), $facilityData)
            ->assertStatus(403);

        // Super admin can create
        $this->actingAs($this->superAdmin)->post(route('admin.facilities.store'), $facilityData)
            ->assertRedirect();
        $facility = FacilityUnit::where('name', $uniqueName)->first();
        $this->assertNotNull($facility);

        // Regular admin forbidden from updating
        $this->actingAs($this->regularAdmin)->put(route('admin.facilities.update', $facility->slug), [
            'name' => $uniqueName . ' Updated',
            'category' => 'General Medicine',
        ])->assertStatus(403);

        // Super admin can update
        $this->actingAs($this->superAdmin)->put(route('admin.facilities.update', $facility->slug), [
            'name' => $uniqueName . ' Updated',
            'category' => 'General Medicine',
        ])->assertRedirect();

        // Regular admin forbidden from deleting
        $this->actingAs($this->regularAdmin)->delete(route('admin.facilities.destroy', $facility->fresh()->slug))
            ->assertStatus(403);

        // Super admin can delete
        $this->actingAs($this->superAdmin)->delete(route('admin.facilities.destroy', $facility->fresh()->slug))
            ->assertRedirect();
        $this->assertDatabaseMissing('facility_units', ['id' => $facility->id]);
    }

    /** Staff Status Privilege Escalation Tests */
    public function test_regular_admin_cannot_change_status_of_super_admin_or_other_admin(): void
    {
        $this->superAdmin->update(['status' => 'Online']);

        // Regular admin trying to update Super Admin status -> 403
        $response = $this->actingAs($this->regularAdmin)->post(route('admin.staff.status', $this->superAdmin->id), [
            'status' => 'Offline',
        ]);
        $response->assertStatus(403);
        $this->assertEquals('Online', $this->superAdmin->fresh()->status);

        // Regular admin trying to update another Admin status -> 403
        $otherAdmin = User::create([
            'name' => 'Other Admin',
            'email' => 'other_admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'Online',
        ]);

        $response2 = $this->actingAs($this->regularAdmin)->post(route('admin.staff.status', $otherAdmin->id), [
            'status' => 'Offline',
        ]);
        $response2->assertStatus(403);
        $this->assertEquals('Online', $otherAdmin->fresh()->status);
    }

    public function test_regular_admin_can_change_status_of_clinical_staff(): void
    {
        $nurse = User::create([
            'name' => 'Nurse For Status Update',
            'email' => 'nurse_status_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'clinical_nurse',
            'status' => 'Online',
        ]);

        $response = $this->actingAs($this->regularAdmin)->post(route('admin.staff.status', $nurse->id), [
            'status' => 'Offline',
        ]);
        $response->assertRedirect();
        $this->assertEquals('Offline', $nurse->fresh()->status);
    }

    public function test_regular_admin_cannot_bulk_update_status_if_admin_accounts_included(): void
    {
        $this->superAdmin->update(['status' => 'Online']);

        $nurse = User::create([
            'name' => 'Nurse In Bulk',
            'email' => 'nurse_bulk_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'clinical_nurse',
            'status' => 'Online',
        ]);

        // Request includes a super_admin ID
        $response = $this->actingAs($this->regularAdmin)->post(route('admin.staff.bulk-status'), [
            'ids' => [$nurse->id, $this->superAdmin->id],
            'status' => 'Offline',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('Online', $nurse->fresh()->status);
        $this->assertEquals('Online', $this->superAdmin->fresh()->status);
    }

    public function test_super_admin_can_bulk_update_staff_status(): void
    {
        $nurse = User::create([
            'name' => 'Nurse Bulk Super',
            'email' => 'nurse_bs_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'clinical_nurse',
            'status' => 'Online',
        ]);
        $doctor = User::create([
            'name' => 'Doctor Bulk Super',
            'email' => 'doc_bs_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'regular_doctor',
            'status' => 'Online',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.staff.bulk-status'), [
            'ids' => [$nurse->id, $doctor->id],
            'status' => 'Occupied',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Occupied', $nurse->fresh()->status);
        $this->assertEquals('Occupied', $doctor->fresh()->status);
    }
}
