<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Professional;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()->favoriteProfessionals()->with('user')->get();

        return view('favorites.index', compact('favorites'));
    }

    public function toggle(Professional $professional)
    {
        $user = auth()->user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('professional_id', $professional->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'favorited' => false,
                'message' => __('messages.favorite_removed'),
            ]);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'professional_id' => $professional->id,
            ]);
            return response()->json([
                'success' => true,
                'favorited' => true,
                'message' => __('messages.favorite_added'),
            ]);
        }
    }
}
