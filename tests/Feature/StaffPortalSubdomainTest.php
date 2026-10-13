<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StaffPortalSubdomainTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Seed site_settings cache so views render without needing live database
        Cache::put('site_settings_all', [], 300);
    }

    /**
     * Test public portal renders clean footer without disguised staff portal login link.
     */
    public function test_public_page_renders_clean_footer_without_disguised_link(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertDontSee('href="' . route('staff.access') . '"', false);
    }

    /**
     * Test public /staff and /staff-access redirect to staff subdomain when configured.
     */
    public function test_public_staff_redirects_to_subdomain_when_configured(): void
    {
        config(['app.staff_domain' => 'staff.example.com']);

        $response = $this->get('/staff');
        $response->assertRedirect('http://staff.example.com');

        $response2 = $this->get('/staff-access');
        $response2->assertRedirect('http://staff.example.com');
    }

    /**
     * Test direct login page renders without requiring hidden staff_portal_token.
     */
    public function test_login_page_renders_directly_without_session_token(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign In to your account');
        $response->assertSee('Email Address');
        $response->assertSee('Password');
    }

    /**
     * Test forgot password page renders directly without hidden session token.
     */
    public function test_forgot_password_page_renders_directly(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Forgot Password');
    }
}
