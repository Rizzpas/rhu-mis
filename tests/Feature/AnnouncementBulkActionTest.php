<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AnnouncementBulkActionTest extends TestCase
{
    protected User $superAdmin;
    protected User $regularAdmin;

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
                'email' => 'super_admin_bulk_' . uniqid() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'super_admin',
                'status' => 'Online',
            ]);

        $this->regularAdmin = User::where('role', 'admin')->first()
            ?? User::create([
                'name' => 'Admin Test',
                'email' => 'admin_bulk_' . uniqid() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'status' => 'Online',
            ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_super_admin_can_bulk_delete_two_or_more_announcements(): void
    {
        $a1 = Announcement::create([
            'title' => 'Bulk Delete 1 ' . uniqid(),
            'content' => 'Content 1',
            'author_id' => $this->superAdmin->id,
            'status' => 'published',
        ]);
        $a2 = Announcement::create([
            'title' => 'Bulk Delete 2 ' . uniqid(),
            'content' => 'Content 2',
            'author_id' => $this->superAdmin->id,
            'status' => 'draft',
        ]);
        $a3 = Announcement::create([
            'title' => 'Bulk Delete 3 ' . uniqid(),
            'content' => 'Content 3',
            'author_id' => $this->superAdmin->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.announcements.bulk-delete'), [
            'ids' => [$a1->id, $a2->id, $a3->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertNotNull($a1->fresh()->deleted_at);
        $this->assertNotNull($a2->fresh()->deleted_at);
        $this->assertNotNull($a3->fresh()->deleted_at);
    }

    public function test_regular_admin_is_forbidden_from_bulk_deleting_announcements(): void
    {
        $a1 = Announcement::create([
            'title' => 'Admin Delete 1 ' . uniqid(),
            'content' => 'Content 1',
            'author_id' => $this->regularAdmin->id,
            'status' => 'published',
        ]);
        $a2 = Announcement::create([
            'title' => 'Admin Delete 2 ' . uniqid(),
            'content' => 'Content 2',
            'author_id' => $this->regularAdmin->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->regularAdmin)->delete(route('admin.announcements.bulk-delete'), [
            'ids' => [$a1->id, $a2->id],
        ]);

        $response->assertStatus(403);
        $this->assertNull($a1->fresh()->deleted_at);
        $this->assertNull($a2->fresh()->deleted_at);
    }

    public function test_super_admin_can_bulk_change_status_of_announcements(): void
    {
        $a1 = Announcement::create([
            'title' => 'Status Test 1 ' . uniqid(),
            'content' => 'Content 1',
            'author_id' => $this->superAdmin->id,
            'status' => 'draft',
        ]);
        $a2 = Announcement::create([
            'title' => 'Status Test 2 ' . uniqid(),
            'content' => 'Content 2',
            'author_id' => $this->superAdmin->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.announcements.bulk-status'), [
            'ids' => [$a1->id, $a2->id],
            'status' => 'published',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('published', $a1->fresh()->status);
        $this->assertEquals('published', $a2->fresh()->status);
    }

    public function test_regular_admin_can_bulk_change_status_to_published(): void
    {
        $a1 = Announcement::create([
            'title' => 'Reg Admin Status 1 ' . uniqid(),
            'content' => 'Content 1',
            'author_id' => $this->regularAdmin->id,
            'status' => 'draft',
        ]);
        $a2 = Announcement::create([
            'title' => 'Reg Admin Status 2 ' . uniqid(),
            'content' => 'Content 2',
            'author_id' => $this->regularAdmin->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->regularAdmin)->post(route('admin.announcements.bulk-status'), [
            'ids' => [$a1->id, $a2->id],
            'status' => 'published',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('published', $a1->fresh()->status);
        $this->assertEquals('published', $a2->fresh()->status);
    }

    public function test_bulk_status_requires_valid_status_and_ids(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.announcements.bulk-status'), [
            'ids' => [],
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['ids', 'status']);
    }
}
