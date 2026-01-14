<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\Schedule;
use App\Notifications\AppointmentConfirmed;
use App\Notifications\NewAppointmentForProfessional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function checkout()
    {
        $carts = Cart::with('professional.user', 'schedule')
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('professionals.index')->with('error', 'Keranjang Anda kosong.');
        }

        $total = $carts->sum('price');

        return view('payment.checkout', compact('carts', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,bank_transfer,e-wallet',
        ]);

        $carts = Cart::with('professional', 'schedule')
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('professionals.index')->with('error', 'Keranjang Anda kosong.');
        }

        DB::beginTransaction();

        try {
            $appointments = [];

            foreach ($carts as $cart) {
                $schedule = $cart->schedule;

                if (!$schedule->is_available) {
                    throw new \Exception('Jadwal tidak tersedia lagi.');
                }

                $endTime = $cart->duration == '30'
                    ? date('H:i:s', strtotime($schedule->start_time) + 1800)
                    : date('H:i:s', strtotime($schedule->start_time) + 3600);

                $appointment = Appointment::create([
                    'user_id' => auth()->id(),
                    'professional_id' => $cart->professional_id,
                    'schedule_id' => $cart->schedule_id,
                    'appointment_date' => $schedule->date,
                    'start_time' => $schedule->start_time,
                    'end_time' => $endTime,
                    'duration' => $cart->duration,
                    'price' => $cart->price,
                    'status' => 'pending',
                ]);

                $payment = Payment::create([
                    'user_id' => auth()->id(),
                    'appointment_id' => $appointment->id,
                    'payment_gateway_id' => 'PG-' . strtoupper(Str::random(10)),
                    'amount' => $cart->price,
                    'status' => 'success',
                    'payment_method' => $request->payment_method,
                    'payment_details' => json_encode([
                        'method' => $request->payment_method,
                    ]),
                    'paid_at' => now(),
                ]);

                $appointment->update(['status' => 'confirmed']);
                $schedule->update(['is_available' => false]);

                $appointments[] = $appointment;

                // Send notification to user
                auth()->user()->notify(new AppointmentConfirmed($appointment));

                // Send notification to professional
                $appointment->professional->user->notify(new NewAppointmentForProfessional($appointment));
            }

            Cart::where('user_id', auth()->id())->delete();

            DB::commit();

            return redirect()->route('appointments.index')->with('success', 'Pembayaran berhasil! Cek email dan WhatsApp Anda untuk detail.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Pembayaran gagal: ' . $e->getMessage());
        }
    }

    public function quickCheckout(Request $request, Schedule $schedule)
    {
        $request->validate([
            'duration' => 'required|in:30,60',
        ]);

        $professional = $schedule->professional;
        $price = $request->duration == '30' ? $professional->rate_30min : $professional->rate_60min;

        return view('payment.quick-checkout', compact('schedule', 'professional', 'price', 'request'));
    }

    public function quickProcess(Request $request, Schedule $schedule)
    {
        $request->validate([
            'duration' => 'required|in:30,60',
            'payment_method' => 'required|in:credit_card,bank_transfer,e-wallet',
        ]);

        $professional = $schedule->professional;

        if (!$schedule->is_available) {
            return back()->with('error', 'Jadwal tidak tersedia lagi.');
        }

        DB::beginTransaction();

        try {
            $price = $request->duration == '30' ? $professional->rate_30min : $professional->rate_60min;

            $endTime = $request->duration == '30'
                ? date('H:i:s', strtotime($schedule->start_time) + 1800)
                : date('H:i:s', strtotime($schedule->start_time) + 3600);

            $appointment = Appointment::create([
                'user_id' => auth()->id(),
                'professional_id' => $professional->id,
                'schedule_id' => $schedule->id,
                'appointment_date' => $schedule->date,
                'start_time' => $schedule->start_time,
                'end_time' => $endTime,
                'duration' => $request->duration,
                'price' => $price,
                'status' => 'pending',
            ]);

            $payment = Payment::create([
                'user_id' => auth()->id(),
                'appointment_id' => $appointment->id,
                'payment_gateway_id' => 'PG-' . strtoupper(Str::random(10)),
                'amount' => $price,
                'status' => 'success',
                'payment_method' => $request->payment_method,
                'payment_details' => json_encode([
                    'method' => $request->payment_method,
                ]),
                'paid_at' => now(),
            ]);

            $appointment->update(['status' => 'confirmed']);
            $schedule->update(['is_available' => false]);

            // Send notification to user
            auth()->user()->notify(new AppointmentConfirmed($appointment));

            // Send notification to professional
            $appointment->professional->user->notify(new NewAppointmentForProfessional($appointment));

            DB::commit();

            return redirect()->route('appointments.index')->with('success', 'Booking berhasil! Cek email dan WhatsApp Anda untuk detail.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Booking gagal: ' . $e->getMessage());
        }
    }
}
