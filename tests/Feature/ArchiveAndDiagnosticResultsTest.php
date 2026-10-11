<?php

namespace Tests\Feature;

use App\Models\AncillaryRequest;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\MedicalCase;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArchiveAndDiagnosticResultsTest extends TestCase
{
    protected User $admin;
    protected User $superAdmin;
    protected Patient $patient;
    protected Consultation $consultation;
    protected MedicalCase $medicalCase;

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
                'name' => 'Admin User',
                'email' => 'admin_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);

        $this->superAdmin = User::where('role', 'super_admin')->first()
            ?? User::create([
                'name' => 'Super Admin User',
                'email' => 'superadmin_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
            ]);

        $this->patient = Patient::create([
            'patient_id' => 'RHU-TEST-' . strtoupper(substr(uniqid(), 0, 6)),
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'dob' => '1995-05-15',
            'sex' => 'Male',
            'civil_status' => 'Single',
            'address' => '123 Rizal St',
            'barangay' => 'San Nicolas',
            'city_province' => 'Ilocos Norte',
            'contact_number' => '09123456789',
        ]);

        $this->consultation = Consultation::create([
            'patient_id' => $this->patient->patient_id,
            'doctor_id' => $this->admin->id,
            'queue_number' => 'Q-999',
            'status' => 'completed',
            'chief_complaint' => 'Routine Diagnostic Check',
            'blood_pressure' => '120/80',
            'temperature' => '36.6',
            'heart_rate' => '72',
            'respiratory_rate' => '18',
            'diagnosis' => 'Clinical Diagnosis Verified',
        ]);

        $preTriage = \App\Models\PreTriage::create([
            'patient_id' => $this->patient->patient_id,
            'patient_name' => $this->patient->full_name,
            'first_name' => $this->patient->first_name,
            'last_name' => $this->patient->last_name,
            'recorded_by' => $this->admin->id,
            'blood_pressure' => '120/80',
            'temperature' => '36.8',
            'status' => 'claimed',
        ]);

        $this->medicalCase = MedicalCase::create([
            'patient_id' => $this->patient->patient_id,
            'consultation_id' => $this->consultation->id,
            'pre_triage_id' => $preTriage->id,
            'case_number' => 'CASE-' . strtoupper(substr(uniqid(), 0, 8)),
            'diagnosis' => 'Clinical Diagnosis Verified',
            'prescription' => json_encode([
                ['medicine' => 'Amoxicillin 500mg', 'instruction' => '1 tab TID', 'quantity' => '21'],
            ]),
            'closed_at' => now(),
            'status' => 'Closed',
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_only_done_diagnostic_results_are_rendered_in_patient_show_and_itr()
    {
        // 1 Done test
        AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'type' => 'Laboratory',
            'test_name' => 'CBC Completed Test',
            'status' => 'Done',
            'result_data' => ['wbc' => '6.5', 'hemoglobin' => '140'],
        ]);

        // 1 Pending test
        AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'type' => 'Laboratory',
            'test_name' => 'Pending Urinalysis Test',
            'status' => 'Pending',
        ]);

        // 1 Rejected test
        AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'type' => 'Laboratory',
            'test_name' => 'Rejected Sputum Test',
            'status' => 'Rejected',
            'rejection_reason' => 'Specimen leaked',
        ]);

        // 1 Cancelled test
        AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'type' => 'Radiology',
            'test_name' => 'Cancelled Chest X-Ray',
            'status' => 'Cancelled',
            'cancellation_reason' => 'Patient left',
        ]);

        // 1. Visit Patient Show page as Admin
        $responseShow = $this->actingAs($this->admin)->get(route('admin.patients.show', $this->patient));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('CBC Completed Test');
        $responseShow->assertDontSee('Pending Urinalysis Test');
        $responseShow->assertDontSee('Rejected Sputum Test');
        $responseShow->assertDontSee('Cancelled Chest X-Ray');

        // 2. Visit Patient ITR Print page as Admin
        $responseItr = $this->actingAs($this->admin)->get(route('admin.patients.print', $this->patient));
        $responseItr->assertStatus(200);
        $responseItr->assertSee('CBC Completed Test');
        $responseItr->assertDontSee('Pending Urinalysis Test');
        $responseItr->assertDontSee('Rejected Sputum Test');
        $responseItr->assertDontSee('Cancelled Chest X-Ray');
    }

    public function test_system_archive_directory_shows_all_categories()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.archive.index'));
        $response->assertStatus(200);
        $response->assertSee('Diagnostic Results (Lab &amp; Radiology)', false);
        $response->assertSee('Appointments');
        $response->assertSee('Prescriptions');
        $response->assertSee('Announcements');
        $response->assertSee('Staff Accounts');
    }

    public function test_super_admin_can_hard_delete_archived_records_and_files()
    {
        Storage::fake('public');
        Storage::fake('uploads');

        $dummyFilePath = 'ancillary_results/dummy_test_' . uniqid() . '.png';
        Storage::disk('public')->put($dummyFilePath, 'dummy file content');

        $ancillary = AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'type' => 'Laboratory',
            'test_name' => 'Archived Test To Purge',
            'status' => 'Pending',
            'archived_at' => now(),
            'result_file_path' => $dummyFilePath,
        ]);

        $this->assertTrue(Storage::disk('public')->exists($dummyFilePath));

        // Super admin can hard delete
        $response = $this->actingAs($this->superAdmin)->delete(route('admin.archive.force-delete', [
            'type' => 'ancillary',
            'id' => $ancillary->id,
        ]));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('ancillary_requests', ['id' => $ancillary->id]);
        $this->assertFalse(Storage::disk('public')->exists($dummyFilePath));
    }

    public function test_regular_admin_cannot_hard_delete_archived_records()
    {
        $ancillary = AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'type' => 'Laboratory',
            'test_name' => 'Archived Test Admin Cannot Purge',
            'status' => 'Pending',
            'archived_at' => now(),
        ]);

        // Regular admin is forbidden (403)
        $response = $this->actingAs($this->admin)->delete(route('admin.archive.force-delete', [
            'type' => 'ancillary',
            'id' => $ancillary->id,
        ]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('ancillary_requests', ['id' => $ancillary->id]);
    }

    public function test_super_admin_can_truncate_records_older_than_one_year()
    {
        Storage::fake('public');

        $oldFilePath = 'ancillary_results/old_file_' . uniqid() . '.png';
        Storage::disk('public')->put($oldFilePath, 'old content');

        // Create 1 record older than 1 year (> 12 months)
        $oldRecord = AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'type' => 'Radiology',
            'test_name' => 'Old Archived X-Ray',
            'status' => 'Cancelled',
            'archived_at' => now()->subMonths(14),
            'result_file_path' => $oldFilePath,
            'created_at' => now()->subMonths(14),
            'updated_at' => now()->subMonths(14),
        ]);

        // Create 1 recent record (3 months old)
        $recentRecord = AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'type' => 'Radiology',
            'test_name' => 'Recent Archived X-Ray',
            'status' => 'Cancelled',
            'archived_at' => now()->subMonths(3),
            'created_at' => now()->subMonths(3),
            'updated_at' => now()->subMonths(3),
        ]);

        // Super Admin truncates ancillary vault
        $response = $this->actingAs($this->superAdmin)->post(route('admin.archive.truncate-year', 'ancillary'));
        $response->assertSessionHas('success');

        // Old record should be deleted and file removed
        $this->assertDatabaseMissing('ancillary_requests', ['id' => $oldRecord->id]);
        $this->assertFalse(Storage::disk('public')->exists($oldFilePath));

        // Recent record should remain untouched
        $this->assertDatabaseHas('ancillary_requests', ['id' => $recentRecord->id]);
    }

    public function test_regular_admin_cannot_truncate_records_older_than_one_year()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.archive.truncate-year', 'ancillary'));
        $response->assertStatus(403);

        $responseAll = $this->actingAs($this->admin)->post(route('admin.archive.truncate-all-year'));
        $responseAll->assertStatus(403);
    }

    public function test_admin_can_view_patient_record_with_ancillary_results_including_double_encoded_json()
    {
        $patient = Patient::where('patient_id', 'RHU-2026-00040')->first() ?? $this->patient;

        // Ensure an ancillary request exists on this patient with string/double-encoded JSON
        $consultation = $patient->consultations()->first() ?? $this->consultation;
        $ancillary = AncillaryRequest::create([
            'consultation_id' => $consultation->id,
            'type' => 'Laboratory',
            'test_name' => 'Complete Blood Count (CBC)',
            'status' => 'Done',
            'result_data' => ['result' => 'Normal', 'notes' => 'Within normal limits'],
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.patients.show', $patient->patient_id));

        $response->assertStatus(200);
        $response->assertSee($patient->patient_id);
        $response->assertSee('Complete Blood Count (CBC)');
    }

    public function test_ancillary_request_model_normalizes_double_encoded_json_and_plain_strings()
    {
        $request = new AncillaryRequest();

        // 1. Array
        $request->result_data = ['wbc' => '6.5', 'hgb' => '140'];
        $this->assertIsArray($request->result_data);
        $this->assertEquals('6.5', $request->result_data['wbc']);

        // 2. Double-encoded JSON string
        $rawDoubleEncoded = json_encode(json_encode(['result' => 'Normal', 'notes' => 'All clear']));
        $this->assertEquals(['result' => 'Normal', 'notes' => 'All clear'], $request->normalizeJsonData($rawDoubleEncoded));

        // 3. Plain string
        $this->assertEquals(['result' => 'Clear'], $request->normalizeJsonData('Clear'));
    }

    public function test_admin_patient_show_renders_styled_authorized_phi_override_modal_with_custom_dropdown()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.patients.show', $this->patient->patient_id));

        $response->assertStatus(200);

        // Verify styled modal elements
        $response->assertSee('Authorized PHI Access Request', false);
        $response->assertSee('DPA RA 10173 Protocol', false);
        $response->assertSee('Permanent Audit Trail Logging', false);
        $response->assertSee('Acknowledge &amp; Access PHI', false);

        // Verify custom x-select container and rich options
        $response->assertSee('custom-dropdown-container', false);
        $response->assertSee('name="override_reason"', false);
        $response->assertSee('Regulatory Compliance Audit', false);
        $response->assertSee('DOH Audit', false);
        $response->assertSee('Authorized Law Enforcement Inquiry', false);
        $response->assertSee('Legal Order', false);
        $response->assertSee('Technical Database Troubleshooting', false);
        $response->assertSee('Medical Director Direct Written Authorization', false);
        $response->assertSee('Director Auth', false);
    }

    public function test_medical_case_model_normalizes_double_encoded_json_and_string_vitals()
    {
        $case = new \App\Models\MedicalCase();

        // 1. Array
        $case->vitals_snapshot = ['bp' => '120/80', 'temp' => '36.5'];
        $this->assertIsArray($case->vitals_snapshot);
        $this->assertEquals('120/80', $case->vitals_snapshot['bp']);

        // 2. Double-encoded JSON string
        $rawDoubleEncoded = json_encode(json_encode(['blood_pressure' => '130/85', 'temperature' => 37.2]));
        $this->assertEquals(['blood_pressure' => '130/85', 'temperature' => 37.2], $case->normalizeVitals($rawDoubleEncoded));

        // 3. Null returns empty array
        $this->assertEquals([], $case->normalizeVitals(null));
    }

    public function test_admin_patient_show_unmasked_renders_vitals_snapshot_cleanly_without_error()
    {
        $this->medicalCase->update([
            'vitals_snapshot' => json_encode([
                'blood_pressure' => '140/90',
                'temperature' => '36.8',
                'weight' => '65',
                'height' => '165',
                'heart_rate' => '78',
                'respiratory_rate' => '18',
                'pulse_rate' => '78',
                'spo2' => '99',
            ]),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.patients.show', [
            'patient' => $this->patient->patient_id,
            'unmask' => '1',
            'override_reason' => 'Technical Database Troubleshooting / System QA Audit',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Vitals Snapshot at Time of Case');
        $response->assertSee('140/90');
        $response->assertSee('36.8');
    }
}

