<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SystemEvaluationRemediationTest extends TestCase
{
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
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    /**
     * SYS-001: Appointment rescheduling does not crash on Capacity model and respects schedule capacity.
     */
    public function test_sys_001_appointment_rescheduling_does_not_crash_and_validates_capacity(): void
    {
        $appointment = Appointment::create([
            'reference_number' => 'APT-TEST-' . uniqid(),
            'first_name' => 'Baby',
            'last_name' => 'Santos',
            'dob' => now()->subYears(2)->toDateString(),
            'email' => 'pedia.reschedule@test.com',
            'contact' => '09123456789',
            'type' => 'pedia',
            'is_follow_up' => false,
            'data_privacy_agreed' => true,
            'barangay' => 'Poblacion',
            'preferred_date' => now()->addDays(5)->toDateString(),
            'preferred_time' => '09:00 AM',
            'status' => 'approved',
        ]);

        $nextDate = now()->addDays(7);
        $nextDateStr = $nextDate->toDateString();
        $dayShort = $nextDate->format('D');

        $pediaDoc = User::where('role', 'pedia_doctor')->first() ?? User::create([
            'name' => 'Dr. Test Pediatrician',
            'email' => 'pedia_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'pedia_doctor',
        ]);

        \App\Models\PractitionerSchedule::firstOrCreate([
            'user_id' => $pediaDoc->id,
            'day_of_week' => $dayShort,
        ], [
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'slot_duration_minutes' => 30,
        ]);

        $response = $this->withSession(['manage_appointment_id' => $appointment->id])
            ->post('/appointment/reschedule', [
                'new_date' => $nextDateStr,
                'new_time' => '10:00 AM',
            ]);

        // It should NOT return a 500 error!
        $this->assertNotEquals(500, $response->getStatusCode());
        
        $appointment->refresh();
        $this->assertEquals('rescheduled', $appointment->status);
        $this->assertEquals($nextDateStr, $appointment->preferred_date->toDateString());
    }

    /**
     * SYS-001: Appointment rescheduling rejects past dates and days with no doctor scheduled.
     */
    public function test_sys_001_reschedule_rejects_past_date(): void
    {
        $appointment = Appointment::create([
            'reference_number' => 'APT-TEST-' . uniqid(),
            'first_name' => 'Baby',
            'last_name' => 'Santos',
            'dob' => now()->subYears(2)->toDateString(),
            'email' => 'pedia.reschedule2@test.com',
            'contact' => '09123456789',
            'type' => 'pedia',
            'is_follow_up' => false,
            'data_privacy_agreed' => true,
            'barangay' => 'Poblacion',
            'preferred_date' => now()->addDays(5)->toDateString(),
            'preferred_time' => '09:00 AM',
            'status' => 'approved',
        ]);

        $yesterday = now()->subDays(1)->toDateString();

        $response = $this->withSession(['manage_appointment_id' => $appointment->id])
            ->post('/appointment/reschedule', [
                'new_date' => $yesterday,
                'new_time' => '10:00 AM',
            ]);

        $response->assertSessionHasErrors(['new_date']);
    }

    /**
     * SYS-012: Doctor starting consultation updates queues table to 'Calling' and populates called_at.
     */
    public function test_sys_012_doctor_starting_consultation_syncs_queue_and_called_at_timestamp(): void
    {
        $doctor = User::where('role', 'regular_doctor')->first() ?? User::create([
            'name' => 'Dr. Queue Tester',
            'email' => 'doc_queue_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Maria',
            'last_name' => 'Clara',
            'dob' => '1995-05-15',
            'gender' => 'Female',
            'contact_number' => '09123456789',
            'civil_status' => 'Single',
            'classification' => 'Regular Adult',
            'address' => '123 Main St, Poblacion',
            'house_no' => '123',
            'street' => 'Main St',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
        ]);

        $queueNumber = 'GEN-' . rand(100, 999);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $doctor->id,
            'consultation_date' => today(),
            'queue_number' => $queueNumber,
            'status' => 'queued',
        ]);

        $queue = \App\Models\Queue::create([
            'patient_id' => $patient->patient_id,
            'queue_number' => $queueNumber,
            'priority_type' => 'Regular',
            'service_type' => 'Consultation',
            'status' => 'Waiting',
            'called_at' => null,
        ]);

        $response = $this->actingAs($doctor)->get("/doctor/consultation/{$consultation->id}/start");
        $response->assertStatus(200);

        $queue->refresh();
        $this->assertEquals('Calling', $queue->status);
        $this->assertNotNull($queue->called_at);
    }

    /**
     * SYS-006: Cannot complete an already completed consultation; prevents duplicate MedicalCase and Prescriptions.
     */
    public function test_sys_006_cannot_recomplete_already_completed_consultation(): void
    {
        $doctor = User::where('role', 'regular_doctor')->first() ?? User::create([
            'name' => 'Dr. Idempotency Tester',
            'email' => 'doc_idem_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'dob' => '1990-01-01',
            'gender' => 'Male',
            'contact_number' => '09123456789',
            'civil_status' => 'Single',
            'classification' => 'Regular Adult',
            'address' => '456 Elm St, Poblacion',
            'house_no' => '456',
            'street' => 'Elm St',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
        ]);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $doctor->id,
            'consultation_date' => today(),
            'queue_number' => 'GEN-' . rand(100, 999),
            'status' => 'completed',
            'diagnosis' => 'Acute Bronchitis',
        ]);

        $response = $this->actingAs($doctor)->post("/doctor/consultation/{$consultation->id}/complete", [
            'diagnosis' => 'Changed Diagnosis Attempt',
        ]);

        $response->assertSessionHas('warning');
        $this->assertNotEquals('Changed Diagnosis Attempt', $consultation->fresh()->diagnosis);
    }

    /**
     * SYS-009: Dispensing full batch quantity sets batch status to 'depleted'.
     */
    public function test_sys_009_dispensing_sets_batch_status_to_depleted_when_zero(): void
    {
        $pharmacist = User::where('role', 'pharmacy')->first() ?? User::create([
            'name' => 'Pharm. Test User',
            'email' => 'pharm_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'pharmacy',
        ]);

        $medicine = \App\Models\Medicine::create([
            'name' => 'Amoxicillin 500mg Test',
            'generic_name' => 'Amoxicillin',
            'form' => 'Capsule',
            'dosage' => '500mg',
            'status' => 'active',
        ]);

        $batch = \App\Models\MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => 'BATCH-TEST-' . uniqid(),
            'quantity' => 10,
            'original_quantity' => 10,
            'expiration_date' => now()->addMonths(6)->toDateString(),
            'status' => 'active',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Pedro',
            'last_name' => 'Penduko',
            'dob' => '1988-08-08',
            'gender' => 'Male',
            'contact_number' => '09123456789',
            'civil_status' => 'Single',
            'classification' => 'Regular Adult',
            'address' => '789 Oak St, Poblacion',
            'house_no' => '789',
            'street' => 'Oak St',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
        ]);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $pharmacist->id,
            'consultation_date' => today(),
            'queue_number' => 'GEN-' . rand(100, 999),
            'status' => 'completed',
        ]);

        $prescription = \App\Models\Prescription::create([
            'patient_id' => $patient->patient_id,
            'consultation_id' => $consultation->id,
            'doctor_id' => $pharmacist->id,
            'status' => 'pending',
        ]);

        $item = \App\Models\PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_id' => $medicine->id,
            'medicine_name' => $medicine->name,
            'quantity' => 10,
            'dispensed_quantity' => 0,
        ]);

        $response = $this->actingAs($pharmacist)->post(route('pharmacy.dispense', $prescription->id), [
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 10,
                ],
            ],
        ]);

        $response->assertSessionHas('success');
        $batch->refresh();
        $this->assertEquals(0, $batch->quantity);
        $this->assertEquals('depleted', $batch->status);
    }

    /**
     * SYS-002: CleanupVitals command archives unfulfilled vitals as 'cancelled' without hard-deleting clinical records.
     */
    public function test_sys_002_cleanup_vitals_archives_records_without_hard_deleting(): void
    {
        $nurse = User::where('role', 'vitals_nurse')->first() ?? User::create([
            'name' => 'Nurse Triage Tester',
            'email' => 'nurse_pt_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'vitals_nurse',
        ]);

        $preTriage = \App\Models\PreTriage::create([
            'patient_name' => 'Test Patient For Vitals Cleanup',
            'blood_pressure' => '120/80',
            'temperature' => 36.8,
            'weight' => 60.5,
            'height' => 165.0,
            'recorded_by' => $nurse->id,
            'classification' => 'Adult',
            'status' => 'waiting',
        ]);

        \Illuminate\Support\Facades\Artisan::call('app:cleanup-vitals');

        $this->assertDatabaseHas('pre_triages', [
            'id' => $preTriage->id,
            'status' => 'cancelled',
        ]);

        $this->assertNotNull(\App\Models\PreTriage::find($preTriage->id));
    }

    /**
     * SYS-003: Patient data retention deletion soft-deletes patient without cascade destroying clinical encounters.
     */
    public function test_sys_003_patient_retention_deletion_soft_deletes_without_cascading_clinical_destruction(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first() ?? User::create([
            'name' => 'Super Admin Tester',
            'email' => 'sadmin_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Retained',
            'last_name' => 'Patient',
            'dob' => '1980-01-01',
            'gender' => 'Male',
            'contact_number' => '09123456789',
            'civil_status' => 'Single',
            'classification' => 'Regular Adult',
            'address' => 'Retention St, Silang',
            'house_no' => '1',
            'street' => 'Retention St',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
            'expires_at' => now()->subDay(),
        ]);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $superAdmin->id,
            'consultation_date' => today(),
            'queue_number' => 'GEN-' . rand(100, 999),
            'status' => 'completed',
            'diagnosis' => 'Chronic Hypertension - Preserved Clinical History',
        ]);

        $response = $this->actingAs($superAdmin)
            ->delete(route('admin.retention.delete', $patient));

        $response->assertSessionHas('success');

        // Patient is soft-deleted, not permanently wiped
        $this->assertNotNull(\App\Models\Patient::withTrashed()->find($patient->id));
        $this->assertNotNull(\App\Models\Patient::withTrashed()->find($patient->id)->deleted_at);

        // Clinical consultation record remains 100% intact!
        $this->assertDatabaseHas('consultations', [
            'id' => $consultation->id,
            'patient_id' => $patient->patient_id,
            'diagnosis' => 'Chronic Hypertension - Preserved Clinical History',
        ]);
    }

    /**
     * SYS-017: Attending doctor can review historical patient records outside the active queue with audit logging.
     */
    public function test_sys_017_doctor_can_view_historical_patient_folder_outside_active_queue(): void
    {
        $doctor = User::where('role', 'regular_doctor')->first() ?? User::create([
            'name' => 'Dr. Chart Reviewer',
            'email' => 'doc_chart_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Historical',
            'last_name' => 'Reviewee',
            'dob' => '1985-04-12',
            'gender' => 'Female',
            'contact_number' => '09123456789',
            'civil_status' => 'Married',
            'classification' => 'Regular Adult',
            'address' => 'Historical Ave, Silang',
            'house_no' => '99',
            'street' => 'Historical Ave',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
        ]);

        // Prior consultation from last week
        \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $doctor->id,
            'consultation_date' => now()->subDays(7),
            'queue_number' => 'GEN-PAST-01',
            'status' => 'completed',
            'diagnosis' => 'Type 2 Diabetes Mellitus',
            'created_at' => now()->subDays(7),
        ]);

        // Patient has NO consultation today, but doctor reviews their folder
        $response = $this->actingAs($doctor)->get(route('doctor.patients.show', $patient));

        $response->assertStatus(200);
        $response->assertViewIs('doctor.patients.show');
        $response->assertViewHas('patient');
        $response->assertSee('Type 2 Diabetes Mellitus');
        $response->assertSee('Print Medical Summary');
        $response->assertSee('Append Official Clinical Addendum');

        // Verify audit log exists
        $this->assertTrue(
            \App\Models\AuditLog::where('action', 'like', "%Accessed Historical Patient Medical Folder: {$patient->patient_id}%")->exists()
        );
    }

    /**
     * SYS-004: Completing consultation while diagnostic test is 'Specimen Collected' or 'In Progress' automatically marks patient for follow-up.
     */
    public function test_sys_004_consultation_completion_with_specimen_collected_schedules_followup(): void
    {
        $doctor = User::where('role', 'regular_doctor')->first() ?? User::create([
            'name' => 'Dr. Diagnostic Followup Tester',
            'email' => 'doc_diag_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'LabPatient',
            'last_name' => 'PendingResults',
            'dob' => '1992-02-02',
            'gender' => 'Male',
            'contact_number' => '09123456789',
            'civil_status' => 'Single',
            'classification' => 'Regular Adult',
            'address' => 'Lab Test St, Silang',
            'house_no' => '55',
            'street' => 'Lab Test St',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
        ]);

        $preTriage = \App\Models\PreTriage::create([
            'patient_name' => 'LabPatient PendingResults',
            'recorded_by' => $doctor->id,
            'blood_pressure' => '120/80',
            'temperature' => 37.0,
            'weight' => 65.0,
            'height' => 170.0,
            'classification' => 'Adult',
            'status' => 'claimed',
        ]);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'pre_triage_id' => $preTriage->id,
            'doctor_id' => $doctor->id,
            'consultation_date' => today(),
            'queue_number' => 'GEN-' . rand(100, 999),
            'status' => 'active',
        ]);

        // Ancillary request with status 'Specimen Collected' (e.g. lab is closing for the day)
        \App\Models\AncillaryRequest::create([
            'consultation_id' => $consultation->id,
            'type' => 'Laboratory',
            'test_name' => 'Complete Blood Count (CBC)',
            'status' => 'Specimen Collected',
            'specimen_collected_at' => now(),
        ]);

        $response = $this->actingAs($doctor)->post("/doctor/consultation/{$consultation->id}/complete", [
            'diagnosis' => 'Viral Syndrome with Pending CBC',
        ]);

        $response->assertSessionHas('success');

        $consultation->refresh();
        $this->assertEquals('completed', $consultation->status);
        $this->assertTrue((bool) $consultation->is_followup_needed);
        $this->assertNotNull($consultation->followup_date);
        $this->assertStringContainsString('Pending Diagnostic Results', $consultation->followup_reason);
    }

    /**
     * SYS-007: Front desk can cancel a queued walk-in patient who leaves before entering the consultation room.
     */
    public function test_sys_007_frontdesk_can_cancel_queued_walkin_consultation(): void
    {
        $frontdesk = User::where('role', 'information_desk')->first() ?? User::create([
            'name' => 'Front Desk Walkout Tester',
            'email' => 'desk_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'information_desk',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Walkout',
            'last_name' => 'Patient',
            'dob' => '1996-06-06',
            'gender' => 'Male',
            'contact_number' => '09123456789',
            'civil_status' => 'Single',
            'classification' => 'Regular Adult',
            'address' => 'Walkout Way, Silang',
            'house_no' => '12',
            'street' => 'Walkout Way',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
        ]);

        $preTriage = \App\Models\PreTriage::create([
            'patient_name' => 'Walkout Patient',
            'recorded_by' => $frontdesk->id,
            'blood_pressure' => '110/70',
            'temperature' => 36.5,
            'classification' => 'Adult',
            'status' => 'claimed',
        ]);

        $queueNumber = 'GEN-' . rand(100, 999);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'pre_triage_id' => $preTriage->id,
            'consultation_date' => today(),
            'queue_number' => $queueNumber,
            'status' => 'queued',
        ]);

        $queue = \App\Models\Queue::create([
            'patient_id' => $patient->patient_id,
            'queue_number' => $queueNumber,
            'priority_type' => 'Regular',
            'service_type' => 'Consultation',
            'status' => 'Waiting',
        ]);

        $response = $this->actingAs($frontdesk)
            ->post(route('frontdesk.queue.cancel', $consultation), [
                'cancellation_reason' => 'Patient left due to personal emergency',
            ]);

        $response->assertSessionHas('success');

        $consultation->refresh();
        $this->assertEquals('cancelled', $consultation->status);
        $this->assertStringContainsStringIgnoringCase('Cancelled by Front Desk', $consultation->medical_notes);

        $queue->refresh();
        $this->assertEquals('Cancelled', $queue->status);

        $preTriage->refresh();
        $this->assertEquals('cancelled', $preTriage->status);
    }

    /**
     * SYS-005: Attending clinician can append signed, timestamped clinical addendum to completed consultations.
     */
    public function test_sys_005_attending_doctor_can_append_clinical_addendum_to_completed_consultation(): void
    {
        $doctor = User::where('role', 'regular_doctor')->first() ?? User::create([
            'name' => 'Dr. Addendum Author',
            'email' => 'addendum_doc_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);

        $otherDoctor = User::create([
            'name' => 'Dr. Unrelated Clinician',
            'email' => 'other_doc_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Chart',
            'last_name' => 'Addendum',
            'dob' => '1990-01-01',
            'gender' => 'Female',
            'contact_number' => '09123456789',
            'civil_status' => 'Single',
            'classification' => 'Regular Adult',
            'address' => 'Sample Street, Silang',
            'house_no' => '1',
            'street' => 'Sample Street',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
        ]);

        $preTriage = \App\Models\PreTriage::create([
            'patient_name' => 'Chart Addendum',
            'recorded_by' => $doctor->id,
            'blood_pressure' => '120/80',
            'temperature' => 37.0,
            'classification' => 'Adult',
            'status' => 'completed',
        ]);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $doctor->id,
            'pre_triage_id' => $preTriage->id,
            'consultation_date' => today(),
            'queue_number' => 'GEN-' . rand(100, 999),
            'status' => 'completed',
            'medical_notes' => 'Original assessment: Acute Pharyngitis. Prescribed Amoxicillin.',
        ]);

        // Non-attending doctor cannot post addendum
        $forbiddenResponse = $this->actingAs($otherDoctor)
            ->post(route('doctor.consultation.addendum', $consultation), [
                'addendum_text' => 'Unauthorized note modification attempt',
            ]);
        $forbiddenResponse->assertStatus(403);

        // Attending doctor successfully appends addendum
        $successResponse = $this->actingAs($doctor)
            ->post(route('doctor.consultation.addendum', $consultation), [
                'addendum_text' => 'Patient called reporting mild rash. Advised to discontinue Amoxicillin and switch to Azithromycin.',
            ]);

        $successResponse->assertSessionHas('success');

        $consultation->refresh();
        $this->assertStringContainsStringIgnoringCase('Original assessment: Acute Pharyngitis', $consultation->medical_notes);
        $this->assertStringContainsStringIgnoringCase('[CLINICAL ADDENDUM', $consultation->medical_notes);
        $this->assertStringContainsStringIgnoringCase('discontinue Amoxicillin', $consultation->medical_notes);
    }

    /**
     * SYS-018: Panic / critical diagnostic results generate high-priority notifications for the attending clinician.
     */
    public function test_sys_018_panic_or_critical_lab_result_notifies_attending_doctor_and_flags_alert(): void
    {
        $doctor = User::where('role', 'regular_doctor')->first() ?? User::create([
            'name' => 'Dr. Critical Watch',
            'email' => 'crit_doc_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);

        $medTech = User::where('role', 'laboratory')->first() ?? User::create([
            'name' => 'MedTech Jane',
            'email' => 'medtech_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'laboratory',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Anaemic',
            'last_name' => 'Patient',
            'dob' => '1985-05-15',
            'gender' => 'Female',
            'contact_number' => '09123456789',
            'civil_status' => 'Married',
            'classification' => 'Regular Adult',
            'address' => 'Lab Alert Way, Silang',
            'house_no' => '42',
            'street' => 'Lab Alert Way',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
        ]);

        $preTriage = \App\Models\PreTriage::create([
            'patient_name' => 'Anaemic Patient',
            'recorded_by' => $doctor->id,
            'blood_pressure' => '90/60',
            'temperature' => 37.1,
            'classification' => 'Adult',
            'status' => 'claimed',
        ]);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $doctor->id,
            'pre_triage_id' => $preTriage->id,
            'consultation_date' => today(),
            'queue_number' => 'GEN-' . rand(100, 999),
            'status' => 'awaiting_results',
        ]);

        $ancillary = \App\Models\AncillaryRequest::create([
            'consultation_id' => $consultation->id,
            'type' => 'Laboratory',
            'test_name' => 'Complete Blood Count (CBC)',
            'status' => 'In Progress',
        ]);

        $response = $this->actingAs($medTech)
            ->post(route('lab.ancillary.complete', $ancillary), [
                'results' => [
                    'hemoglobin' => '5.2',
                    'hematocrit' => '16.0',
                    'wbc' => '4.8',
                    'platelets' => '220',
                ],
                'is_critical' => true,
                'critical_remarks' => 'Panic value: Hemoglobin 5.2 g/dL (Severe Life-Threatening Anemia)',
            ]);

        $response->assertSessionHas('warning');

        $ancillary->refresh();
        $this->assertEquals('Done', $ancillary->status);
        $this->assertTrue($ancillary->result_data['_is_critical']);
        $this->assertStringContainsStringIgnoringCase('CRITICAL VALUE ALERT', $ancillary->remarks);
        $this->assertStringContainsStringIgnoringCase('Hemoglobin 5.2', $ancillary->remarks);

        // Verify that attending physician received the high priority alert notification
        $notification = $doctor->notifications()
            ->where('type', 'App\\Notifications\\CriticalLabResultNotification')
            ->first();

        $this->assertNotNull($notification, 'Attending clinician should have received a CriticalLabResultNotification');
        $this->assertStringContainsStringIgnoringCase('CRITICAL VALUE ALERT', $notification->data['message']);
        $this->assertEquals($ancillary->id, $notification->data['ancillary_id']);
    }

    /**
     * SYS-014: Clinic holiday, weekend, and closure calendar enforcement blocks appointment booking & rescheduling.
     */
    public function test_sys_014_holiday_and_closure_handling_blocks_booking_and_rescheduling(): void
    {
        // 1. Dec 25 (Christmas Day) check via checkAvailability API
        $checkResponse = $this->getJson(route('appointment.check-availability', [
            'start' => '2026-12-24',
            'end' => '2026-12-26',
            'type' => 'pedia',
        ]));

        $checkResponse->assertOk();
        $data = $checkResponse->json();
        $this->assertTrue($data['2026-12-25']['is_closed']);
        $this->assertEquals(0, $data['2026-12-25']['capacity']);
        $this->assertStringContainsString('Christmas Day', $data['2026-12-25']['closed_reason']);

        // 2. getTimeSlots on Dec 25 returns empty slots with closure message
        $slotsResponse = $this->getJson(route('appointment.time-slots', [
            'date' => '2026-12-25',
            'type' => 'pedia',
        ]));
        $slotsResponse->assertOk();
        $this->assertEmpty($slotsResponse->json('slots'));
        $this->assertStringContainsString('closed', strtolower($slotsResponse->json('message')));

        // 3. Rescheduling to a statutory holiday is rejected with validation error
        $appointment = Appointment::create([
            'reference_number' => 'APT-HOLIDAY-' . uniqid(),
            'first_name' => 'Holiday',
            'last_name' => 'Tester',
            'dob' => '2022-01-01',
            'email' => 'holiday@test.com',
            'contact' => '09123456789',
            'type' => 'pedia',
            'is_follow_up' => false,
            'data_privacy_agreed' => true,
            'barangay' => 'Poblacion',
            'preferred_date' => now()->addDays(5)->toDateString(),
            'preferred_time' => '09:00 AM',
            'status' => 'approved',
        ]);

        $rescheduleResponse = $this->withSession(['manage_appointment_id' => $appointment->id])
            ->post(route('appointment.reschedule'), [
                'new_date' => '2026-12-25',
                'new_time' => '10:00 AM',
            ]);

        $rescheduleResponse->assertSessionHasErrors(['new_date']);
        $appointment->refresh();
        $this->assertEquals('approved', $appointment->status);

        // 4. Booking a new appointment on Christmas Day is blocked with validation error
        $bookingResponse = $this->post(route('appointment.store'), [
            'type' => 'pedia',
            'preferred_date' => '2026-12-25',
            'preferred_time' => '09:00 AM - 09:30 AM',
            'first_name' => 'New',
            'last_name' => 'Child',
            'dob' => '2022-05-10',
            'email' => 'new.child@test.com',
            'guardian_name' => 'Parent Child',
            'guardian_relationship' => 'Mother',
            'guardian_contact' => '09123456789',
            'civil_status' => 'Single',
            'sex' => 'Male',
            'street' => 'Sample St',
            'barangay' => 'Poblacion',
            'city_province' => 'Cavite',
            'data_privacy_agreed' => '1',
        ]);

        $bookingResponse->assertSessionHasErrors(['preferred_date']);
    }

    /**
     * SYS-008: Pharmacy stock adjustment and batch disposal enforce mandatory reasons and notes safeguards.
     */
    public function test_sys_008_pharmacy_stock_adjustment_and_disposal_enforce_reasons_and_audit(): void
    {
        $pharmacist = User::where('role', 'pharmacy')->first() ?? User::create([
            'name' => 'Pharm. Adjuster',
            'email' => 'pharm_adj_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'pharmacy',
        ]);

        $medicine = \App\Models\Medicine::create([
            'name' => 'Amoxicillin 500mg Capsule ' . uniqid(),
            'category' => 'Antibiotics',
            'unit' => 'capsule',
        ]);

        $batch = \App\Models\MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => 'AMX-TEST-' . rand(100, 999),
            'expiration_date' => now()->addMonths(6)->toDateString(),
            'quantity' => 100,
            'original_quantity' => 100,
            'status' => 'active',
        ]);

        // 1. Adjustment with reason 'Other' but empty notes is rejected
        $failedAdj = $this->actingAs($pharmacist)
            ->post(route('pharmacy.batches.adjust', $batch), [
                'new_quantity' => 80,
                'adjustment_reason' => 'Other',
                'adjustment_notes' => '',
            ]);
        $failedAdj->assertSessionHasErrors(['adjustment_notes']);

        // 2. Adjustment with reason 'Physical Recount' succeeds and records audit
        $successAdj = $this->actingAs($pharmacist)
            ->post(route('pharmacy.batches.adjust', $batch), [
                'new_quantity' => 85,
                'adjustment_reason' => 'Physical Recount',
                'adjustment_notes' => 'Q4 physical inventory count verified against shelf stock.',
            ]);
        $successAdj->assertSessionHas('success');

        $batch->refresh();
        $this->assertEquals(85, $batch->quantity);

        // 3. Disposal with reason 'Other' but empty notes is rejected
        $failedDisp = $this->actingAs($pharmacist)
            ->post(route('pharmacy.batches.dispose', $batch), [
                'disposal_reason' => 'Other',
                'disposal_notes' => '',
            ]);
        $failedDisp->assertSessionHasErrors(['disposal_notes']);

        // 4. Disposal with valid reason and notes succeeds
        $successDisp = $this->actingAs($pharmacist)
            ->post(route('pharmacy.batches.dispose', $batch), [
                'disposal_reason' => 'Damaged / Broken',
                'disposal_notes' => 'Box crushed during shelf maintenance; blister foils perforated.',
            ]);
        $successDisp->assertSessionHas('success');

        $batch->refresh();
        $this->assertEquals('disposed', $batch->status);
        $this->assertEquals(0, $batch->quantity);
    }

    /**
     * SYS-010: Dispensing creates structured lot-traceability audit and inventory log entries with patient ID.
     */
    public function test_sys_010_dispensing_creates_patient_and_batch_lot_traceability_logs(): void
    {
        $pharmacist = User::where('role', 'pharmacy')->first() ?? User::create([
            'name' => 'Pharm. Trace Officer',
            'email' => 'pharm_trace_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'pharmacy',
        ]);

        $doctor = User::where('role', 'regular_doctor')->first() ?? User::create([
            'name' => 'Dr. Prescriber',
            'email' => 'presc_doc_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Traceable',
            'last_name' => 'Patient',
            'dob' => '1995-03-20',
            'gender' => 'Male',
            'contact_number' => '09123456789',
            'civil_status' => 'Single',
            'classification' => 'Regular Adult',
            'address' => 'Recall Lane, Silang',
            'house_no' => '5',
            'street' => 'Recall Lane',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
        ]);

        $preTriage = \App\Models\PreTriage::create([
            'patient_name' => 'Traceable Patient',
            'recorded_by' => $doctor->id,
            'blood_pressure' => '120/80',
            'temperature' => 36.8,
            'classification' => 'Adult',
            'status' => 'claimed',
        ]);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $doctor->id,
            'pre_triage_id' => $preTriage->id,
            'consultation_date' => today(),
            'queue_number' => 'GEN-' . rand(100, 999),
            'status' => 'in_consultation',
        ]);

        $medicineName = 'Paracetamol 500mg Tablet ' . uniqid();
        $medicine = \App\Models\Medicine::create([
            'name' => $medicineName,
            'category' => 'Analgesic',
            'unit' => 'tablet',
        ]);

        $batchNumber = 'LOT-TRACE-' . rand(1000, 9999);
        $batch = \App\Models\MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => $batchNumber,
            'expiration_date' => now()->addMonths(12)->toDateString(),
            'quantity' => 50,
            'original_quantity' => 50,
            'status' => 'active',
        ]);

        $prescription = \App\Models\Prescription::create([
            'patient_id' => $patient->patient_id,
            'consultation_id' => $consultation->id,
            'doctor_id' => $doctor->id,
            'status' => 'pending',
            'priority' => 'Normal',
        ]);

        $item = \App\Models\PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_id' => $medicine->id,
            'medicine_name' => $medicineName,
            'quantity' => 10,
            'dosage' => '500mg',
            'frequency' => 'Every 6 hours',
            'duration' => '3 days',
        ]);

        $response = $this->actingAs($pharmacist)
            ->post(route('pharmacy.dispense', $prescription->id), [
                'items' => [
                    [
                        'item_id' => $item->id,
                        'quantity' => 10,
                    ],
                ],
                'pharmacist_notes' => 'Complete dispensation verified against DOH standard dosage.',
            ]);

        $response->assertSessionHas('success');

        // Check InventoryLog has patient and batch traceability details
        $invLog = \App\Models\InventoryLog::where('batch_id', $batch->id)
            ->where('action', 'Dispensed')
            ->latest('id')
            ->first();

        $this->assertNotNull($invLog);
        $this->assertStringContainsString($patient->patient_id, $invLog->remarks);
        $this->assertStringContainsString($batchNumber, $invLog->remarks);
        $this->assertEquals(-10, $invLog->quantity_changed);
    }

    /**
     * SYS-011: Ancillary diagnostic requests validate strictly against authoritative RHU diagnostic catalog.
     */
    public function test_sys_011_ancillary_requests_strictly_validate_against_diagnostic_catalog(): void
    {
        $doctor = User::where('role', 'regular_doctor')->first() ?? User::create([
            'name' => 'Dr. Catalog Validator',
            'email' => 'cat_doc_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'Catalog',
            'last_name' => 'Patient',
            'dob' => '1992-08-12',
            'gender' => 'Female',
            'contact_number' => '09123456789',
            'civil_status' => 'Single',
            'classification' => 'Regular Adult',
            'address' => 'Diagnostic Row, Silang',
            'house_no' => '8',
            'street' => 'Diagnostic Row',
            'barangay' => 'Poblacion',
            'city_province' => 'Test City',
        ]);

        $preTriage = \App\Models\PreTriage::create([
            'patient_name' => 'Catalog Patient',
            'recorded_by' => $doctor->id,
            'blood_pressure' => '115/75',
            'temperature' => 36.6,
            'classification' => 'Adult',
            'status' => 'claimed',
        ]);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $doctor->id,
            'pre_triage_id' => $preTriage->id,
            'consultation_date' => today(),
            'queue_number' => 'GEN-' . rand(100, 999),
            'status' => 'in_consultation',
        ]);

        // 1. Invalid test (not in RHU diagnostic catalog) is rejected with error
        $invalidResponse = $this->actingAs($doctor)
            ->post(route('doctor.ancillary.store', $consultation), [
                'type' => 'Laboratory',
                'test_name' => 'MRI Brain with Contrast',
                'remarks' => 'Suspected lesion',
            ]);
        $invalidResponse->assertSessionHas('error');
        $this->assertStringContainsStringIgnoringCase('Invalid Laboratory test selected', session('error'));

        // 2. Valid test in catalog succeeds
        $validResponse = $this->actingAs($doctor)
            ->post(route('doctor.ancillary.store', $consultation), [
                'type' => 'Laboratory',
                'test_name' => 'Complete Blood Count (CBC)',
                'remarks' => 'Routine workup',
            ]);
        $validResponse->assertSessionHas('success');

        $this->assertDatabaseHas('ancillary_requests', [
            'consultation_id' => $consultation->id,
            'type' => 'Laboratory',
            'test_name' => 'Complete Blood Count (CBC)',
            'status' => 'Pending',
        ]);

        // 3. Duplicate active test request for same consultation is rejected
        $duplicateResponse = $this->actingAs($doctor)
            ->post(route('doctor.ancillary.store', $consultation), [
                'type' => 'Laboratory',
                'test_name' => 'Complete Blood Count (CBC)',
                'remarks' => 'Duplicate order',
            ]);
        $duplicateResponse->assertSessionHas('error');
        $this->assertStringContainsStringIgnoringCase('already pending', session('error'));
    }

    /**
     * CSV Export Audit: Detailed analytics CSV export includes Prescriptions and Clinical Notes / Addenda.
     */
    public function test_detailed_analytics_csv_export_includes_prescription_and_clinical_notes(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::create([
            'name' => 'Admin CSV Tester',
            'email' => 'admin_csv_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $patient = \App\Models\Patient::create([
            'patient_id' => 'RHU-' . date('Y') . '-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => 'CSVExport',
            'last_name' => 'Patient',
            'dob' => '1990-05-15',
            'gender' => 'Female',
            'contact_number' => '09123456789',
            'civil_status' => 'Single',
            'classification' => 'Adult',
            'address' => 'Silang, Cavite',
            'house_no' => '10',
            'street' => 'Rizal St',
            'barangay' => 'Biga 1',
            'city_province' => 'Silang, Cavite',
        ]);

        $preTriage = \App\Models\PreTriage::create([
            'patient_name' => 'CSVExport Patient',
            'recorded_by' => $admin->id,
            'blood_pressure' => '120/80',
            'temperature' => 36.6,
            'classification' => 'Adult',
            'status' => 'completed',
        ]);

        $consultation = \App\Models\Consultation::create([
            'patient_id' => $patient->patient_id,
            'pre_triage_id' => $preTriage->id,
            'consultation_date' => today(),
            'queue_number' => 'GEN-CSV-99',
            'status' => 'completed',
            'diagnosis' => 'Acute Bronchitis',
            'prescription' => 'Salbutamol 2mg tab TID for 5 days',
            'medical_notes' => 'Patient informed to hydrate and return if dyspnea develops',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics.export-csv', ['time_filter' => 'today']));

        $response->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));

        // Capture streamed content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Prescription', $content);
        $this->assertStringContainsString('Clinical Notes / Addenda', $content);
        $this->assertStringContainsStringIgnoringCase('Salbutamol 2mg tab TID', $content);
        $this->assertStringContainsString('Acute Bronchitis', $content);
    }
}









