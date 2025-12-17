<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use Illuminate\Http\Request;

class ProfessionalController extends Controller
{
    public function index(Request $request)
    {
        $query = Professional::query()->where('is_active', true);

        // Filter by type/specialization
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Search by name or specialization
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('user', function ($subQ) use ($request) {
                    $subQ->where('name', 'like', '%' . $request->search . '%');
                })->orWhere('specialization', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        if ($request->has('sort') && $request->sort) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->join('users', 'professionals.user_id', '=', 'users.id')
                          ->select('professionals.*')
                          ->orderBy('users.name', 'asc');
                    break;
                case 'name_desc':
                    $query->join('users', 'professionals.user_id', '=', 'users.id')
                          ->select('professionals.*')
                          ->orderBy('users.name', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('professionals.rate_30min', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('professionals.rate_30min', 'desc');
                    break;
                default:
                    $query->orderBy('professionals.created_at', 'desc');
            }
        } else {
            $query->orderBy('professionals.created_at', 'desc');
        }

        $professionals = $query->with('user')->paginate(12)->appends($request->except('page'));

        return view('professionals.index', compact('professionals'));
    }

    public function show(Professional $professional)
    {
        $professional->load('user', 'schedules');
        $availableSchedules = $professional->availableSchedules()->get();

        return view('professionals.show', compact('professional', 'availableSchedules'));
    }
}
