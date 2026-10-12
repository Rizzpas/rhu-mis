<?php

namespace Tests\Feature;

use App\Console\Commands\SyncScheduleStatus;
use App\Http\Controllers\PublicController;
use App\Models\PractitionerSchedule;
use App\Models\User;
use App\Services\StaffPresence;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DoctorSchedulePresenceTest extends TestCase
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
        Carbon::setTestNow();
        parent::tearDown();
    }

    protected function createDoctorWithSchedule(string $day = 'Mon', string $timeIn = '08:00:00', string $timeOut = '17:00:00'): User
    {
        $doctor = User::create([
            'name' => 'Dr. Test Attending Doctor',
            'email' => 'dr.test_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'regular_doctor',
            'status' => 'Online', // legacy or stored static column
        ]);

        PractitionerSchedule::create([
            'user_id' => $doctor->id,
            'day_of_week' => $day,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
        ]);

        return $doctor->fresh(['practitionerSchedules']);
    }

    /**
     * Test Case 1:
     * Schedule 8 AM–5 PM, time 7:49 PM, no override → offline, not displayed.
     */
    public function test_case_1_schedule_8am_to_5pm_at_749pm_no_override_is_offline_and_not_displayed(): void
    {
        // Monday 7:49 PM in Asia/Manila
        Carbon::setTestNow(Carbon::parse('2026-10-12 19:49:00', 'Asia/Manila'));

        $doctor = $this->createDoctorWithSchedule('Mon', '08:00:00', '17:00:00');

        $this->assertFalse(StaffPresence::isWithinSchedule($doctor));
        $this->assertFalse(StaffPresence::isOnline($doctor));
        $this->assertEquals('Offline', StaffPresence::effectiveStatus($doctor));
        $this->assertEquals('Offline', $doctor->effective_status);
        $this->assertFalse($doctor->is_present);

        // Not present in PublicController::onlineDoctors()
        $onlineDoctors = PublicController::onlineDoctors();
        $this->assertFalse($onlineDoctors->contains('id', $doctor->id));

        // Welcome page does not display the doctor
        $response = $this->get(route('welcome'));
        $response->assertOk();
        $response->assertDontSee($doctor->formatted_name);
    }

    /**
     * Test Case 2:
     * Schedule 9 AM–2 PM, time 11:00 AM, no override → online, displayed.
     */
    public function test_case_2_schedule_9am_to_2pm_at_11am_no_override_is_online_and_displayed(): void
    {
        // Monday 11:00 AM in Asia/Manila
        Carbon::setTestNow(Carbon::parse('2026-10-12 11:00:00', 'Asia/Manila'));

        $doctor = $this->createDoctorWithSchedule('Mon', '09:00:00', '14:00:00');

        $this->assertTrue(StaffPresence::isWithinSchedule($doctor));
        $this->assertTrue(StaffPresence::isOnline($doctor));
        $this->assertEquals('Online', StaffPresence::effectiveStatus($doctor));
        $this->assertEquals('Online', $doctor->effective_status);
        $this->assertTrue($doctor->is_present);

        // Included in PublicController::onlineDoctors()
        $onlineDoctors = PublicController::onlineDoctors();
        $this->assertTrue($onlineDoctors->contains('id', $doctor->id));

        // Welcome page displays the doctor
        $response = $this->get(route('welcome'));
        $response->assertOk();
        $response->assertSee($doctor->formatted_name);
        $response->assertSee('In Clinic');
    }

    /**
     * Test Case 3:
     * Same schedule (Mon & Tue 9 AM–2 PM), doctor switches offline at 1:00 PM →
     * offline at 1:00 PM, 1:30 PM, 2:00 PM and for the rest of the day,
     * then automatically online the next day at 9:00 AM.
     */
    public function test_case_3_manual_offline_override_persists_until_next_shift_starts(): void
    {
        // Setup: Mon & Tue 9 AM - 2 PM
        $doctor = User::create([
            'name' => 'Dr. Shift Test Doctor',
            'email' => 'dr.shift_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password123'),
            'role' => 'regular_doctor',
            'status' => 'Online',
        ]);
        PractitionerSchedule::create([
            'user_id' => $doctor->id,
            'day_of_week' => 'Mon',
            'time_in' => '09:00:00',
            'time_out' => '14:00:00',
        ]);
        PractitionerSchedule::create([
            'user_id' => $doctor->id,
            'day_of_week' => 'Tue',
            'time_in' => '09:00:00',
            'time_out' => '14:00:00',
        ]);
        $doctor->load('practitionerSchedules');

        // At 1:00 PM Monday, doctor switches themselves offline
        Carbon::setTestNow(Carbon::parse('2026-10-12 13:00:00', 'Asia/Manila'));
        $response = $this->actingAs($doctor)->postJson(route('profile.status.update'), [
            'status' => 'Offline',
        ]);
        $response->assertOk();
        $doctor->refresh();

        $this->assertEquals('manual_offline', $doctor->schedule_override);
        $this->assertNotNull($doctor->schedule_override_expires_at);
        // Expiry should be next shift start: Tuesday 9:00 AM
        $this->assertEquals('2026-10-13 09:00:00', $doctor->schedule_override_expires_at->format('Y-m-d H:i:s'));

        // At 1:00 PM: Offline
        $this->assertFalse(StaffPresence::isOnline($doctor));
        $this->assertEquals('Offline', $doctor->effective_status);

        // At 1:30 PM: Offline
        Carbon::setTestNow(Carbon::parse('2026-10-12 13:30:00', 'Asia/Manila'));
        $this->assertFalse(StaffPresence::isOnline($doctor));
        $this->assertEquals('Offline', $doctor->effective_status);

        // At 2:00 PM: Offline
        Carbon::setTestNow(Carbon::parse('2026-10-12 14:00:00', 'Asia/Manila'));
        $this->assertFalse(StaffPresence::isOnline($doctor));
        $this->assertEquals('Offline', $doctor->effective_status);

        // At 5:00 PM: Offline for rest of the day
        Carbon::setTestNow(Carbon::parse('2026-10-12 17:00:00', 'Asia/Manila'));
        $this->assertFalse(StaffPresence::isOnline($doctor));
        $this->assertEquals('Offline', $doctor->effective_status);

        // At 8:59:59 AM Tuesday: still Offline (shift hasn't started yet)
        Carbon::setTestNow(Carbon::parse('2026-10-13 08:59:59', 'Asia/Manila'));
        $this->assertFalse(StaffPresence::isOnline($doctor));
        $this->assertEquals('Offline', $doctor->effective_status);

        // Exactly at 9:00:00 AM Tuesday: automatically online!
        Carbon::setTestNow(Carbon::parse('2026-10-13 09:00:00', 'Asia/Manila'));
        $this->assertTrue(StaffPresence::isOnline($doctor));
        $this->assertEquals('Online', $doctor->effective_status);
        $this->assertTrue($doctor->is_present);

        // Running sync command clears expired override in database
        $this->artisan('staff:sync-schedule-status');
        $doctor->refresh();
        $this->assertNull($doctor->schedule_override);
        $this->assertEquals('Online', $doctor->status);
    }

    /**
     * Test: If doctor switches back online before the override ends, remove the override.
     */
    public function test_switching_back_online_during_schedule_clears_manual_offline_override(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-12 13:00:00', 'Asia/Manila'));
        $doctor = $this->createDoctorWithSchedule('Mon', '09:00:00', '14:00:00');

        // Switch offline at 1:00 PM
        $this->actingAs($doctor)->postJson(route('profile.status.update'), ['status' => 'Offline']);
        $doctor->refresh();
        $this->assertEquals('manual_offline', $doctor->schedule_override);

        // Switch back online at 1:15 PM (still within 9 AM - 2 PM)
        Carbon::setTestNow(Carbon::parse('2026-10-12 13:15:00', 'Asia/Manila'));
        $this->actingAs($doctor)->postJson(route('profile.status.update'), ['status' => 'Online']);
        $doctor->refresh();

        // Override is removed because schedule already makes them online
        $this->assertNull($doctor->schedule_override);
        $this->assertNull($doctor->schedule_override_expires_at);
        $this->assertTrue(StaffPresence::isOnline($doctor));
        $this->assertEquals('Online', $doctor->effective_status);
    }

    /**
     * Test Case 4:
     * Same schedule (Mon 9 AM–2 PM), time 5:00 PM, doctor opens their account → online.
     * Stays online until they switch offline or log out.
     */
    public function test_case_4_doctor_opening_account_outside_schedule_becomes_online_until_logout(): void
    {
        $doctor = $this->createDoctorWithSchedule('Mon', '09:00:00', '14:00:00');

        // At 5:00 PM Monday, doctor is outside schedule
        Carbon::setTestNow(Carbon::parse('2026-10-12 17:00:00', 'Asia/Manila'));
        $this->assertFalse(StaffPresence::isOnline($doctor));

        // Doctor logs in at 5:00 PM
        StaffPresence::handleLogin($doctor);
        $doctor->refresh();

        $this->assertEquals('manual_online', $doctor->schedule_override);
        $this->assertTrue(StaffPresence::isOnline($doctor));
        $this->assertEquals('Online', $doctor->effective_status);

        // Doctor is displayed on public schedule
        $this->assertTrue(PublicController::onlineDoctors()->contains('id', $doctor->id));

        // Doctor logs out at 5:45 PM
        Carbon::setTestNow(Carbon::parse('2026-10-12 17:45:00', 'Asia/Manila'));
        StaffPresence::handleLogout($doctor);
        $doctor->refresh();

        $this->assertNull($doctor->schedule_override);
        $this->assertFalse(StaffPresence::isOnline($doctor));
        $this->assertEquals('Offline', $doctor->effective_status);
        $this->assertFalse(PublicController::onlineDoctors()->contains('id', $doctor->id));
    }

    /**
     * Test Case 5:
     * Exactly at 8:00 AM → becomes online. Exactly at 5:00 PM → becomes offline.
     * (Start inclusive, end exclusive)
     */
    public function test_case_5_start_inclusive_and_end_exclusive_boundaries(): void
    {
        $doctor = $this->createDoctorWithSchedule('Mon', '08:00:00', '17:00:00');

        // 7:59:59 AM → Offline
        Carbon::setTestNow(Carbon::parse('2026-10-12 07:59:59', 'Asia/Manila'));
        $this->assertFalse(StaffPresence::isWithinSchedule($doctor));
        $this->assertFalse(StaffPresence::isOnline($doctor));
        $this->assertEquals('Offline', $doctor->effective_status);

        // Exactly 8:00:00 AM → Online (start inclusive)
        Carbon::setTestNow(Carbon::parse('2026-10-12 08:00:00', 'Asia/Manila'));
        $this->assertTrue(StaffPresence::isWithinSchedule($doctor));
        $this->assertTrue(StaffPresence::isOnline($doctor));
        $this->assertEquals('Online', $doctor->effective_status);

        // 4:59:59 PM → Online
        Carbon::setTestNow(Carbon::parse('2026-10-12 16:59:59', 'Asia/Manila'));
        $this->assertTrue(StaffPresence::isWithinSchedule($doctor));
        $this->assertTrue(StaffPresence::isOnline($doctor));
        $this->assertEquals('Online', $doctor->effective_status);

        // Exactly 5:00:00 PM → Offline (end exclusive)
        Carbon::setTestNow(Carbon::parse('2026-10-12 17:00:00', 'Asia/Manila'));
        $this->assertFalse(StaffPresence::isWithinSchedule($doctor));
        $this->assertFalse(StaffPresence::isOnline($doctor));
        $this->assertEquals('Offline', $doctor->effective_status);
    }

    /**
     * Test: Real-time update endpoint /check-home-updates reflects schedule transitions
     * without a full page refresh.
     */
    public function test_check_home_updates_signature_changes_when_shift_ends(): void
    {
        $doctor = $this->createDoctorWithSchedule('Mon', '08:00:00', '17:00:00');

        // 4:30 PM: Doctor is online
        Carbon::setTestNow(Carbon::parse('2026-10-12 16:30:00', 'Asia/Manila'));
        $stateAt430 = PublicController::getHomeUpdateState();
        $this->assertGreaterThan(0, $stateAt430['doctor_count']);
        $this->assertNotEmpty($stateAt430['doctor_signature']);

        // 5:00 PM: Doctor shift ends -> signature and doctor_count change
        Carbon::setTestNow(Carbon::parse('2026-10-12 17:00:00', 'Asia/Manila'));
        $stateAt500 = PublicController::getHomeUpdateState();

        $this->assertNotEquals($stateAt430['doctor_signature'], $stateAt500['doctor_signature']);
        $this->assertNotEquals($stateAt430['doctor_count'], $stateAt500['doctor_count']);

        // /check-home-updates endpoint returns updated state
        $response = $this->get(route('welcome.check-updates'));
        $response->assertOk();
        $response->assertJsonFragment([
            'doctor_signature' => $stateAt500['doctor_signature'],
            'doctor_count' => $stateAt500['doctor_count'],
        ]);
    }

    /**
     * Test: Timezone consistency. Ensure all comparisons use Asia/Manila.
     */
    public function test_timezone_consistency(): void
    {
        $this->assertEquals('Asia/Manila', config('app.timezone'));
        $this->assertEquals('Asia/Manila', StaffPresence::timezone());

        $now = StaffPresence::now();
        $this->assertEquals('Asia/Manila', $now->getTimezone()->getName());
    }
}
