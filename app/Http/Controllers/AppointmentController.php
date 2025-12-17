<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with('professional.user')
            ->where('user_id', auth()->id())
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('appointments.index', compact('appointments'));
    }

    public function show(Appointment $appointment)
    {
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        $appointment->load('professional.user', 'payment');

        return view('appointments.show', compact('appointment'));
    }

    public function cancel(Appointment $appointment)
    {
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->status === 'completed' || $appointment->status === 'cancelled') {
            return back()->with('error', 'Appointment tidak dapat dibatalkan.');
        }

        $appointment->update(['status' => 'cancelled']);
        $appointment->schedule->update(['is_available' => true]);

        return back()->with('success', 'Appointment berhasil dibatalkan.');
    }
}
