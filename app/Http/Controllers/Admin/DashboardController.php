<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = User::where('role', 'user')->count();
        $totalProfessionals = Professional::count();
        $totalAppointments = Appointment::count();
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');

        // Recent data
        $recentAppointments = Appointment::with(['user', 'professional.user', 'schedule'])
            ->latest()
            ->take(10)
            ->get();

        $recentUsers = User::where('role', 'user')
            ->latest()
            ->take(10)
            ->get();

        // Monthly revenue
        $monthlyRevenue = Payment::where('status', 'paid')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProfessionals',
            'totalAppointments',
            'totalRevenue',
            'recentAppointments',
            'recentUsers',
            'monthlyRevenue'
        ));
    }
}
