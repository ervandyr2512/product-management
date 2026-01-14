<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $recommendations = null;

        // If user is authenticated, get personalized recommendations
        if (auth()->check() && auth()->user()->isUser()) {
            $recommendations = auth()->user()->getRecommendedProfessionals(6);
        } else {
            // For guests, show top-rated professionals
            $recommendations = Professional::where('is_active', true)
                ->with(['user', 'reviews'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('reviews_count')
                ->take(6)
                ->get();
        }

        return view('welcome', compact('recommendations'));
    }
}
