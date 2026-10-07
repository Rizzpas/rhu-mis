<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Conversation;
use App\Models\FacilityUnit;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Message;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SystemAnalysisImplementationTest extends TestCase
{
    protected User $superAdmin;
    protected User $admin;

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
                'name' => 'Super Administrator',
                'email' => 'super_admin_sys_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('Password123!'),
                'role' => 'super_admin',
                'status' => 'Online',
            ]);

        $this->admin = User::where('role', 'admin')->first()
            ?? User::create([
                'name' => 'Regular Admin',
                'email' => 'admin_sys_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('Password123!'),
                'role' => 'admin',
                'status' => 'Online',
            ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_batches_expire_command_transitions_expired_active_batches(): void
    {
        $medicine = Medicine::create([
            'name' => 'Amoxicillin 500mg Test',
            'generic_name' => 'Amoxicillin',
            'form' => 'Capsule',
        ]);

        // Active batch past its expiration date
        $expiredBatch = MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => 'BATCH-PAST-' . uniqid(),
            'expiration_date' => now()->subDays(5)->format('Y-m-d'),
            'quantity' => 50,
            'original_quantity' => 100,
            'status' => 'active',
        ]);

        // Active unexpired batch
        $validBatch = MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => 'BATCH-FUTURE-' . uniqid(),
            'expiration_date' => now()->addDays(30)->format('Y-m-d'),
            'quantity' => 100,
            'original_quantity' => 100,
            'status' => 'active',
        ]);

        $exitCode = Artisan::call('batches:expire');
        $this->assertEquals(0, $exitCode);

        $expiredBatch->refresh();
        $validBatch->refresh();

        $this->assertEquals('expired', $expiredBatch->status);
        $this->assertEquals('active', $validBatch->status);

        // Check inventory log was recorded
        $this->assertDatabaseHas('inventory_logs', [
            'medicine_id' => $medicine->id,
            'batch_id' => $expiredBatch->id,
            'action' => 'Expired',
            'quantity_changed' => -50,
        ]);
    }

    public function test_consultations_close_stale_command_auto_cancels_previous_day_queued(): void
    {
        $patient = Patient::create([
            'patient_id' => 'RHU-' . date('y') . '-' . rand(10000, 99999),
            'first_name' => 'Pedro',
            'last_name' => 'Penduko',
            'dob' => '1995-05-15',
            'sex' => 'Male',
            'contact_number' => '09123456789',
            'address' => 'Poblacion',
            'city_province' => 'Silang, Cavite',
        ]);

        // Stale consultation queued yesterday
        $staleConsultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'queue_number' => 'Q-998',
            'status' => 'queued',
            'consultation_date' => now()->subDay()->toDateString(),
            'created_at' => now()->subDay(),
        ]);

        // Today's queued consultation (should NOT be cancelled)
        $todaysConsultation = Consultation::create([
            'patient_id' => $patient->patient_id,
            'queue_number' => 'Q-999',
            'status' => 'queued',
            'consultation_date' => now()->toDateString(),
            'created_at' => now(),
        ]);

        $exitCode = Artisan::call('consultations:close-stale');
        $this->assertEquals(0, $exitCode);

        $staleConsultation->refresh();
        $todaysConsultation->refresh();

        $this->assertEquals('cancelled', $staleConsultation->status);
        $this->assertEquals('queued', $todaysConsultation->status);
        $this->assertStringContainsStringIgnoringCase('auto-cancelled', $staleConsultation->medical_notes);
    }

    public function test_store_appointment_rejects_non_main_health_center_facility(): void
    {
        $otherFacility = FacilityUnit::create([
            'name' => 'Barangay Biga Health Station ' . uniqid(),
            'slug' => 'bhs-biga-' . uniqid(),
            'is_active' => true,
        ]);

        $payload = [
            'first_name' => 'Maria',
            'last_name' => 'Clara',
            'dob' => '2000-01-01',
            'sex' => 'Female',
            'email' => 'maria.clara.test_' . uniqid() . '@example.com',
            'contact_number' => '09171234567',
            'type' => 'adult',
            'preferred_date' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'complaint' => 'Fever and cough',
            'data_privacy_agreed' => '1',
            'facility_id' => $otherFacility->id,
        ];

        $response = $this->post(route('appointment.store'), $payload);

        $response->assertSessionHasErrors(['facility_id']);
    }

    public function test_split_admin_controllers_respond_properly_to_all_domains(): void
    {
        // 1. Dashboard (AdminController)
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertViewIs('admin.dashboard');

        // 2. Analytics (AnalyticsController)
        $this->actingAs($this->admin)
            ->get(route('admin.analytics'))
            ->assertStatus(200)
            ->assertViewIs('admin.analytics.index');

        // 3. Announcements (AnnouncementController)
        $this->actingAs($this->admin)
            ->get(route('admin.announcements.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.announcements.index');

        // 4. Staff (StaffController)
        $this->actingAs($this->admin)
            ->get(route('admin.staff.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.staff.index');

        // 5. Retention (RetentionController)
        $this->actingAs($this->admin)
            ->get(route('admin.retention.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.retention.index');

        // 6. Patient Records (PatientRecordController)
        $this->actingAs($this->admin)
            ->get(route('admin.patients.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.patients.index');

        // 7. Audit Logs (AuditLogController - Super Admin gate)
        $this->actingAs($this->superAdmin)
            ->get(route('admin.audit.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.audit.index');

        // 8. Content (ContentController)
        $this->actingAs($this->admin)
            ->get(route('admin.content.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.content.index');
    }

    public function test_message_model_strips_html_tags_to_prevent_xss(): void
    {
        $conversation = Conversation::create();

        $message = new Message([
            'body' => '<script>alert("xss")</script><b>Hello Doctor</b>',
        ]);
        $message->conversation_id = $conversation->id;
        $message->sender_id = $this->admin->id;
        $message->type = 'text';
        $message->save();

        $this->assertEquals('alert("xss")Hello Doctor', $message->body);
        $this->assertStringNotContainsString('<script>', $message->body);
        $this->assertStringNotContainsString('<b>', $message->body);
    }
}
