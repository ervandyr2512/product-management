<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Professional;
use App\Models\Schedule;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('professional.user', 'schedule')
            ->where('user_id', auth()->id())
            ->get();

        $total = $carts->sum('price');

        return view('cart.index', compact('carts', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'professional_id' => 'required|exists:professionals,id',
            'schedule_id' => 'required|exists:schedules,id',
            'duration' => 'required|in:30,60',
        ]);

        $professional = Professional::findOrFail($request->professional_id);
        $schedule = Schedule::findOrFail($request->schedule_id);

        if (!$schedule->is_available) {
            return back()->with('error', 'Jadwal tidak tersedia.');
        }

        $price = $request->duration == '30' ? $professional->rate_30min : $professional->rate_60min;

        $existingCart = Cart::where('user_id', auth()->id())
            ->where('schedule_id', $request->schedule_id)
            ->first();

        if ($existingCart) {
            return back()->with('error', 'Jadwal ini sudah ada di keranjang Anda.');
        }

        Cart::create([
            'user_id' => auth()->id(),
            'professional_id' => $request->professional_id,
            'schedule_id' => $request->schedule_id,
            'duration' => $request->duration,
            'price' => $price,
        ]);

        return redirect()->route('cart.index')->with('success', 'Berhasil ditambahkan ke keranjang.');
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cart->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
