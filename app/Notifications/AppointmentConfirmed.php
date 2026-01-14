<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class AppointmentConfirmed extends Notification
{
    use Queueable;

    public $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'whatsapp'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Konfirmasi Pembayaran - Janji Temu Teman Bicara')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Terima kasih telah membuat janji temu di temanbicara.com.')
            ->line('Pembayaran Anda telah terkonfirmasi dengan nominal **Rp ' . number_format($this->appointment->price, 0, ',', '.') . '**')
            ->line('')
            ->line('**Berikut adalah detail janji temu Anda:**')
            ->line('👤 Professional: ' . $this->appointment->professional->user->name)
            ->line('📅 Tanggal: ' . $this->appointment->appointment_date->format('d F Y'))
            ->line('🕐 Jam: ' . date('H:i', strtotime($this->appointment->start_time)) . ' - ' . date('H:i', strtotime($this->appointment->end_time)) . ' WIB')
            ->line('⏱️ Durasi: ' . $this->appointment->duration . ' menit')
            ->line('💰 Nominal Pembayaran: Rp ' . number_format($this->appointment->price, 0, ',', '.'))
            ->line('')
            ->action('Lihat Detail Janji Temu', route('appointments.show', $this->appointment->id))
            ->line('')
            ->line('💡 **Penting:** Anda dapat bergabung ke video konsultasi 10 menit sebelum jadwal dimulai.')
            ->line('')
            ->line('Terima kasih telah mempercayai Teman Bicara untuk kesehatan mental Anda!');
    }

    /**
     * Send WhatsApp notification via WAHA
     */
    public function toWhatsapp(object $notifiable)
    {
        $appointmentUrl = route('appointments.show', $this->appointment->id);

        $message = "*Konfirmasi Pembayaran - Teman Bicara*\n\n"
            . "Halo {$notifiable->name}!\n\n"
            . "Terima kasih telah membuat janji temu di temanbicara.com.\n\n"
            . "Pembayaran Anda telah terkonfirmasi dengan nominal *Rp " . number_format($this->appointment->price, 0, ',', '.') . "*\n\n"
            . "*Berikut adalah detail janji temu Anda:*\n"
            . "👤 Professional: {$this->appointment->professional->user->name}\n"
            . "📅 Tanggal: {$this->appointment->appointment_date->format('d F Y')}\n"
            . "🕐 Jam: " . date('H:i', strtotime($this->appointment->start_time)) . " - " . date('H:i', strtotime($this->appointment->end_time)) . " WIB\n"
            . "⏱️ Durasi: {$this->appointment->duration} menit\n"
            . "💰 Nominal Pembayaran: Rp " . number_format($this->appointment->price, 0, ',', '.') . "\n\n"
            . "💡 *Penting:*\n"
            . "Anda dapat bergabung ke video konsultasi 10 menit sebelum jadwal dimulai.\n\n"
            . "🔗 Lihat detail & join video konsultasi:\n{$appointmentUrl}\n\n"
            . "Terima kasih telah mempercayai Teman Bicara untuk kesehatan mental Anda! 💚";

        // Phone number should already be in international format (e.g., 6281234567890)
        // Just remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $notifiable->phone);

        \Log::info('WhatsApp Notification Debug', [
            'original_phone' => $notifiable->phone,
            'formatted_phone' => $phone,
            'chatId' => $phone . '@c.us',
            'waha_url' => config('services.waha.url'),
            'waha_session' => config('services.waha.session'),
        ]);

        try {
            $response = Http::timeout(10) // Set timeout 10 seconds
                ->withHeaders([
                    'X-Api-Key' => config('services.waha.api_key'),
                ])
                ->post(config('services.waha.url') . '/api/sendText', [
                    'session' => config('services.waha.session'),
                    'chatId' => $phone . '@c.us',
                    'text' => $message,
                ]);

            if ($response->successful()) {
                \Log::info('WhatsApp notification sent successfully', [
                    'phone' => $phone,
                    'response' => $response->json()
                ]);
            } else {
                \Log::error('WhatsApp API returned error', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::warning('WhatsApp notification timeout or connection failed (non-blocking)', [
                'phone' => $phone,
                'error' => 'Connection timeout or failed',
                'message' => $e->getMessage()
            ]);
            // Don't block the notification process if WhatsApp fails
        } catch (\Exception $e) {
            \Log::warning('WhatsApp notification failed (non-blocking): ' . $e->getMessage(), [
                'phone' => $phone,
                'exception' => get_class($e),
            ]);
            // Don't block the notification process if WhatsApp fails
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'professional_name' => $this->appointment->professional->user->name,
            'appointment_date' => $this->appointment->appointment_date,
            'start_time' => $this->appointment->start_time,
        ];
    }
}
