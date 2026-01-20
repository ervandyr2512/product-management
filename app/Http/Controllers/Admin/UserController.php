<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['user', 'professional']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,professional',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->load(['appointments', 'professional', 'payments']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:user,professional,admin',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus user admin.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Show form to promote user to professional
     */
    public function promoteForm(User $user)
    {
        if ($user->isProfessional()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User sudah menjadi professional.');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat mempromote admin.');
        }

        return view('admin.users.promote', compact('user'));
    }

    /**
     * Promote user to professional with profile setup
     */
    public function promote(Request $request, User $user)
    {
        if ($user->isProfessional()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User sudah menjadi professional.');
        }

        $request->validate([
            'type' => 'required|in:psychiatrist,psychologist,conversationalist',
            'specialization' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'bio' => 'required|string',
            'rate_30min' => 'required|numeric|min:0',
            'rate_60min' => 'required|numeric|min:0',
            'experience_years' => 'required|integer|min:0',
        ]);

        // Update user role
        $user->role = 'professional';
        $user->save();

        // Create professional profile
        $user->professional()->create([
            'type' => $request->type,
            'specialization' => $request->specialization,
            'license_number' => $request->license_number,
            'bio' => $request->bio,
            'rate_30min' => $request->rate_30min,
            'rate_60min' => $request->rate_60min,
            'experience_years' => $request->experience_years,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil dipromote menjadi professional.");
    }

    /**
     * Demote professional back to regular user
     */
    public function demote(User $user)
    {
        if (!$user->isProfessional()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User bukan professional.');
        }

        // Check if professional has active appointments
        $hasActiveAppointments = $user->professional->appointments()
            ->where('appointment_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($hasActiveAppointments) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat demote professional yang masih memiliki appointment aktif.');
        }

        // Delete professional profile
        $user->professional()->delete();

        // Update user role
        $user->role = 'user';
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Professional {$user->name} berhasil di-demote menjadi user biasa.");
    }
}
