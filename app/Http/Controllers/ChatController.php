<?php

namespace App\Http\Controllers;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * List all conversations for the authenticated user.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $conversations = Conversation::forUser($userId)
            ->with(['users' => function ($q) {
                $q->select('users.id', 'users.name', 'users.role', 'users.avatar_path', 'users.status', 'users.last_activity_at');
            }, 'latestMessage.sender'])
            ->get()
            ->map(function ($conversation) use ($userId) {
                $otherUser = $conversation->getOtherUser($userId);
                $latestMessage = $conversation->latestMessage;

                return [
                    'id' => $conversation->id,
                    'other_user' => $otherUser ? [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'formatted_name' => $otherUser->formatted_name,
                        'role' => $otherUser->role,
                        'avatar_url' => $otherUser->avatar_url,
                        'initials' => $otherUser->initials,
                        'is_present' => $otherUser->is_present,
                    ] : null,
                    'latest_message' => $latestMessage ? [
                        'body' => $latestMessage->body,
                        'sender_id' => $latestMessage->sender_id,
                        'sender_name' => $latestMessage->sender->name,
                        'created_at' => $latestMessage->created_at->toISOString(),
                        'type' => $latestMessage->type,
                    ] : null,
                    'unread_count' => $conversation->unreadCountFor($userId),
                    'updated_at' => ($latestMessage?->created_at ?? $conversation->created_at)->toISOString(),
                ];
            })
            ->sortByDesc('updated_at')
            ->values();

        return response()->json($conversations);
    }

    /**
     * Create or find a direct conversation with another user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $currentUserId = $request->user()->id;
        $otherUserId = (int) $request->user_id;

        if ($currentUserId === $otherUserId) {
            return response()->json(['error' => 'Cannot start a conversation with yourself.'], 422);
        }

        $conversation = Conversation::findOrCreateDirect($currentUserId, $otherUserId);
        $conversation->load(['users', 'latestMessage.sender']);

        $otherUser = $conversation->getOtherUser($currentUserId);

        return response()->json([
            'id' => $conversation->id,
            'other_user' => $otherUser ? [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'formatted_name' => $otherUser->formatted_name,
                'role' => $otherUser->role,
                'avatar_url' => $otherUser->avatar_url,
                'initials' => $otherUser->initials,
                'is_present' => $otherUser->is_present,
            ] : null,
            'latest_message' => null,
            'unread_count' => 0,
            'updated_at' => $conversation->created_at->toISOString(),
        ]);
    }

    /**
     * Fetch paginated messages for a conversation.
     */
    public function messages(Request $request, Conversation $conversation)
    {
        // Verify user is a participant
        if (! $conversation->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()
            ->with(['sender' => function ($q) {
                $q->select('id', 'name', 'role', 'avatar_path');
            }])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(30);

        $mapped = collect($messages->items())->map(function ($msg) {
            return [
                'id' => $msg->id,
                'conversation_id' => $msg->conversation_id,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name,
                'sender_avatar' => $msg->sender->avatar_url,
                'sender_initials' => $msg->sender->initials,
                'sender_role' => $msg->sender->role,
                'body' => $msg->body,
                'type' => $msg->type,
                'created_at' => $msg->created_at->toISOString(),
            ];
        });

        $otherUser = $conversation->getOtherUser($request->user()->id);
        $rawReadAt = $otherUser
            ? $conversation->users()->where('users.id', $otherUser->id)->first()?->pivot?->last_read_at
            : null;
        $otherUserLastReadAt = $rawReadAt ? \Illuminate\Support\Carbon::parse($rawReadAt)->toISOString() : null;

        return response()->json([
            'data' => $mapped,
            'next_cursor' => $messages->nextCursor()?->encode(),
            'has_more' => $messages->hasMorePages(),
            'other_user_last_read_at' => $otherUserLastReadAt,
        ]);
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        // Verify user is a participant
        if (! $conversation->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => strip_tags($request->body),
            'type' => 'text',
        ]);

        $message->load('sender');

        // Touch conversation timestamp
        $conversation->touch();

        // Broadcast to all participants
        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Chat broadcast MessageSent failed: ' . $e->getMessage());
        }

        return response()->json([
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->name,
            'sender_avatar' => $message->sender->avatar_url,
            'sender_initials' => $message->sender->initials,
            'sender_role' => $message->sender->role,
            'body' => $message->body,
            'type' => $message->type,
            'created_at' => $message->created_at->toISOString(),
        ]);
    }

    /**
     * Mark a conversation as read for the authenticated user.
     */
    public function markAsRead(Request $request, Conversation $conversation)
    {
        $userId = $request->user()->id;

        if (! $conversation->users()->where('users.id', $userId)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $now = now();
        $conversation->users()->updateExistingPivot($userId, [
            'last_read_at' => $now,
        ]);

        try {
            broadcast(new MessageRead($conversation->id, $userId, $now->toISOString()))->toOthers();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Chat broadcast MessageRead failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }

    /**
     * List staff users available to chat with.
     */
    public function users(Request $request)
    {
        $search = $request->get('search', '');
        $currentUserId = $request->user()->id;

        $query = User::where('id', '!=', $currentUserId)
            ->whereNull('deleted_at')
            ->whereNotIn('role', ['ordinary_user'])
            ->select('id', 'name', 'role', 'avatar_path', 'status', 'last_activity_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'formatted_name' => $user->formatted_name,
                'role' => $user->role,
                'role_label' => ucfirst(str_replace('_', ' ', $user->role)),
                'avatar_url' => $user->avatar_url,
                'initials' => $user->initials,
                'is_present' => $user->is_present,
            ];
        });

        return response()->json($users);
    }

    /**
     * Get total unread message count across all conversations.
     */
    public function unreadCount(Request $request)
    {
        $userId = $request->user()->id;
        $total = 0;

        $conversations = Conversation::forUser($userId)->get();
        foreach ($conversations as $conversation) {
            $total += $conversation->unreadCountFor($userId);
        }

        return response()->json(['count' => $total]);
    }
}
