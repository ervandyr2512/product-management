<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWhatsapp')) {
            return;
        }

        $message = $notification->toWhatsapp($notifiable);

        if (!$message) {
            return;
        }

        $phone = $notifiable->phone;

        if (!$phone) {
            Log::warning('WhatsApp notification failed: No phone number for user', [
                'user_id' => $notifiable->id,
            ]);
            return;
        }

        // Format phone number (ensure it starts with country code, remove any + or spaces)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (!str_starts_with($phone, '62')) {
            // If doesn't start with 62 (Indonesia), assume it's local number starting with 0
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } else {
                $phone = '62' . $phone;
            }
        }

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => config('services.waha.api_key'),
            ])->post(config('services.waha.url') . '/api/sendText', [
                'session' => config('services.waha.session'),
                'chatId' => $phone . '@c.us',
                'text' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp notification sent successfully', [
                    'user_id' => $notifiable->id,
                    'phone' => $phone,
                ]);
            } else {
                Log::error('WhatsApp notification failed', [
                    'user_id' => $notifiable->id,
                    'phone' => $phone,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('WhatsApp notification exception', [
                'user_id' => $notifiable->id,
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
