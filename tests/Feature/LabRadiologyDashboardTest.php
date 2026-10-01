<?php

namespace Tests\Feature;

use App\Models\AncillaryRequest;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LabRadiologyDashboardTest extends TestCase
{
    protected User $labUser;
    protected User $radUser;
    protected User $doctor;
    protected Patient $patient;
    protected Consultation $consultation;

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

        $this->labUser = User::where('role', 'laboratory')->first()
            ?? User::create([
                'name' => 'Lab MedTech Tester',
                'email' => 'lab_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'laboratory',
            ]);

        $this->radUser = User::where('role', 'radiology')->first()
            ?? User::create([
                'name' => 'Rad Tech Tester',
                'email' => 'rad_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'radiology',
            ]);

        $this->doctor = User::where('role', 'regular_doctor')->first()
            ?? User::create([
                'name' => 'Dr. Clinic Examiner',
                'email' => 'dr_exam_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'regular_doctor',
            ]);

        $this->patient = Patient::create([
            'patient_id' => 'PT-TST-' . rand(1000, 9999),
            'first_name' => 'Elena',
            'last_name' => 'Reyes',
            'dob' => '1990-05-15',
            'address' => 'Poblacion, Silang, Cavite',
            'contact_number' => '09181234567',
            'sex' => 'Female',
        ]);

        $this->consultation = Consultation::create([
            'queue_number' => 'QN-' . rand(100, 999),
            'patient_id' => $this->patient->patient_id,
            'doctor_id' => $this->doctor->id,
            'consultation_date' => now(),
            'status' => 'In Progress',
            'chief_complaint' => 'Routine checkup and labs',
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_laboratory_dashboard_renders_with_unified_hero_and_metrics()
    {
        // Create an active lab order
        AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'test_name' => 'Complete Blood Count (CBC)',
            'type' => 'Laboratory',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->labUser)->get(route('lab.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Laboratory Diagnostic Station');
        $response->assertSee('Complete Blood Count (CBC)');
        $response->assertSee('Active Queue');
        $response->assertSee('Real-Time Dashboard');
    }

    public function test_radiology_dashboard_renders_with_radiology_branding()
    {
        // Create an active radiology order
        AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'test_name' => 'Chest X-Ray PA',
            'type' => 'Radiology',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->radUser)->get(route('lab.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Radiology Diagnostic Station');
        $response->assertSee('Chest X-Ray PA');
        $response->assertSee('Awaiting Scan');
    }

    public function test_completed_laboratory_results_render_with_shared_readonly_template()
    {
        $labRequest = AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'test_name' => 'Complete Blood Count (CBC)',
            'type' => 'Laboratory',
            'status' => 'Done',
            'performed_by' => $this->labUser->id,
            'completed_at' => now(),
            'result_data' => [
                'wbc' => '7.20',
                'rbc' => '4.80',
                'hemoglobin' => '138.0',
                'hematocrit' => '41.5',
                'neutrophil' => '62.0',
                'lymphocyte' => '30.0',
                'monocyte' => '5.0',
                'eosinophil' => '2.0',
                'basophil' => '1.0',
                'band_cells' => '0.0',
                'remarks' => 'Normal blood count.',
            ],
        ]);

        $response = $this->actingAs($this->labUser)->get(route('lab.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Verified Result');
        $response->assertSee('Complete Blood Count (CBC) with Differential');
        $response->assertSee('Verified Hematology Report');
        // Check for readonly rendered values
        $response->assertSee('7.20');
        $response->assertSee('138.0');
        $response->assertSee('Normal blood count.');
        // Ensure amend shortcut is available
        $response->assertSee('Amend Results');
    }

    public function test_completed_radiology_results_render_with_shared_readonly_template()
    {
        $radRequest = AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'test_name' => 'Chest X-Ray (PA View)',
            'type' => 'Radiology',
            'status' => 'Done',
            'performed_by' => $this->radUser->id,
            'completed_at' => now(),
            'result_data' => [
                'exam_view' => 'Chest PA (Standard)',
                'findings' => 'Both lung fields are clear. Costophrenic sulci sharp. Heart size within normal limits.',
                'impression' => 'NORMAL CHEST RADIOGRAPH',
                'remarks' => 'Routine screening film.',
            ],
        ]);

        $response = $this->actingAs($this->radUser)->get(route('lab.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Radiology / Imaging Report');
        $response->assertSee('Verified Imaging Record');
        $response->assertSee('Chest PA (Standard)');
        $response->assertSee('Both lung fields are clear');
        $response->assertSee('NORMAL CHEST RADIOGRAPH');
        $response->assertSee('Routine screening film.');
    }

    public function test_laboratory_can_amend_result_and_view_retains_audit_comparison()
    {
        $labRequest = AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'test_name' => 'Complete Blood Count (CBC)',
            'type' => 'Laboratory',
            'status' => 'Done',
            'performed_by' => $this->labUser->id,
            'completed_at' => now(),
            'result_data' => [
                'wbc' => '6.00',
                'platelet_count' => '140',
                'remarks' => 'Initial readout.',
            ],
        ]);

        // Submit amendment
        $amendResponse = $this->actingAs($this->labUser)->post(
            route('lab.ancillary.amend', $labRequest->id),
            [
                'amendment_reason' => 'Platelet rerun confirmed count is 220 instead of 140.',
                'results' => [
                    'wbc' => '6.00',
                    'platelet_count' => '220',
                    'remarks' => 'Sample rerun confirmed normal platelets.',
                ],
            ]
        );

        $amendResponse->assertSessionHas('success');

        $labRequest->refresh();
        $this->assertTrue((bool)$labRequest->is_amended);
        $this->assertEquals('Platelet rerun confirmed count is 220 instead of 140.', $labRequest->amendment_reason);
        $this->assertEquals('140', $labRequest->previous_result_data['platelet_count']);
        $this->assertEquals('220', $labRequest->result_data['platelet_count']);

        // Check dashboard view of amended result
        $viewResponse = $this->actingAs($this->labUser)->get(route('lab.dashboard'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Officially Amended Result');
        $viewResponse->assertSee('Platelet rerun confirmed count is 220 instead of 140.');
        $viewResponse->assertSee('View Prior Unamended Values (Audit)');
        $viewResponse->assertSee('220');
    }
}
