<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsContentManagementTest extends TestCase
{
    protected const VALID_PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    protected User $admin;
    protected User $superAdmin;
    protected User $patient;

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
                'name' => 'Super Admin Test',
                'email' => 'superadmin_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
            ]);

        $this->admin = User::where('role', 'admin')->first()
            ?? User::create([
                'name' => 'Admin Test',
                'email' => 'admin_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);

        $this->patient = User::where('role', 'patient')->first()
            ?? User::create([
                'name' => 'Patient Test',
                'email' => 'patient_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'patient',
            ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_guest_is_hidden_from_cms_content_index_with_404(): void
    {
        $response = $this->get(route('admin.content.index'));
        $response->assertStatus(404);
    }

    public function test_unauthorized_user_is_redirected_with_warning(): void
    {
        $response = $this->actingAs($this->patient)->get(route('admin.content.index'));
        $response->assertRedirect(route('welcome'));
        $response->assertSessionHas('warning');
    }

    public function test_admin_and_super_admin_can_access_cms_content_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.content.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.content.index');
        $response->assertViewHasAll(['settings', 'facilityUnits', 'systemStaff']);

        $responseSuper = $this->actingAs($this->superAdmin)->get(route('admin.content.index'));
        $responseSuper->assertStatus(200);
    }

    public function test_admin_can_update_general_landing_content_settings(): void
    {
        $payload = [
            'settings' => [
                'hero_badge_text' => 'Updated Municipal Health System',
                'hero_title_line1' => 'Modern Care',
                'hero_title_highlight' => 'For All Families',
                'hero_title_line2' => 'In Silang',
                'hero_description' => 'Tested description for landing page.',
                'footer_email' => 'test_health@silang.gov.ph',
                'footer_phone' => '046-123-4567',
                'clinic_hours' => 'Mon-Fri 8:00 AM - 5:00 PM',
                'emergency_hotlines' => '911 / (046) 414-0000',
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.content.update'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('Updated Municipal Health System', SiteSetting::get('hero_badge_text'));
        $this->assertEquals('test_health@silang.gov.ph', SiteSetting::get('footer_email'));
        $this->assertEquals('046-123-4567', SiteSetting::get('footer_phone'));
    }

    public function test_admin_can_update_organization_structure_settings(): void
    {
        $payload = [
            'settings' => [
                'org_kicker' => 'Test Leadership & Governance',
                'org_title' => 'Test Organizational Structure',
                'org_subtitle' => 'Dedicated healthcare professionals of Silang.',
                'mho_badge' => 'Executive Officer',
                'mho_subbadge' => 'Head of Agency',
                'mho_name' => 'Dr. Test MHO, MD',
                'mho_title' => 'Municipal Health Officer',
                'mho_oversight_title' => 'Oversight:',
                'mho_oversight_desc' => 'Comprehensive public health governance.',
            ],
            'org_medical_officers' => [
                ['name' => 'Dr. Juan Dela Cruz, MD', 'role' => 'Specialist I', 'initials' => ''],
                ['name' => 'Dr. Maria Santos, MD', 'role' => 'Medical Officer III', 'initials' => 'MS'],
            ],
            'org_divisions' => [
                [
                    'id' => 'primary',
                    'number' => '1',
                    'title' => 'Primary Health Division',
                    'subtitle' => 'Disease Surveillance & NIP',
                    'accent' => 'emerald',
                    'units' => [
                        [
                            'title' => 'Vaccination Unit',
                            'category' => 'Immunization',
                            'lead_name' => 'Nurse Lead, RN',
                            'lead_role' => 'Nurse II',
                            'members' => ['Staff A', 'Staff B'],
                        ],
                    ],
                ],
                [
                    'id' => 'maternal',
                    'number' => '2',
                    'title' => 'Maternal & Child Health',
                    'subtitle' => 'BEmONC Birthing & Midwifery',
                    'accent' => 'teal',
                    'units' => [],
                ],
            ],
            'org_midwives' => [
                ['name' => 'Midwife Ana, RM', 'rank' => 'Midwife III'],
                ['name' => 'Midwife Bea, RM', 'rank' => 'Midwife II'],
            ],
            'org_admins' => [
                ['name' => 'Admin Officer Pedro', 'role' => 'Administrative Officer', 'initials' => ''],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.content.update'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('Test Leadership & Governance', SiteSetting::get('org_kicker'));
        $this->assertEquals('Dr. Test MHO, MD', SiteSetting::get('mho_name'));

        // Check medical officers & initials auto-generation
        $officers = SiteSetting::getJson('org_medical_officers');
        $this->assertCount(2, $officers);
        $this->assertEquals('JD', $officers[0]['initials']);
        $this->assertEquals('MS', $officers[1]['initials']);

        // Check divisions & auto-calculated badges
        $divisions = SiteSetting::getJson('org_divisions');
        $this->assertCount(2, $divisions);
        // Primary division: 1 lead + 2 members = 3 staff
        $this->assertEquals('3 Staff', $divisions[0]['badge']);
        // Maternal division: 0 unit staff + 2 midwives = 2 staff
        $this->assertEquals('2 Staff', $divisions[1]['badge']);

        // Check midwives
        $midwives = SiteSetting::getJson('org_midwives');
        $this->assertCount(2, $midwives);
        $this->assertEquals('Midwife Ana, RM', $midwives[0]['name']);

        // Check admins & initials auto-generation
        $admins = SiteSetting::getJson('org_admins');
        $this->assertCount(1, $admins);
        $this->assertEquals('AO', $admins[0]['initials']);
    }

    public function test_admin_can_upload_and_remove_mho_image(): void
    {
        Storage::fake('uploads');

        $tmpFile = tempnam(sys_get_temp_dir(), 'test_mho_') . '.png';
        file_put_contents($tmpFile, base64_decode(self::VALID_PNG_BASE64));

        $uploadedFile = new UploadedFile(
            $tmpFile,
            'mho.png',
            'image/png',
            null,
            true
        );

        $payload = [
            'settings' => [
                'org_title' => 'Silang RHU Org Chart',
            ],
            'mho_image_file' => $uploadedFile,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.content.update'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $storedPath = SiteSetting::get('mho_image');
        $this->assertNotEmpty($storedPath);
        $this->assertStringStartsWith('uploads/content/mho/', $storedPath);

        // Test removing the MHO image
        $removePayload = [
            'settings' => [
                'org_title' => 'Silang RHU Org Chart',
            ],
            'remove_mho_image' => '1',
        ];

        $removeResponse = $this->actingAs($this->admin)->put(route('admin.content.update'), $removePayload);
        $removeResponse->assertRedirect();
        $removeResponse->assertSessionHas('success');

        $this->assertEquals('', SiteSetting::get('mho_image'));
        if (file_exists($tmpFile)) {
            @unlink($tmpFile);
        }
    }

    public function test_public_about_page_renders_with_org_chart_settings(): void
    {
        SiteSetting::set('org_title', 'Silang Public Health Leadership', 'organization');
        SiteSetting::set('mho_name', 'Dr. Sample Jericho, MD', 'organization');

        $response = $this->get(route('about'));
        $response->assertStatus(200);
        $response->assertSee('Silang Public Health Leadership');
        $response->assertSee('Dr. Sample Jericho, MD');
    }

    public function test_content_update_validation_rejects_invalid_email_and_invalid_images(): void
    {
        // Invalid email
        $payload = [
            'settings' => [
                'footer_email' => 'invalid-not-an-email',
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.content.update'), $payload);
        $response->assertSessionHasErrors(['settings.footer_email']);

        // Invalid file upload (e.g., text file disguised as image)
        $fakeTxt = UploadedFile::fake()->create('malicious.php', 10, 'text/plain');
        $filePayload = [
            'settings' => [
                'org_title' => 'Title',
            ],
            'mho_image_file' => $fakeTxt,
        ];

        $fileResponse = $this->actingAs($this->admin)->put(route('admin.content.update'), $filePayload);
        $fileResponse->assertSessionHasErrors(['mho_image_file']);
    }

    public function test_site_setting_get_works_even_if_get_group_was_called_first(): void
    {
        SiteSetting::set('org_kicker', 'Leadership & Governance', 'organization');
        SiteSetting::clearCache();

        // Call getGroup first
        SiteSetting::getGroup('organization');

        // Now call get
        $val = SiteSetting::get('org_kicker');
        $this->assertEquals('Leadership & Governance', $val);
    }
}
