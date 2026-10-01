<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FlowFFollowUpManagementTest extends TestCase
{
    protected User $frontDeskUser;
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

        $this->frontDeskUser = User::where('role', 'information_desk')->first()
            ?? User::create([
                'name' => 'Front Desk Tester',
                'email' => 'frontdesk_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'information_desk',
            ]);

        $this->doctor = User::where('role', 'regular_doctor')->first()
            ?? User::create([
                'name' => 'Dr. Followup Specialist',
                'email' => 'dr_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'regular_doctor',
            ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_frontdesk_staff_can_view_followup_tracker_list()
    {
        $patient = Patient::create([
            'patient_id' => 'PT-FO-' . rand(1000, 9999),
            'first_name' => 'Clara',
            'last_name' => 'Oswald',
            'dob' => '1995-04-12',
            'address' => 'Brgy. Bulihan, Silang, Cavite',
            'contact_number' => '09171234567',
        ]);

        $consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $this->doctor->id,
            'followup_doctor_id' => $this->doctor->id,
            'diagnosis' => 'Essential Hypertension Stage 1',
            'is_followup_needed' => true,
            'followup_date' => now()->subDays(2)->toDateString(),
            'followup_reason' => 'Recheck blood pressure after 2 weeks',
            'status' => 'completed',
            'queue_number' => 'FU-' . rand(1000, 9999),
        ]);

        $response = $this->actingAs($this->frontDeskUser)->get(route('frontdesk.followups.index'));

        $response->assertStatus(200);
        $response->assertSee('Patient Follow-Up Tracker');
        $response->assertSee('Clara Oswald');
        $response->assertSee('Essential Hypertension Stage 1');
        $response->assertSee('Recheck Blood Pressure After 2 Weeks');
        $response->assertSee('Overdue');
    }

    public function test_frontdesk_staff_can_filter_by_overdue_today_upcoming_and_fulfilled()
    {
        $p1 = Patient::create([
            'patient_id' => 'PT-OD-' . rand(1000, 9999),
            'first_name' => 'Overdue',
            'last_name' => 'Patient',
            'dob' => '1990-01-01',
            'address' => 'Brgy. Bulihan, Silang, Cavite',
        ]);
        $c1 = Consultation::create([
            'patient_id' => $p1->patient_id,
            'doctor_id' => $this->doctor->id,
            'is_followup_needed' => true,
            'followup_date' => now()->subDays(5)->toDateString(),
            'followup_reason' => 'Check surgical wound',
            'status' => 'completed',
            'queue_number' => 'FU-' . rand(1000, 9999),
        ]);

        $p2 = Patient::create([
            'patient_id' => 'PT-TD-' . rand(1000, 9999),
            'first_name' => 'Due Today',
            'last_name' => 'Citizen',
            'dob' => '1985-02-02',
            'address' => 'Brgy. Bulihan, Silang, Cavite',
        ]);
        $c2 = Consultation::create([
            'patient_id' => $p2->patient_id,
            'doctor_id' => $this->doctor->id,
            'is_followup_needed' => true,
            'followup_date' => now()->toDateString(),
            'followup_reason' => 'Suture removal today',
            'status' => 'completed',
            'queue_number' => 'FU-' . rand(1000, 9999),
        ]);

        $p3 = Patient::create([
            'patient_id' => 'PT-FF-' . rand(1000, 9999),
            'first_name' => 'Fulfilled',
            'last_name' => 'Resolved',
            'dob' => '1992-03-03',
            'address' => 'Brgy. Bulihan, Silang, Cavite',
        ]);
        $c3 = Consultation::create([
            'patient_id' => $p3->patient_id,
            'doctor_id' => $this->doctor->id,
            'is_followup_needed' => true,
            'followup_date' => now()->subDays(10)->toDateString(),
            'followup_completed_at' => now()->subDays(2),
            'followup_reason' => 'Post-treatment evaluation',
            'status' => 'completed',
            'queue_number' => 'FU-' . rand(1000, 9999),
        ]);

        // Filter Overdue
        $resOverdue = $this->actingAs($this->frontDeskUser)->get(route('frontdesk.followups.index', ['tab' => 'overdue']));
        $resOverdue->assertStatus(200);
        $resOverdue->assertSee('Overdue Patient');
        $resOverdue->assertDontSee('Fulfilled Resolved');

        // Filter Due Today
        $resToday = $this->actingAs($this->frontDeskUser)->get(route('frontdesk.followups.index', ['tab' => 'due_today']));
        $resToday->assertStatus(200);
        $resToday->assertSee('Due Today Citizen');
        $resToday->assertDontSee('Overdue Patient');

        // Filter Fulfilled
        $resFulfilled = $this->actingAs($this->frontDeskUser)->get(route('frontdesk.followups.index', ['tab' => 'fulfilled']));
        $resFulfilled->assertStatus(200);
        $resFulfilled->assertSee('Fulfilled Resolved');
        $resFulfilled->assertDontSee('DueToday Citizen');
    }

    public function test_frontdesk_staff_can_reschedule_followup()
    {
        $patient = Patient::create([
            'patient_id' => 'PT-RS-' . rand(1000, 9999),
            'first_name' => 'Arthur',
            'last_name' => 'Dent',
            'dob' => '1980-05-15',
            'address' => 'Brgy. Bulihan, Silang, Cavite',
            'next_followup_date' => now()->subDay()->toDateString(),
        ]);

        $consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $this->doctor->id,
            'is_followup_needed' => true,
            'followup_date' => now()->subDay()->toDateString(),
            'followup_reason' => 'Repeat fasting blood sugar',
            'status' => 'completed',
            'queue_number' => 'FU-' . rand(1000, 9999),
        ]);

        $newDate = now()->addDays(14)->toDateString();

        $response = $this->actingAs($this->frontDeskUser)->post(route('frontdesk.followups.reschedule', $consultation->id), [
            'followup_date' => $newDate,
            'followup_reason' => 'Patient requested return after work travel',
            'followup_doctor_id' => $this->doctor->id,
        ]);

        $response->assertSessionHas('success');

        $consultation->refresh();
        $this->assertEquals($newDate, $consultation->followup_date->format('Y-m-d'));
        $this->assertEquals('Patient Requested Return After Work Travel', $consultation->followup_reason);

        // Synchronized with Patient model
        $patient->refresh();
        $this->assertEquals($newDate, $patient->next_followup_date->format('Y-m-d'));

        // Audit log created
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'Rescheduled Patient Follow-Up',
            'user_id' => $this->frontDeskUser->id,
        ]);
    }

    public function test_frontdesk_staff_can_manually_fulfill_followup()
    {
        $patient = Patient::create([
            'patient_id' => 'PT-MF-' . rand(1000, 9999),
            'first_name' => 'Rose',
            'last_name' => 'Tyler',
            'dob' => '1987-07-20',
            'address' => 'Brgy. Bulihan, Silang, Cavite',
            'next_followup_date' => now()->toDateString(),
        ]);

        $consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $this->doctor->id,
            'is_followup_needed' => true,
            'followup_date' => now()->toDateString(),
            'followup_reason' => 'Check response to antibiotics',
            'status' => 'completed',
            'queue_number' => 'FU-' . rand(1000, 9999),
        ]);

        $response = $this->actingAs($this->frontDeskUser)->post(route('frontdesk.followups.fulfill', $consultation->id), [
            'resolution_notes' => 'Patient called, symptoms resolved, no discomfort',
        ]);

        $response->assertSessionHas('success');

        $consultation->refresh();
        $this->assertNotNull($consultation->followup_completed_at);
        $this->assertStringContainsString('Resolved: Patient Called, Symptoms Resolved', $consultation->followup_reason);

        // Patient next_followup_date cleared
        $patient->refresh();
        $this->assertNull($patient->next_followup_date);

        // Audit log created
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'Marked Follow-Up Fulfilled (Manual)',
            'user_id' => $this->frontDeskUser->id,
        ]);
    }

    public function test_unauthorized_roles_cannot_access_followup_tracker()
    {
        $patientUser = User::where('role', 'patient')->first()
            ?? User::create([
                'name' => 'Patient Test',
                'email' => 'pt_user_' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'role' => 'patient',
            ]);

        $response = $this->actingAs($patientUser)->get(route('frontdesk.followups.index'));
        // RoleMiddleware redirects unauthorized users to their home route (302), not 403
        $response->assertRedirect();
    }
}
