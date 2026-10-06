<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\PreTriage;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NurseTriageQueueRedesignTest extends TestCase
{
    protected User $nurse;
    protected User $doctor;
    protected Patient $patient;

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

        $this->nurse = User::create([
            'name' => 'Nurse Joy Test',
            'first_name' => 'Joy',
            'last_name' => 'Test',
            'email' => 'nurse_joy_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'clinical_nurse',
            'status' => 'Present',
        ]);

        $this->doctor = User::create([
            'name' => 'Doctor Oak Test',
            'first_name' => 'Samuel',
            'last_name' => 'Oak',
            'email' => 'doctor_oak_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
            'status' => 'Present',
        ]);

        $this->patient = Patient::create([
            'patient_id' => 'P-TEST-' . rand(10000, 99999),
            'first_name' => 'Maria',
            'last_name' => 'Clara',
            'dob' => '1995-05-15',
            'sex' => 'Female',
            'address' => 'Silang, Cavite',
            'contact_number' => '09171234567',
            'classification' => 'Senior Citizen',
        ]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
        parent::tearDown();
    }

    public function test_nurse_triage_queue_dashboard_renders_with_redesigned_ui(): void
    {
        $response = $this->actingAs($this->nurse)->get(route('nurse.dashboard'));

        $response->assertOk();
        $response->assertSee('Nurse Triage Queue');
        $response->assertSee('Triage Session Active');
        $response->assertSee('Waiting Patients');
        $response->assertSee('Priority Patients');
        $response->assertSee('Completed Today');
        $response->assertSee('Patient Triage Queue');
        $response->assertSee('patient-queue-section');
        $response->assertSee('data-dynamic-block="true"', false);
        $response->assertSee('Forward Patient to Doctor');
        $response->assertSee('Cancel Consultation / Walkout');
    }

    public function test_nurse_triage_queue_displays_active_and_priority_patient_cards(): void
    {
        $preTriage = PreTriage::create([
            'patient_id' => $this->patient->patient_id,
            'patient_name' => $this->patient->full_name,
            'blood_pressure' => '130/85',
            'temperature' => '38.2',
            'heart_rate' => '88',
            'oxygen_saturation' => '98',
            'symptoms' => 'High fever and chills',
            'recorded_by' => $this->nurse->id,
            'status' => 'claimed',
        ]);

        $queue = Queue::create([
            'patient_id' => $this->patient->patient_id,
            'queue_number' => 'PED-E-01',
            'priority_type' => 'Priority',
            'service_type' => 'Consultation',
            'status' => 'Waiting',
        ]);

        $consultation = Consultation::create([
            'patient_id' => $this->patient->patient_id,
            'pre_triage_id' => $preTriage->id,
            'nurse_id' => $this->nurse->id,
            'queue_number' => 'PED-E-01',
            'consultation_date' => Carbon::today(),
            'status' => 'queued',
        ]);

        $response = $this->actingAs($this->nurse)->get(route('nurse.dashboard'));

        $response->assertOk();
        $response->assertSee('PED-E-01');
        $response->assertSee('Maria Clara');
        $response->assertSee('130/85');
        $response->assertSee('38.2°C');
        $response->assertSee('High fever and chills');
        $response->assertSee('Admit Patient');
        $response->assertSee('Forward');
    }

    public function test_nurse_can_forward_patient_to_doctor(): void
    {
        $consultation = Consultation::create([
            'patient_id' => $this->patient->patient_id,
            'nurse_id' => $this->nurse->id,
            'queue_number' => 'REG-001',
            'consultation_date' => Carbon::today(),
            'status' => 'queued',
        ]);

        $response = $this->actingAs($this->nurse)->post(route('nurse.forward', $consultation->id), [
            'doctor_id' => $this->doctor->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $consultation->refresh();
        $this->assertEquals($this->doctor->id, $consultation->doctor_id);
        $this->assertNull($consultation->nurse_id);
        $this->assertEquals('queued', $consultation->status);
    }

    public function test_nurse_can_cancel_walkout_consultation(): void
    {
        $consultation = Consultation::create([
            'patient_id' => $this->patient->patient_id,
            'nurse_id' => $this->nurse->id,
            'queue_number' => 'REG-002',
            'consultation_date' => Carbon::today(),
            'status' => 'active',
            'consultation_start_time' => now(),
        ]);

        $response = $this->actingAs($this->nurse)->post(route('nurse.consultation.cancel', $consultation->id), [
            'cancellation_reason' => 'Patient Walked Out / Left Premises',
            'notes' => 'Patient chose not to wait.',
        ]);

        $response->assertRedirect(route('nurse.dashboard'));
        $response->assertSessionHas('success');

        $consultation->refresh();
        $this->assertEquals('cancelled', $consultation->status);
        $this->assertNotNull($consultation->consultation_end_time);
        $this->assertStringContainsString('Patient Walked Out / Left Premises', $consultation->medical_notes);
    }

    public function test_vitals_nurse_triage_dashboard_renders_custom_styled_dob_picker(): void
    {
        $vitalsNurse = User::create([
            'name' => 'Vitals Nurse Test',
            'first_name' => 'Vitals',
            'last_name' => 'Nurse',
            'email' => 'vitals_nurse_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'vitals_nurse',
            'status' => 'Present',
        ]);

        $response = $this->actingAs($vitalsNurse)->get(route('triage.dashboard'));

        $response->assertOk();
        $response->assertSee('showMonthPicker');
        $response->assertSee('showYearPicker');
        $response->assertSee('triageYearSearchInput');
        $response->assertSee('triageYearList');
        $response->assertSee('autoUpdateClassification');
        $response->assertDontSee('<select @change="setMonth', false);
        $response->assertDontSee('<select @change="setYear', false);
    }
}

