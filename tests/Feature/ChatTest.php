<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use DatabaseTransactions;

    protected $connection = 'mysql';

    protected User $userA;
    protected User $userB;
    protected User $userC;

    protected function setUp(): void
    {
        parent::setUp();

        // Use existing seeded staff users or create temporary ones in transaction
        $this->userA = User::where('role', 'super_admin')->first()
            ?? User::create(['name' => 'Admin User', 'email' => 'admin_test@rhu.gov.ph', 'password' => bcrypt('password'), 'role' => 'admin']);

        $this->userB = User::where('role', 'admin')->where('id', '!=', $this->userA->id)->first()
            ?? User::create(['name' => 'Doctor User', 'email' => 'doctor_test@rhu.gov.ph', 'password' => bcrypt('password'), 'role' => 'doctor']);

        $this->userC = User::create([
            'name' => 'Nurse User Test',
            'email' => 'nurse_temp_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'nurse',
        ]);
    }

    public function test_guest_cannot_access_chat_endpoints(): void
    {
        $this->getJson('/chat/conversations')->assertStatus(401);
        $this->getJson('/chat/users')->assertStatus(401);
        $this->getJson('/chat/unread-count')->assertStatus(401);
    }

    public function test_user_can_list_available_staff(): void
    {
        $response = $this->actingAs($this->userA)->getJson('/chat/users');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->userB->id]);
        $response->assertJsonMissing(['id' => $this->userA->id]); // excludes self
    }

    public function test_user_can_start_conversation_and_idempotent(): void
    {
        // First call creates/finds conversation
        $response1 = $this->actingAs($this->userA)->postJson('/chat/conversations', [
            'user_id' => $this->userB->id,
        ]);

        $response1->assertStatus(200);
        $convId1 = $response1->json('id');
        $this->assertNotNull($convId1);

        // Second call with same users returns existing conversation
        $response2 = $this->actingAs($this->userB)->postJson('/chat/conversations', [
            'user_id' => $this->userA->id,
        ]);

        $response2->assertStatus(200);
        $convId2 = $response2->json('id');
        $this->assertEquals($convId1, $convId2);
    }

    public function test_user_cannot_start_conversation_with_self(): void
    {
        $response = $this->actingAs($this->userA)->postJson('/chat/conversations', [
            'user_id' => $this->userA->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_send_and_retrieve_messages(): void
    {
        $conv = Conversation::findOrCreateDirect($this->userA->id, $this->userB->id);

        // User A sends message
        $uniqueBody = 'Hello test message ' . uniqid();
        $sendResponse = $this->actingAs($this->userA)->postJson("/chat/conversations/{$conv->id}/messages", [
            'body' => $uniqueBody,
        ]);

        $sendResponse->assertStatus(200)
            ->assertJsonFragment(['body' => $uniqueBody])
            ->assertJsonFragment(['sender_id' => $this->userA->id]);

        $msgId = $sendResponse->json('id');

        // User B retrieves messages
        $getMessagesResponse = $this->actingAs($this->userB)->getJson("/chat/conversations/{$conv->id}/messages");

        $getMessagesResponse->assertStatus(200)
            ->assertJsonFragment(['id' => $msgId, 'body' => $uniqueBody]);
    }

    public function test_unread_count_and_mark_as_read(): void
    {
        $conv = Conversation::findOrCreateDirect($this->userA->id, $this->userC->id);

        // User A sends message to User C
        $this->actingAs($this->userA)->postJson("/chat/conversations/{$conv->id}/messages", [
            'body' => 'Unread message test ' . uniqid(),
        ]);

        // User C checks unread count
        $unreadResponse = $this->actingAs($this->userC)->getJson('/chat/unread-count');
        $unreadResponse->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, $unreadResponse->json('count'));

        // User C marks conversation as read
        $readResponse = $this->actingAs($this->userC)->postJson("/chat/conversations/{$conv->id}/read");
        $readResponse->assertStatus(200)->assertJson(['success' => true]);

        // After reading, unread count for that conversation is 0
        $this->assertEquals(0, $conv->unreadCountFor($this->userC->id));
    }

    public function test_unauthorized_user_cannot_access_or_send_to_conversation(): void
    {
        $conv = Conversation::findOrCreateDirect($this->userA->id, $this->userB->id);

        // User C (not participant) tries to view messages
        $this->actingAs($this->userC)
            ->getJson("/chat/conversations/{$conv->id}/messages")
            ->assertStatus(403);

        // User C tries to send message
        $this->actingAs($this->userC)
            ->postJson("/chat/conversations/{$conv->id}/messages", ['body' => 'Intruder message'])
            ->assertStatus(403);

        // User C tries to mark as read
        $this->actingAs($this->userC)
            ->postJson("/chat/conversations/{$conv->id}/read")
            ->assertStatus(403);
    }
}
