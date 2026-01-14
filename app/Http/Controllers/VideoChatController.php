<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\VideoChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoChatController extends Controller
{
    public function show(Appointment $appointment)
    {
        $user = Auth::user();

        // Check if user has access to this appointment
        if ($user->id !== $appointment->user_id &&
            (!$user->professional || $user->professional->id !== $appointment->professional_id)) {
            abort(403, 'Unauthorized access to this video chat.');
        }

        // Check if appointment is confirmed and paid
        if ($appointment->status !== 'confirmed' || !$appointment->payment || $appointment->payment->status !== 'success') {
            return redirect()->back()->with('error', __('messages.video_chat_not_available'));
        }

        // Check if can join video chat
        if (!$appointment->canStartVideoChat()) {
            return redirect()->back()->with('error', __('messages.video_chat_not_ready'));
        }

        // Get or create video chat room
        $room = $appointment->videoChatRoom;
        if (!$room) {
            $room = VideoChatRoom::create([
                'appointment_id' => $appointment->id,
            ]);
        }

        // Check if room can be joined
        if (!$room->canJoin()) {
            return redirect()->back()->with('error', __('messages.video_chat_expired'));
        }

        // Determine user role
        $isProvider = $user->professional && $user->professional->id === $appointment->professional_id;
        $userName = $isProvider ? $user->professional->user->name : $user->name;

        return view('video-chat.room', compact('appointment', 'room', 'isProvider', 'userName'));
    }

    public function start(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Check access
        if ($user->id !== $appointment->user_id &&
            (!$user->professional || $user->professional->id !== $appointment->professional_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $room = $appointment->videoChatRoom;
        if (!$room) {
            $room = VideoChatRoom::create([
                'appointment_id' => $appointment->id,
            ]);
        }

        if ($room->status === 'pending') {
            $room->start();
        }

        return response()->json([
            'success' => true,
            'room_id' => $room->room_id,
            'status' => $room->status,
        ]);
    }

    public function end(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Check access
        if ($user->id !== $appointment->user_id &&
            (!$user->professional || $user->professional->id !== $appointment->professional_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $room = $appointment->videoChatRoom;
        if ($room && $room->status === 'active') {
            $room->end();

            // Update appointment status to completed
            $appointment->update(['status' => 'completed']);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.video_chat_ended'),
        ]);
    }

    public function signal(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Check access
        if ($user->id !== $appointment->user_id &&
            (!$user->professional || $user->professional->id !== $appointment->professional_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Broadcast signaling data to other participant
        $room = $appointment->videoChatRoom;
        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        // Return signaling data for WebRTC connection
        return response()->json([
            'success' => true,
            'data' => $request->all(),
        ]);
    }
}
