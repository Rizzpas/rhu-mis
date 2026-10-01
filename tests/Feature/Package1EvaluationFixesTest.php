<?php

namespace Tests\Feature;

use App\Models\AncillaryRequest;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\MedicalCase;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Patient;
use App\Models\PreTriage;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Package1EvaluationFixesTest extends TestCase
{
    protected User $admin;
    protected User $doctor;
    protected User $nurse;
    protected User $pharmacist;

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
                'name' => 'Admin Auditor',
                'email' => 'admin_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);

        $this->doctor = User::where('role', 'regular_doctor')->first()
            ?? User::create([
                'name' => 'Dr. Walkout Handler',
                'email' => 'dr_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'regular_doctor',
            ]);

        $this->nurse = User::where('role', 'clinical_nurse')->first()
            ?? User::create([
                'name' => 'Nurse Triage Specialist',
                'email' => 'nurse_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'clinical_nurse',
            ]);

        $this->pharmacist = User::where('role', 'pharmacy')->first()
            ?? User::create([
                'name' => 'Chief Pharmacist',
                'email' => 'pharm_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'pharmacy',
            ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_doctor_can_cancel_consultation_as_patient_walkout()
    {
        $patient = Patient::create([
            'patient_id' => 'PT-WO-' . rand(1000, 9999),
            'first_name' => 'Arthur',
            'last_name' => 'Dent',
            'dob' => '1985-05-20',
            'address' => 'Silang, Cavite',
            'contact_number' => '09170001111',
            'classification' => 'Adult',
            'sex' => 'Male',
        ]);

        $appointment = Appointment::create([
            'reference_number' => 'APT-WO-' . rand(1000, 9999),
            'first_name' => 'Arthur',
            'last_name' => 'Dent',
            'email' => 'arthur' . uniqid() . '@example.com',
            'preferred_date' => today()->toDateString(),
            'preferred_time' => '09:00',
            'type' => 'adult',
            'status' => 'triaged',
            'data_privacy_agreed' => 1,
        ]);

        $preTriage = PreTriage::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->patient_id,
            'patient_name' => $patient->full_name,
            'first_name' => $patient->first_name,
            'last_name' => $patient->last_name,
            'recorded_by' => $this->nurse->id,
            'blood_pressure' => '120/80',
            'temperature' => '36.8',
            'status' => 'claimed',
        ]);

        $queueNum = 'REG-WO-01';
        $queue = Queue::create([
            'patient_id' => $patient->patient_id,
            'queue_number' => $queueNum,
            'priority_type' => 'Regular',
            'service_type' => 'Consultation',
            'status' => 'Waiting',
        ]);

        $consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'pre_triage_id' => $preTriage->id,
            'doctor_id' => $this->doctor->id,
            'queue_number' => $queueNum,
            'consultation_date' => today()->toDateString(),
            'status' => 'active',
            'consultation_start_time' => now(),
        ]);

        $ancillary = AncillaryRequest::create([
            'consultation_id' => $consultation->id,
            'patient_id' => $patient->patient_id,
            'requested_by' => $this->doctor->id,
            'type' => 'Laboratory',
            'department' => 'Laboratory',
            'test_name' => 'Complete Blood Count (CBC)',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->doctor)
            ->post(route('doctor.consultation.cancel', $consultation->id), [
                'cancellation_reason' => 'Patient Walked Out / Left Premises',
                'notes' => 'Patient felt better and left without seeing doctor.',
            ]);

        $response->assertRedirect(route('doctor.dashboard'));
        $response->assertSessionHas('success');

        $consultation->refresh();
        $this->assertEquals('cancelled', $consultation->status);
        $this->assertNotNull($consultation->consultation_end_time);
        $this->assertStringContainsString('Patient Walked Out', $consultation->medical_notes);

        $preTriage->refresh();
        $this->assertEquals('cancelled', $preTriage->status);

        $appointment->refresh();
        $this->assertEquals('cancelled', $appointment->status);

        $queue->refresh();
        $this->assertEquals('Cancelled', $queue->status);

        $ancillary->refresh();
        $this->assertEquals('Cancelled', $ancillary->status);

        $this->assertTrue(
            AuditLog::where('action', 'like', '%Consultation Cancelled / Patient Walked Out%')
                ->where('model_id', $consultation->id)
                ->exists()
        );
    }

    public function test_nurse_can_cancel_consultation_as_patient_refusal()
    {
        $patient = Patient::create([
            'patient_id' => 'PT-REF-' . rand(1000, 9999),
            'first_name' => 'Rose',
            'last_name' => 'Tyler',
            'dob' => '1990-07-15',
            'address' => 'Silang, Cavite',
            'contact_number' => '09170002222',
            'classification' => 'Adult',
            'sex' => 'Female',
        ]);

        $queueNum = 'NUR-REF-01';
        $queue = Queue::create([
            'patient_id' => $patient->patient_id,
            'queue_number' => $queueNum,
            'priority_type' => 'Regular',
            'service_type' => 'Consultation',
            'status' => 'Waiting',
        ]);

        $consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'nurse_id' => $this->nurse->id,
            'queue_number' => $queueNum,
            'consultation_date' => today()->toDateString(),
            'status' => 'active',
            'consultation_start_time' => now(),
        ]);

        $response = $this->actingAs($this->nurse)
            ->post(route('nurse.consultation.cancel', $consultation->id), [
                'cancellation_reason' => 'Patient Refused Consultation / Treatment',
                'notes' => 'Decided to seek private hospital treatment.',
            ]);

        $response->assertRedirect(route('nurse.dashboard'));
        $response->assertSessionHas('success');

        $consultation->refresh();
        $this->assertEquals('cancelled', $consultation->status);
        $this->assertStringContainsString('Patient Refused', $consultation->medical_notes);

        $queue->refresh();
        $this->assertEquals('Cancelled', $queue->status);
    }

    public function test_admin_patient_view_masks_clinical_data_by_default()
    {
        $patient = Patient::create([
            'patient_id' => 'PT-DPA-' . rand(1000, 9999),
            'first_name' => 'Donna',
            'last_name' => 'Noble',
            'dob' => '1982-11-20',
            'address' => 'Brgy. Lucsuhin, Silang',
            'classification' => 'Adult',
            'sex' => 'Female',
        ]);

        $preTriage = PreTriage::create([
            'patient_id' => $patient->patient_id,
            'patient_name' => $patient->full_name,
            'first_name' => $patient->first_name,
            'last_name' => $patient->last_name,
            'recorded_by' => $this->nurse->id,
            'blood_pressure' => '130/85',
            'temperature' => '38.2',
            'status' => 'completed',
        ]);

        $consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'pre_triage_id' => $preTriage->id,
            'doctor_id' => $this->doctor->id,
            'queue_number' => 'REG-DPA-01',
            'consultation_date' => today()->toDateString(),
            'blood_pressure' => '130/85',
            'temperature' => '38.2',
            'status' => 'completed',
        ]);

        $case = MedicalCase::create([
            'case_number' => 'CASE-DPA-' . rand(1000, 9999),
            'patient_id' => $patient->patient_id,
            'consultation_id' => $consultation->id,
            'pre_triage_id' => $preTriage->id,
            'diagnosis' => 'CONFIDENTIAL_ACUTE_BRONCHITIS_DIAGNOSIS',
            'prescription' => 'CONFIDENTIAL_AMOXICILLIN_500MG_TID',
            'vitals_snapshot' => ['bp' => '130/85', 'temp' => '38.2'],
            'closed_at' => now(),
        ]);

        // Default Admin View: Masked
        $response = $this->actingAs($this->admin)
            ->get(route('admin.patients.show', $patient));

        $response->assertOk();
        $response->assertSee('Data Privacy Act (RA 10173) Protection Active');
        $response->assertSee('Authorized PHI Override');
        // Confidential clinical data must NOT be visible
        $response->assertDontSee('CONFIDENTIAL_ACUTE_BRONCHITIS_DIAGNOSIS');
        $response->assertDontSee('CONFIDENTIAL_AMOXICILLIN_500MG_TID');
        $response->assertSee('[RESTRICTED — CLINICAL DIAGNOSIS MASKED UNDER RA 10173]');
    }

    public function test_admin_patient_view_unmasks_with_authorized_override_and_audit_log()
    {
        $patient = Patient::create([
            'patient_id' => 'PT-UNM-' . rand(1000, 9999),
            'first_name' => 'Rory',
            'last_name' => 'Williams',
            'dob' => '1989-02-14',
            'address' => 'Silang, Cavite',
            'classification' => 'Adult',
            'sex' => 'Male',
        ]);

        $preTriage = PreTriage::create([
            'patient_id' => $patient->patient_id,
            'patient_name' => $patient->full_name,
            'first_name' => $patient->first_name,
            'last_name' => $patient->last_name,
            'recorded_by' => $this->nurse->id,
            'blood_pressure' => '120/80',
            'temperature' => '36.5',
            'status' => 'completed',
        ]);

        $consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'pre_triage_id' => $preTriage->id,
            'doctor_id' => $this->doctor->id,
            'queue_number' => 'REG-UNM-01',
            'consultation_date' => today()->toDateString(),
            'blood_pressure' => '120/80',
            'temperature' => '36.5',
            'status' => 'completed',
        ]);

        $case = MedicalCase::create([
            'case_number' => 'CASE-UNM-' . rand(1000, 9999),
            'patient_id' => $patient->patient_id,
            'consultation_id' => $consultation->id,
            'pre_triage_id' => $preTriage->id,
            'diagnosis' => 'UNMASKED_ALLERGIC_RHINITIS_DIAGNOSIS',
            'prescription' => 'UNMASKED_CETIRIZINE_10MG',
            'vitals_snapshot' => ['bp' => '120/80', 'temp' => '36.5'],
            'closed_at' => now(),
        ]);

        // Unmasked Admin View with authorized reason
        $response = $this->actingAs($this->admin)
            ->get(route('admin.patients.show', [
                'patient' => $patient,
                'unmask' => 1,
                'override_reason' => 'Official DOH Regulatory Compliance Audit',
            ]));

        $response->assertOk();
        $response->assertSee('PHI Override Active');
        $response->assertSee('UNMASKED_ALLERGIC_RHINITIS_DIAGNOSIS');

        // Verify immutable audit log was created
        $this->assertTrue(
            AuditLog::where('action', 'like', '%Admin Clinical PHI Override Accessed%')
                ->where('model_id', $patient->id)
                ->exists()
        );
    }

    public function test_pharmacy_dispense_shortfall_guard_prevents_negative_or_unbacked_inventory()
    {
        $patient = Patient::create([
            'patient_id' => 'PT-PHARM-' . rand(1000, 9999),
            'first_name' => 'Clara',
            'last_name' => 'Oswald',
            'dob' => '1992-11-23',
            'address' => 'Silang, Cavite',
            'classification' => 'Adult',
            'sex' => 'Female',
        ]);

        $medicine = Medicine::create([
            'name' => 'Paracetamol 500mg Tab',
            'category' => 'Analgesic',
            'unit' => 'tablet',
            'current_stock' => 5, // Only 5 available in medicine table
        ]);

        // Batch only has 5
        MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => 'BATCH-TEST-001',
            'quantity' => 5,
            'original_quantity' => 5,
            'expiration_date' => now()->addMonths(6)->toDateString(),
            'status' => 'active',
        ]);

        $consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $this->doctor->id,
            'queue_number' => 'REG-PH-01',
            'consultation_date' => today()->toDateString(),
            'status' => 'completed',
        ]);

        $prescription = Prescription::create([
            'consultation_id' => $consultation->id,
            'patient_id' => $patient->patient_id,
            'doctor_id' => $this->doctor->id,
            'status' => 'pending',
        ]);

        $item = PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_id' => $medicine->id,
            'medicine_name' => $medicine->name,
            'quantity' => 10, // Demands 10, but batch only has 5!
            'dosage' => '1 tab TID',
            'instructions' => 'Take after meals',
        ]);

        // Attempting to dispense 10 when only 5 exist in batch
        $response = $this->actingAs($this->pharmacist)
            ->post(route('pharmacy.dispense', $prescription->id), [
                'items' => [
                    [
                        'item_id' => $item->id,
                        'quantity' => 10,
                    ],
                ],
            ]);

        // It should catch the shortfall and redirect back with error
        $response->assertSessionHas('error');
        $this->assertStringContainsString('only 5 in stock', session('error'));

        // Verify stock was not deducted into negative or partial dirty state
        $this->assertEquals(5, $medicine->batches()->sum('quantity'));
        $prescription->refresh();
        $this->assertEquals('pending', $prescription->status);
    }
}
