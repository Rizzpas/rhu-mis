<?php

namespace Tests\Feature;

use App\Models\User;
use App\Rules\SecureImage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecureImageUploadTest extends TestCase
{
    protected $connection = 'mysql';

    protected const VALID_PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    protected const VALID_JPEG_BASE64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';
    protected const VALID_WEBP_BASE64 = 'UklGRkAAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAARBxAR/Q9ERP8DAABWUDggGAAAADABAJ0BKgEAAQABAAAAbwAA/v60AAAA';
    protected const VALID_GIF_BASE64 = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'rhu-mis',
        ]);
        \Illuminate\Support\Facades\DB::purge('mysql');
        \Illuminate\Support\Facades\DB::reconnect('mysql');
        \Illuminate\Support\Facades\DB::beginTransaction();

        $this->user = User::create([
            'name' => 'Temp Upload User',
            'email' => 'temp_upload_user_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'patient',
        ]);

        $this->admin = User::where('role', 'super_admin')->first()
            ?? User::where('role', 'admin')->first()
            ?? User::create([
                'name' => 'Admin User',
                'email' => 'admin_user_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Facades\DB::rollBack();
        if (isset($this->user)) {
            $this->user->delete();
        }
        parent::tearDown();
    }

    public function test_profile_avatar_upload_with_rejected_file_type_fails_validation(): void
    {
        $file = UploadedFile::fake()->createWithContent('avatar.gif', base64_decode(self::VALID_GIF_BASE64));

        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'Updated Name',
                'avatar' => $file,
            ]);

        $response->assertSessionHasErrors(['avatar' => SecureImage::errorMessage()]);
    }

    public function test_api_request_with_rejected_file_returns_http_415_with_exact_message(): void
    {
        $file = UploadedFile::fake()->createWithContent('malicious.php.jpg', base64_decode(self::VALID_JPEG_BASE64));

        $response = $this->actingAs($this->user)
            ->patchJson(route('profile.update'), [
                'name' => 'Updated Name',
                'avatar' => $file,
            ]);

        $response->assertStatus(415);
        $response->assertJson([
            'message' => 'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
        ]);
        $response->assertJsonValidationErrors(['avatar']);
    }

    public function test_api_request_with_pdf_returns_http_415(): void
    {
        $file = UploadedFile::fake()->createWithContent('document.pdf', "%PDF-1.4\n1 0 obj<<>>endobj");

        $response = $this->actingAs($this->user)
            ->patchJson(route('profile.update'), [
                'name' => 'Updated Name',
                'avatar' => $file,
            ]);

        $response->assertStatus(415);
        $response->assertJson([
            'message' => 'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
        ]);
    }

    public function test_profile_avatar_upload_with_valid_image_succeeds(): void
    {
        Storage::fake('uploads');

        $file = UploadedFile::fake()->createWithContent('photo.png', base64_decode(self::VALID_PNG_BASE64));

        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'Updated Name',
                'avatar' => $file,
            ]);

        $response->assertSessionDoesntHaveErrors(['avatar']);
    }

    public function test_profile_avatar_upload_with_valid_webp_image_succeeds(): void
    {
        Storage::fake('uploads');

        $file = UploadedFile::fake()->createWithContent('avatar.webp', base64_decode(self::VALID_WEBP_BASE64));

        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'Updated Name',
                'avatar' => $file,
            ]);

        $response->assertSessionDoesntHaveErrors(['avatar']);
    }

    public function test_rejected_files_are_never_stored_in_filesystem(): void
    {
        Storage::fake('uploads');

        $file = UploadedFile::fake()->createWithContent('bad.svg', '<svg></svg>');

        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'Updated Name',
                'avatar' => $file,
            ]);

        $response->assertSessionHasErrors(['avatar']);

        // Assert nothing was stored
        $this->assertEmpty(Storage::disk('uploads')->allFiles());
    }

    public function test_admin_announcement_upload_with_invalid_image_fails_validation(): void
    {
        $file = UploadedFile::fake()->createWithContent('banner.gif', base64_decode(self::VALID_GIF_BASE64));

        $response = $this->actingAs($this->admin)
            ->post(route('admin.announcements.store'), [
                'title' => 'Test Announcement',
                'content' => 'Some content here',
                'category' => 'General',
                'images' => [$file],
            ]);

        $response->assertSessionHasErrors(['images.0' => SecureImage::errorMessage()]);
    }

    public function test_admin_facility_unit_upload_with_invalid_image_fails_validation(): void
    {
        $file = UploadedFile::fake()->createWithContent('facility.bmp', base64_decode('Qk06AAAAAAAAADYAAAAoAAAAAQAAAAEAAAABACAAAAAAAAYAAAASCwAAEgsAAAAAAAAAAAAA'));

        $response = $this->actingAs($this->admin)
            ->post(route('admin.facilities.store'), [
                'name' => 'Test Facility',
                'description' => 'Test facility description',
                'location' => 'Building A',
                'operating_hours' => '8AM - 5PM',
                'contact_number' => '09123456789',
                'image' => $file,
            ]);

        $response->assertSessionHasErrors(['image' => SecureImage::errorMessage()]);
    }
}
