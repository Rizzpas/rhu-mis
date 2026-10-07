<?php

namespace Tests\Feature;

use App\Models\AncillaryRequest;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecureDiagnosticFileAccessTest extends TestCase
{
    protected User $doctor;
    protected User $nurse;
    protected User $labTech;
    protected User $admin;
    protected User $superAdmin;
    protected Consultation $consultation;
    protected AncillaryRequest $ancillary;

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

        $this->doctor = User::create([
            'name' => 'Dr. Test Attending',
            'email' => 'doc_diag_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'regular_doctor',
            'status' => 'Online',
        ]);

        $this->nurse = User::create([
            'name' => 'Nurse Test Clinician',
            'email' => 'nurse_diag_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'clinical_nurse',
            'status' => 'Online',
        ]);

        $this->labTech = User::create([
            'name' => 'MedTech Diagnostic',
            'email' => 'lab_diag_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'laboratory',
            'status' => 'Online',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Diagnostic',
            'email' => 'admin_diag_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'Online',
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin Diagnostic',
            'email' => 'sadmin_diag_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'status' => 'Online',
        ]);

        $patient = Patient::create([
            'first_name' => 'John',
            'last_name' => 'Diagnostic',
            'dob' => '1995-05-15',
            'sex' => 'Male',
            'blood_type' => 'O+',
            'address' => 'Silang, Cavite',
            'classification' => 'Regular Adult',
            'mothers_maiden_name' => 'Santos',
            'philhealth_number' => '12-123456789-1',
            'contact_number' => '09171234567',
        ]);

        $this->consultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'doctor_id' => $this->doctor->id,
            'consultation_date' => now()->toDateString(),
            'queue_number' => 'REG-999',
            'status' => 'active',
        ]);

        $this->ancillary = AncillaryRequest::create([
            'consultation_id' => $this->consultation->id,
            'type' => 'Laboratory',
            'test_name' => 'Complete Blood Count (CBC)',
            'status' => 'Pending',
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_unauthenticated_user_cannot_access_diagnostic_file(): void
    {
        // Unauthenticated web access returns 404 per RHU security policy to prevent route discovery
        $response = $this->get(route('ancillary.file', $this->ancillary));
        $response->assertStatus(404);

        // API / JSON access explicitly returns 401 Unauthenticated
        $jsonResponse = $this->getJson(route('ancillary.file', $this->ancillary));
        $jsonResponse->assertStatus(401);
    }

    public function test_unauthorized_user_role_is_forbidden(): void
    {
        $ordinaryUser = User::create([
            'name' => 'Ordinary Patient User',
            'email' => 'ordinary_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'ordinary_user',
            'status' => 'Online',
        ]);

        Storage::fake('local');
        $filePath = 'ancillary_results/forbidden_scan_' . uniqid() . '.png';
        Storage::disk('local')->put($filePath, 'fake content');
        $this->ancillary->update(['status' => 'Done', 'result_file_path' => $filePath]);

        $response = $this->actingAs($ordinaryUser)->get(route('ancillary.file', $this->ancillary));
        $response->assertStatus(403);
    }

    public function test_diagnostic_upload_is_stored_on_local_private_disk_not_public(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        // PNG 1x1 image fixture
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $file = UploadedFile::fake()->createWithContent('blood_result.png', $pngContent);

        $response = $this->actingAs($this->labTech)->post(route('lab.ancillary.complete', $this->ancillary), [
            'results' => ['wbc' => '7.5', 'rbc' => '4.8', 'hemoglobin' => '14.2'],
            'result_file' => $file,
        ]);

        $response->assertRedirect();
        $this->ancillary->refresh();

        $this->assertNotNull($this->ancillary->result_file_path);
        // File must exist on local private disk
        $this->assertTrue(Storage::disk('local')->exists($this->ancillary->result_file_path));
        // File must NOT be saved on public disk
        $this->assertFalse(Storage::disk('public')->exists($this->ancillary->result_file_path));
    }

    public function test_authorized_clinician_can_stream_diagnostic_file(): void
    {
        Storage::fake('local');

        $filePath = 'ancillary_results/test_scan_' . uniqid() . '.png';
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        Storage::disk('local')->put($filePath, $pngContent);

        $this->ancillary->update([
            'status' => 'Done',
            'result_file_path' => $filePath,
        ]);

        // Doctor can access
        $doctorResponse = $this->actingAs($this->doctor)->get(route('ancillary.file', $this->ancillary));
        $doctorResponse->assertStatus(200);
        $doctorResponse->assertHeader('Content-Type', 'image/png');
        $this->assertStringContainsString('private', (string) $doctorResponse->headers->get('Cache-Control'));

        // Nurse can access
        $nurseResponse = $this->actingAs($this->nurse)->get(route('ancillary.file', $this->ancillary));
        $nurseResponse->assertStatus(200);

        // Lab Tech can access
        $labResponse = $this->actingAs($this->labTech)->get(route('ancillary.file', $this->ancillary));
        $labResponse->assertStatus(200);

        // Super Admin can access
        $sAdminResponse = $this->actingAs($this->superAdmin)->get(route('ancillary.file', $this->ancillary));
        $sAdminResponse->assertStatus(200);
    }

    public function test_legacy_public_file_is_streamed_with_fallback(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $filePath = 'ancillary_results/legacy_scan_' . uniqid() . '.png';
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        Storage::disk('public')->put($filePath, $pngContent);

        $this->ancillary->update([
            'status' => 'Done',
            'result_file_path' => $filePath,
        ]);

        $response = $this->actingAs($this->doctor)->get(route('ancillary.file', $this->ancillary));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_missing_diagnostic_file_returns_404(): void
    {
        $response = $this->actingAs($this->doctor)->get(route('ancillary.file', $this->ancillary));
        $response->assertStatus(404);
    }
}
