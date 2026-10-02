<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\PreTriage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueueGenerationAndSlipPrintTest extends TestCase
{
    protected User $frontdesk;
    protected User $doctor;
    protected User $nurse;

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

        $this->frontdesk = User::where('role', 'information_desk')->first()
            ?? User::create([
                'name' => 'Front Desk Staff',
                'email' => 'frontdesk_' . uniqid() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'information_desk',
                'status' => 'Present',
            ]);

        $this->doctor = User::where('role', 'regular_doctor')->where('status', 'Present')->first()
            ?? User::create([
                'name' => 'Dr. Test Attending',
                'email' => 'doctor_' . uniqid() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'regular_doctor',
                'status' => 'Present',
            ]);

        $this->nurse = User::where('role', 'clinical_nurse')->where('status', 'Present')->first()
            ?? User::create([
                'name' => 'Nurse Test Attending',
                'email' => 'nurse_' . uniqid() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'clinical_nurse',
                'status' => 'Present',
            ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_severe_triage_assigns_to_doctor_and_redirects_with_print_queue_id(): void
    {
        $patient = Patient::create([
            'first_name' => 'Severe',
            'last_name' => 'Patient',
            'middle_name' => 'T',
            'dob' => '1990-01-01',
            'sex' => 'Male',
            'blood_type' => 'O+',
            'contact_number' => '09123456789',
            'address' => 'Silang, Cavite',
            'barangay' => 'Poblacion',
            'classification' => 'Regular Adult',
            'mothers_maiden_name' => 'Mother Test',
            'philhealth_number' => '12-345678901-2',
        ]);

        $preTriage = PreTriage::create([
            'recorded_by' => $this->nurse->id,
            'patient_id' => $patient->patient_id,
            'patient_name' => $patient->full_name,
            'blood_pressure' => '150/100',
            'temperature' => '39.5',
            'heart_rate' => '115',
            'respiratory_rate' => '24',
            'spo2' => '94',
            'symptoms' => 'Severe chest discomfort and high fever',
            'status' => 'waiting',
        ]);

        $response = $this->actingAs($this->frontdesk)->post(route('frontdesk.visits.store', $patient), [
            'pre_triage_id' => $preTriage->id,
            'consultation_date' => Carbon::today()->toDateString(),
            'symptom_severity' => 'severe',
        ]);

        $response->assertRedirect(route('frontdesk.registration.index'));
        $response->assertSessionHas('print_queue_id');
        $response->assertSessionHas('success');

        $printQueueId = session('print_queue_id');
        $this->assertNotNull($printQueueId);

        $consultation = Consultation::find($printQueueId);
        $this->assertNotNull($consultation);
        $this->assertEquals('severe', $consultation->severity);
        $this->assertNotNull($consultation->doctor_id, 'Severe case must be assigned to doctor');
        $this->assertNull($consultation->nurse_id);
    }

    public function test_mild_triage_assigns_appropriately_and_redirects_with_print_queue_id(): void
    {
        $patient = Patient::create([
            'first_name' => 'Mild',
            'last_name' => 'Patient',
            'middle_name' => 'M',
            'dob' => '1992-05-15',
            'sex' => 'Female',
            'blood_type' => 'A+',
            'contact_number' => '09223456789',
            'address' => 'Silang, Cavite',
            'barangay' => 'Biga',
            'classification' => 'Regular Adult',
            'mothers_maiden_name' => 'Mother Mild',
            'philhealth_number' => '12-345678902-3',
        ]);

        $preTriage = PreTriage::create([
            'recorded_by' => $this->nurse->id,
            'patient_id' => $patient->patient_id,
            'patient_name' => $patient->full_name,
            'blood_pressure' => '120/80',
            'temperature' => '37.8',
            'heart_rate' => '82',
            'respiratory_rate' => '18',
            'spo2' => '98',
            'symptoms' => 'Moderate cough and sore throat',
            'status' => 'waiting',
        ]);

        $response = $this->actingAs($this->frontdesk)->post(route('frontdesk.visits.store', $patient), [
            'pre_triage_id' => $preTriage->id,
            'consultation_date' => Carbon::today()->toDateString(),
            'symptom_severity' => 'mild',
        ]);

        $response->assertRedirect(route('frontdesk.registration.index'));
        $response->assertSessionHas('print_queue_id');

        $printQueueId = session('print_queue_id');
        $consultation = Consultation::find($printQueueId);
        $this->assertNotNull($consultation);
        $this->assertEquals('mild', $consultation->severity);
        // Mild is load balanced between doctor and nurse
        $this->assertTrue($consultation->doctor_id !== null || $consultation->nurse_id !== null);
    }

    public function test_light_normal_triage_assigns_to_nurse_and_redirects_with_print_queue_id(): void
    {
        $patient = Patient::create([
            'first_name' => 'Light',
            'last_name' => 'Patient',
            'middle_name' => 'L',
            'dob' => '1995-10-20',
            'sex' => 'Female',
            'blood_type' => 'B+',
            'contact_number' => '09323456789',
            'address' => 'Silang, Cavite',
            'barangay' => 'Tubuan',
            'classification' => 'Regular Adult',
            'mothers_maiden_name' => 'Mother Light',
            'philhealth_number' => '12-345678903-4',
        ]);

        $preTriage = PreTriage::create([
            'recorded_by' => $this->nurse->id,
            'patient_id' => $patient->patient_id,
            'patient_name' => $patient->full_name,
            'blood_pressure' => '115/75',
            'temperature' => '36.6',
            'heart_rate' => '72',
            'respiratory_rate' => '16',
            'spo2' => '99',
            'symptoms' => 'Routine follow-up / vitals monitoring',
            'status' => 'waiting',
        ]);

        $response = $this->actingAs($this->frontdesk)->post(route('frontdesk.visits.store', $patient), [
            'pre_triage_id' => $preTriage->id,
            'consultation_date' => Carbon::today()->toDateString(),
            'symptom_severity' => 'light',
        ]);

        $response->assertRedirect(route('frontdesk.registration.index'));
        $response->assertSessionHas('print_queue_id');

        $printQueueId = session('print_queue_id');
        $consultation = Consultation::find($printQueueId);
        $this->assertNotNull($consultation);
        $this->assertEquals('light', $consultation->severity);
        $this->assertNotNull($consultation->nurse_id, 'Light triage routes to clinical nurse');
    }

    public function test_queue_slip_renders_properly_with_queue_number_and_provider(): void
    {
        $patient = Patient::create([
            'first_name' => 'Slip',
            'last_name' => 'Tester',
            'dob' => '1985-04-12',
            'sex' => 'Male',
            'blood_type' => 'O+',
            'contact_number' => '09423456789',
            'address' => 'Silang, Cavite',
            'barangay' => 'Poblacion',
            'classification' => 'Regular Adult',
            'mothers_maiden_name' => 'Mother Slip',
            'philhealth_number' => '12-345678904-5',
        ]);

        $consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $this->doctor->id,
            'consultation_date' => Carbon::today()->toDateString(),
            'queue_number' => 'REG-999',
            'status' => 'queued',
            'severity' => 'severe',
            'blood_pressure' => '140/90',
            'temperature' => '38.5',
            'heart_rate' => '95',
            'respiratory_rate' => '20',
            'pulse_rate' => '95',
            'spo2' => '97',
        ]);

        $response = $this->actingAs($this->frontdesk)->get(route('frontdesk.queue-slip', $consultation));
        $response->assertStatus(200);
        $response->assertSee('REG-999');
        $response->assertSee('Dr. ' . $this->doctor->name);
        $response->assertSee('QUEUE_SLIP_RENDERED');
    }

    public function test_registration_index_includes_confirmation_modal_and_print_modal_when_queued(): void
    {
        $patient = Patient::create([
            'first_name' => 'View',
            'last_name' => 'Tester',
            'dob' => '1988-08-08',
            'sex' => 'Male',
            'blood_type' => 'A+',
            'contact_number' => '09523456789',
            'address' => 'Silang, Cavite',
            'barangay' => 'Lucsuhin',
            'classification' => 'Regular Adult',
            'mothers_maiden_name' => 'Mother View',
            'philhealth_number' => '12-345678905-6',
        ]);

        $consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $this->doctor->id,
            'consultation_date' => Carbon::today()->toDateString(),
            'queue_number' => 'REG-101',
            'status' => 'queued',
            'severity' => 'severe',
        ]);

        // When visiting registration index WITH session('print_queue_id')
        $response = $this->actingAs($this->frontdesk)
            ->withSession(['print_queue_id' => $consultation->id])
            ->get(route('frontdesk.registration.index'));

        $response->assertStatus(200);
        // Confirmation modal component is present
        $response->assertSee('Confirm Queue Generation');
        $response->assertSee('window.queueConfirmState');
        // Queue slip print modal is triggered
        $response->assertSee('Queue Slip Generated');
        $response->assertSee('Print Queue Slip (P)');
        $response->assertSee('queueSlipIframe');
        $response->assertSee(route('frontdesk.queue-slip', $consultation->id));
    }
}
