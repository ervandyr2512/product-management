# Chat System Documentation

## Overview
The chat system allows users to communicate with mental health professionals they have booked and paid for. This document covers the recent improvements to show all available chat partners.

## Problem Statement

### Initial Issue
Users could only see chat conversations if they had already sent or received messages with a professional. This meant:
- Newly booked professionals didn't appear in the chat list
- Users didn't know they could start chatting with booked professionals
- Confusing user experience after completing payment

### Expected Behavior
After successfully booking and paying for a consultation with a professional, users should:
1. See the professional in their chat list immediately
2. Be able to initiate conversation before the scheduled appointment
3. Easily identify which professionals they can chat with

## Solution Implemented

### Core Logic Changes

#### ChatController::index() Method
Located in `app/Http/Controllers/ChatController.php` (lines 13-103)

**Step 1: Get All Allowed Chat Partners**
```php
$allowedUsers = collect();

if ($user->role === 'user') {
    // For regular users: get professionals they have paid appointments with
    $allowedUsers = User::whereHas('professional.appointments', function ($query) use ($userId) {
        $query->where('user_id', $userId)
            ->whereHas('payment', function ($q) {
                $q->where('status', 'success');
            });
    })->get();
}
```

**Step 2: Get Existing Conversations**
```php
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
        return in_array($otherUserId, $allowedUserIds);
    })
    ->map(function ($messages) use ($userId) {
        // Map to conversation structure
        return [
            'user' => $otherUser,
            'last_message' => $lastMessage,
            'unread_count' => $unreadCount,
        ];
    });
```

**Step 3: Add New Conversations (No Messages Yet)**
```php
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
```

**Step 4: Merge and Sort**
```php
// Convert to arrays first to avoid collection merge issues
$allConversations = $existingConversations->values()->toArray();
$newConversationsArray = $newConversations->values()->toArray();

// Merge using array_merge, then wrap in collection
$conversations = collect(array_merge($allConversations, $newConversationsArray))
    ->sortByDesc(function ($conversation) {
        return $conversation['last_message']
            ? $conversation['last_message']->created_at
            : now()->subYears(10);
    })
    ->values();
```

### View Updates

#### Chat Index View
Located in `resources/views/chat/index.blade.php` (lines 46-61)

**Handling Null Last Message:**
```php
<div class="flex items-center justify-between mt-1">
    @if($conversation['last_message'])
        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
            {{ Str::limit($conversation['last_message']->message, 50) }}
        </p>
    @else
        <p class="text-sm text-gray-400 dark:text-gray-500 italic">
            {{ __('messages.no_messages_yet') }}
        </p>
    @endif
    @if($conversation['unread_count'] > 0)
        <span class="ml-2 bg-purple-600 text-white text-xs px-2 py-1 rounded-full">
            {{ $conversation['unread_count'] }}
        </span>
    @endif
</div>
```

## Technical Details

### Data Structure

#### Conversation Array Structure
```php
[
    'user' => User,                    // The other user (professional or client)
    'last_message' => Message|null,    // Most recent message or null
    'unread_count' => int              // Number of unread messages
]
```

### Database Queries

#### Query for Allowed Professionals (User Role)
```sql
SELECT users.*
FROM users
WHERE EXISTS (
    SELECT 1 FROM professionals
    WHERE professionals.user_id = users.id
    AND EXISTS (
        SELECT 1 FROM appointments
        WHERE appointments.professional_id = professionals.id
        AND appointments.user_id = ?
        AND EXISTS (
            SELECT 1 FROM payments
            WHERE payments.appointment_id = appointments.id
            AND payments.status = 'success'
        )
    )
)
```

#### Query for Allowed Clients (Professional Role)
```sql
SELECT users.*
FROM users
WHERE EXISTS (
    SELECT 1 FROM appointments
    WHERE appointments.user_id = users.id
    AND appointments.professional_id = ?
    AND EXISTS (
        SELECT 1 FROM payments
        WHERE payments.appointment_id = appointments.id
        AND payments.status = 'success'
    )
)
```

### Error Handling

#### Collection Merge Error Fix

**Problem:**
```php
// This caused error: "Call to a member function getKey() on array"
$conversations = $existingConversations->merge($newConversations);
```

**Root Cause:**
Laravel's `merge()` method expects collections with models, but we had nested arrays with mixed structures.

**Solution:**
```php
// Convert to plain arrays first
$allConversations = $existingConversations->values()->toArray();
$newConversationsArray = $newConversations->values()->toArray();

// Use PHP's array_merge, then wrap in collection
$conversations = collect(array_merge($allConversations, $newConversationsArray));
```

## Access Control

### canChatWith() Method
Located in `app/Http/Controllers/ChatController.php` (lines 119-156)

**Purpose:** Verify that a user has permission to chat with another user.

**Logic:**
1. **User trying to chat with Professional:**
   - Check if professional exists
   - Verify user has paid appointment with professional

2. **Professional trying to chat with User:**
   - Check if professional profile exists
   - Verify user has paid appointment with this professional

3. **Admin or other roles:**
   - Return false (not allowed to chat)

**Implementation:**
```php
private function canChatWith(User $currentUser, User $targetUser): bool
{
    if ($currentUser->role === 'user') {
        $targetProfessional = $targetUser->professional;
        if (!$targetProfessional) return false;

        return Appointment::where('user_id', $currentUser->id)
            ->where('professional_id', $targetProfessional->id)
            ->whereHas('payment', function ($query) {
                $query->where('status', 'success');
            })
            ->exists();
    }

    if ($currentUser->role === 'professional') {
        $currentProfessional = $currentUser->professional;
        if (!$currentProfessional) return false;

        return Appointment::where('user_id', $targetUser->id)
            ->where('professional_id', $currentProfessional->id)
            ->whereHas('payment', function ($query) {
                $query->where('status', 'success');
            })
            ->exists();
    }

    return false;
}
```

## User Interface

### Chat List Display

#### With Messages
```
┌─────────────────────────────────────────────────┐
│ [Photo] Dr. John Doe - Psychologist             │
│         Hey, how are you doing today?      2h   │
│                                           [2]   │
└─────────────────────────────────────────────────┘
```

#### Without Messages (New)
```
┌─────────────────────────────────────────────────┐
│ [Photo] Dr. Jane Smith - Psychiatrist           │
│         No messages yet                         │
│                                                 │
└─────────────────────────────────────────────────┘
```

### Sort Order
1. Conversations with recent messages (newest first)
2. Conversations without messages (at the bottom)

**Sorting Logic:**
```php
->sortByDesc(function ($conversation) {
    return $conversation['last_message']
        ? $conversation['last_message']->created_at
        : now()->subYears(10);  // Very old date for conversations without messages
})
```

## Translation Keys

### Added Keys
Located in `lang/en/messages.php` and `lang/id/messages.php`

```php
'no_messages_yet' => 'No messages yet' / 'Belum ada pesan',
'no_messages' => 'No Messages' / 'Tidak Ada Pesan',
'start_conversation' => 'Start a conversation with a professional' / 'Mulai percakapan dengan profesional',
```

## Testing Guide

### Test Scenarios

#### 1. User Books and Pays for Consultation
```
Given: User completes booking and payment
When: User navigates to chat page
Then: Professional should appear in chat list
And: Show "No messages yet" indicator
```

#### 2. User Starts Conversation
```
Given: User has booked professional in chat list
When: User sends first message
Then: "No messages yet" changes to actual message
And: Conversation moves to top of list
```

#### 3. Professional Receives Booking
```
Given: User books and pays for professional
When: Professional navigates to chat page
Then: User should appear in chat list
And: Show "No messages yet" indicator
```

#### 4. Multiple Conversations
```
Given: User has multiple booked professionals
When: User views chat list
Then: All professionals appear
And: Conversations with messages appear first
And: New conversations appear at bottom
```

#### 5. Access Control
```
Given: User tries to access chat with non-booked professional
When: User attempts to navigate to chat/:id
Then: Show 403 error
And: Display message about booking requirement
```

## Performance Considerations

### Database Queries
- **Allowed Users Query:** 1 query per page load
- **Existing Conversations:** 1 query with eager loading
- **Unread Count:** 1 query per conversation

### Optimization Strategies
1. **Eager Loading:**
   ```php
   ->with(['sender', 'receiver'])
   ```

2. **Query Caching (Future):**
   ```php
   Cache::remember("chat.allowed.{$userId}", 300, function() {
       return $allowedUsers;
   });
   ```

3. **Pagination (Future):**
   ```php
   $conversations->paginate(20);
   ```

## Security Considerations

### Access Control
- ✅ Users can only chat with booked professionals
- ✅ Professionals can only chat with their clients
- ✅ Payment verification required
- ✅ Direct URL access blocked without permission

### Data Privacy
- Messages only visible to sender and receiver
- No admin access to private messages
- Secure database queries with proper escaping

## Future Enhancements

### Planned Features
1. **Real-time Messaging:**
   - WebSocket integration with Laravel Echo
   - Instant message delivery
   - Typing indicators

2. **Chat Features:**
   - File sharing (images, documents)
   - Voice messages
   - Video call integration

3. **Notifications:**
   - Email notification for new messages
   - Push notifications via FCM
   - Desktop notifications

4. **Search & Filter:**
   - Search within conversations
   - Filter by professional type
   - Archive conversations

5. **Performance:**
   - Message pagination
   - Lazy loading conversations
   - Cache frequently accessed data

## Related Documentation
- [Navigation Improvements](./NAVIGATION-IMPROVEMENTS.md)
- [Multilingual Support](./MULTILINGUAL-SUPPORT.md)
- [API Documentation](./API.md)
