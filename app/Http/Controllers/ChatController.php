<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $user = auth()->user();

        // Get all allowed chat partners based on paid appointments
        $allowedUsers = collect();

        if ($user->role === 'user') {
            // For regular users: get professionals they have paid appointments with
            $allowedUsers = User::whereHas('professional.appointments', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereHas('payment', function ($q) {
                        $q->where('status', 'success');
                    });
            })->get();
        } elseif ($user->role === 'professional') {
            // For professionals: get users who have paid appointments with them
            $professional = $user->professional;
            if ($professional) {
                $allowedUsers = User::whereHas('appointments', function ($query) use ($professional) {
                    $query->where('professional_id', $professional->id)
                        ->whereHas('payment', function ($q) {
                            $q->where('status', 'success');
                        });
                })->get();
            }
        }

        // Get allowed user IDs for filtering
        $allowedUserIds = $allowedUsers->pluck('id')->toArray();

        // Get existing conversations with messages
        $existingConversations = Message::where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            })
            ->filter(function ($messages, $otherUserId) use ($allowedUserIds) {
                // Only show conversations with allowed users
                return in_array($otherUserId, $allowedUserIds);
            })
            ->map(function ($messages) use ($userId) {
                $lastMessage = $messages->first();
                $otherUserId = $lastMessage->sender_id == $userId ? $lastMessage->receiver_id : $lastMessage->sender_id;
                $otherUser = User::find($otherUserId);

                $unreadCount = Message::where('sender_id', $otherUserId)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();

                return [
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            });

        // Get IDs of users who already have conversations
        $existingConversationUserIds = $existingConversations->pluck('user.id')->toArray();

        // Add allowed users who don't have conversations yet
        $newConversations = $allowedUsers
            ->filter(function ($user) use ($existingConversationUserIds) {
                return !in_array($user->id, $existingConversationUserIds);
            })
            ->map(function ($user) {
                return [
                    'user' => $user,
                    'last_message' => null,
                    'unread_count' => 0,
                ];
            });

        // Merge existing and new conversations
        $allConversations = $existingConversations->values()->toArray();
        $newConversationsArray = $newConversations->values()->toArray();

        $conversations = collect(array_merge($allConversations, $newConversationsArray))
            ->sortByDesc(function ($conversation) {
                return $conversation['last_message'] ? $conversation['last_message']->created_at : now()->subYears(10);
            })
            ->values();

        return view('chat.index', compact('conversations'));
    }

    public function show(User $user)
    {
        $currentUserId = auth()->id();
        $currentUser = auth()->user();

        // Validate that user has access to chat with this person
        if (!$this->canChatWith($currentUser, $user)) {
            abort(403, 'Anda tidak memiliki akses untuk chat dengan user ini. Silakan booking dan bayar konsultasi terlebih dahulu.');
        }

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get all messages between users
        $messages = Message::betweenUsers($currentUserId, $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.show', compact('user', 'messages'));
    }

    public function store(Request $request, User $user)
    {
        $currentUser = auth()->user();

        // Validate that user has access to chat with this person
        if (!$this->canChatWith($currentUser, $user)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk chat dengan user ini.',
                ], 403);
            }
            abort(403, 'Anda tidak memiliki akses untuk chat dengan user ini. Silakan booking dan bayar konsultasi terlebih dahulu.');
        }

        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);
        }

        return redirect()->route('chat.show', $user);
    }

    public function fetchMessages(User $user)
    {
        $currentUserId = auth()->id();
        $currentUser = auth()->user();

        // Validate that user has access to chat with this person
        if (!$this->canChatWith($currentUser, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk chat dengan user ini.',
            ], 403);
        }

        // Mark as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::betweenUsers($currentUserId, $user->id)
            ->with(['sender', 'receiver'])
            ->where('created_at', '>', now()->subMinutes(5))
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Check if current user can chat with target user
     */
    private function canChatWith(User $currentUser, User $targetUser): bool
    {
        // If current user is a regular user
        if ($currentUser->role === 'user') {
            // Check if target user is a professional
            $targetProfessional = $targetUser->professional;
            if (!$targetProfessional) {
                return false;
            }

            // Check if current user has paid appointment with this professional
            return Appointment::where('user_id', $currentUser->id)
                ->where('professional_id', $targetProfessional->id)
                ->whereHas('payment', function ($query) {
                    $query->where('status', 'success');
                })
                ->exists();
        }

        // If current user is a professional
        if ($currentUser->role === 'professional') {
            $currentProfessional = $currentUser->professional;
            if (!$currentProfessional) {
                return false;
            }

            // Check if target user has paid appointment with this professional
            return Appointment::where('user_id', $targetUser->id)
                ->where('professional_id', $currentProfessional->id)
                ->whereHas('payment', function ($query) {
                    $query->where('status', 'success');
                })
                ->exists();
        }

        // Admin or other roles cannot chat
        return false;
    }
}
