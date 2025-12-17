<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->role !== 'professional') {
            abort(403, 'Unauthorized');
        }

        $professional = $user->professional;

        if (!$professional) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum terdaftar sebagai professional.');
        }

        $schedules = Schedule::where('professional_id', $professional->id)
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(20);

        return view('professional.schedules.index', compact('professional', 'schedules'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->role !== 'professional') {
            abort(403, 'Unauthorized');
        }

        $professional = $user->professional;

        if (!$professional) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum terdaftar sebagai professional.');
        }

        return view('professional.schedules.create', compact('professional'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'professional') {
            abort(403, 'Unauthorized');
        }

        $professional = $user->professional;

        if (!$professional) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum terdaftar sebagai professional.');
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Check if schedule already exists
        $exists = Schedule::where('professional_id', $professional->id)
            ->where('date', $request->date)
            ->where('start_time', $request->start_time)
            ->exists();

        if ($exists) {
            return back()->withErrors(['date' => 'Jadwal pada waktu ini sudah ada.'])->withInput();
        }

        Schedule::create([
            'professional_id' => $professional->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => true,
        ]);

        return redirect()->route('professional.schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function destroy(Schedule $schedule)
    {
        $user = auth()->user();

        if ($user->role !== 'professional' || $schedule->professional->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Check if schedule is already booked
        if ($schedule->appointments()->exists()) {
            return back()->with('error', 'Jadwal ini sudah di-booking dan tidak dapat dihapus.');
        }

        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
