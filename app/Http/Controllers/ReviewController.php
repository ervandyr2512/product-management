<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create(Appointment $appointment)
    {
        // Check if appointment belongs to user and is completed
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->status !== 'completed') {
            return redirect()->route('appointments.index')
                ->with('error', 'Hanya appointment yang sudah selesai yang bisa di-review.');
        }

        // Check if review already exists
        if ($appointment->review) {
            return redirect()->route('appointments.index')
                ->with('error', 'Anda sudah memberikan review untuk appointment ini.');
        }

        return view('reviews.create', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        // Check if appointment belongs to user and is completed
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->status !== 'completed') {
            return redirect()->route('appointments.index')
                ->with('error', 'Hanya appointment yang sudah selesai yang bisa di-review.');
        }

        // Check if review already exists
        if ($appointment->review) {
            return redirect()->route('appointments.index')
                ->with('error', 'Anda sudah memberikan review untuk appointment ini.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'user_id' => auth()->id(),
            'professional_id' => $appointment->professional_id,
            'appointment_id' => $appointment->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Terima kasih atas review Anda!');
    }
}
