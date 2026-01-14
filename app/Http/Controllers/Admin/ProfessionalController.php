<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use Illuminate\Http\Request;

class ProfessionalController extends Controller
{
    public function index(Request $request)
    {
        $query = Professional::with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $professionals = $query->latest()->paginate(20);

        return view('admin.professionals.index', compact('professionals'));
    }

    public function show(Professional $professional)
    {
        $professional->load(['user', 'schedules', 'appointments', 'reviews.user']);
        return view('admin.professionals.show', compact('professional'));
    }

    public function edit(Professional $professional)
    {
        $professional->load('user');
        return view('admin.professionals.edit', compact('professional'));
    }

    public function update(Request $request, Professional $professional)
    {
        $request->validate([
            'type' => 'required|in:psychiatrist,psychologist,conversationalist',
            'license_number' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'rate_30min' => 'required|numeric|min:0',
            'rate_60min' => 'required|numeric|min:0',
            'bio' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $professional->update($request->only([
            'type',
            'license_number',
            'specialization',
            'experience_years',
            'rate_30min',
            'rate_60min',
            'bio',
            'is_active',
        ]));

        return redirect()->route('admin.professionals.index')
            ->with('success', 'Professional berhasil diupdate.');
    }

    public function destroy(Professional $professional)
    {
        $professional->delete();

        return redirect()->route('admin.professionals.index')
            ->with('success', 'Professional berhasil dihapus.');
    }

    public function toggleStatus(Professional $professional)
    {
        $professional->is_active = !$professional->is_active;
        $professional->save();

        $status = $professional->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Professional berhasil {$status}.");
    }
}
