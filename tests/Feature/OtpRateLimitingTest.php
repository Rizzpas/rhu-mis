<?php

namespace Tests\Feature;

use App\Http\Controllers\PublicController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OtpRateLimitingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_correct_otp_verifies_successfully()
    {
        $email = 'testuser@example.com';
        $otp = '123456';
        Cache::put('otp_' . $email, $otp, 600);

        $response = $this->postJson(route('appointment.verify-otp'), [
            'email' => $email,
            'otp' => $otp,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_incorrect_otp_tracks_remaining_attempts()
    {
        $email = 'testuser@example.com';
        Cache::put('otp_' . $email, '123456', 600);

        $response = $this->postJson(route('appointment.verify-otp'), [
            'email' => $email,
            'otp' => '999999',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'attempts_remaining' => 4,
            ]);
    }

    public function test_five_incorrect_attempts_triggers_3_minute_lockout()
    {
        $email = 'locked@example.com';
        Cache::put('otp_' . $email, '123456', 600);

        // 4 failed attempts
        for ($i = 1; $i <= 4; $i++) {
            $resp = $this->postJson(route('appointment.verify-otp'), [
                'email' => $email,
                'otp' => '00000' . $i,
            ]);
            $resp->assertStatus(400);
            $this->assertEquals(5 - $i, $resp->json('attempts_remaining'));
        }

        // 5th attempt triggers lockout
        $fifthResp = $this->postJson(route('appointment.verify-otp'), [
            'email' => $email,
            'otp' => '000005',
        ]);

        $fifthResp->assertStatus(429)
            ->assertJson(['success' => false]);
        $this->assertStringContainsString('3 minute', $fifthResp->json('message'));
        $this->assertEquals(180, $fifthResp->json('seconds_remaining'));

        // 6th attempt while locked out also rejected with 429
        $sixthResp = $this->postJson(route('appointment.verify-otp'), [
            'email' => $email,
            'otp' => '123456',
        ]);

        $sixthResp->assertStatus(429)
            ->assertJson(['success' => false]);
        $this->assertStringContainsString('3 minute', $sixthResp->json('message'));
    }

    public function test_exponential_backoff_escalates_on_repeated_lockouts()
    {
        $email = 'repeat@example.com';
        Cache::put('otp_' . $email, '123456', 600);

        // First cycle: 5 failed attempts -> 3 minute lockout (180s)
        for ($i = 1; $i <= 5; $i++) {
            $resp = $this->postJson(route('appointment.verify-otp'), [
                'email' => $email,
                'otp' => '00000' . $i,
            ]);
        }
        $resp->assertStatus(429);
        $this->assertEquals(180, $resp->json('seconds_remaining'));

        // Fast forward cache past first lockout (simulate lockout expiration)
        $rateLimitKey = sha1($email . '|127.0.0.1');
        Cache::forget('otp_verify_lockout_' . $rateLimitKey);

        // Second cycle: 5 failed attempts -> 5 minute lockout (300s)
        for ($i = 1; $i <= 5; $i++) {
            $resp2 = $this->postJson(route('appointment.verify-otp'), [
                'email' => $email,
                'otp' => '11111' . $i,
            ]);
        }
        $resp2->assertStatus(429);
        $this->assertEquals(300, $resp2->json('seconds_remaining'));
        $this->assertStringContainsString('5 minute', $resp2->json('message'));
    }

    public function test_successful_verification_clears_counter_and_lockout()
    {
        $email = 'cleartest@example.com';
        $otp = '654321';
        Cache::put('otp_' . $email, $otp, 600);

        // 2 failed attempts
        $this->postJson(route('appointment.verify-otp'), ['email' => $email, 'otp' => '000001']);
        $this->postJson(route('appointment.verify-otp'), ['email' => $email, 'otp' => '000002']);

        $rateLimitKey = sha1($email . '|127.0.0.1');
        $this->assertEquals(2, Cache::get('otp_verify_attempts_' . $rateLimitKey));

        // Successful attempt
        $successResp = $this->postJson(route('appointment.verify-otp'), [
            'email' => $email,
            'otp' => $otp,
        ]);
        $successResp->assertStatus(200);

        // Cache should be clean
        $this->assertNull(Cache::get('otp_verify_attempts_' . $rateLimitKey));
        $this->assertNull(Cache::get('otp_verify_lockout_' . $rateLimitKey));
        $this->assertNull(Cache::get('otp_verify_tier_' . $rateLimitKey));
    }
}
