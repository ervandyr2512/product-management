# Notifications System - Teman Bicara

## Overview

Teman Bicara menggunakan dual-channel notification system:
- **Email** via Mailhog (development) / SMTP (production)
- **WhatsApp** via WAHA (WhatsApp HTTP API)

## Architecture

```
┌──────────────────┐
│  Event Trigger   │ (Appointment Created, Cancelled, etc.)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│   Notification   │ (AppointmentConfirmed, etc.)
│      Class       │
└────────┬─────────┘
         │
         ├─────────────────┬─────────────────┐
         ▼                 ▼                 ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│     Mail     │  │   WhatsApp   │  │   Database   │
│   Channel    │  │   Channel    │  │   Channel    │
└──────┬───────┘  └──────┬───────┘  └──────┬───────┘
       │                 │                 │
       ▼                 ▼                 ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│   Mailhog    │  │     WAHA     │  │  notifications│
│  (Dev Mail)  │  │  (WhatsApp)  │  │     Table     │
└──────────────┘  └──────────────┘  └──────────────┘
```

## Notification Types

### 1. AppointmentConfirmed

**Trigger**: After successful payment and appointment creation

**Recipients**: User (client)

**Channels**: Email, WhatsApp

**Purpose**: Confirm booking and provide video link

### 2. AppointmentCancelled

**Trigger**: When user or professional cancels appointment

**Recipients**: User (client) and Professional

**Channels**: Email, WhatsApp

**Purpose**: Notify about cancellation

### 3. AppointmentReminder (Future)

**Trigger**: 24 hours before appointment

**Recipients**: User (client) and Professional

**Channels**: Email, WhatsApp

**Purpose**: Remind about upcoming appointment

### 4. ScheduleCreated (Future)

**Trigger**: When professional adds new schedule

**Recipients**: Professional

**Channels**: Email

**Purpose**: Confirm schedule creation

## Email Notifications

### Configuration

**Development (.env):**
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@temanbicara.com"
MAIL_FROM_NAME="Teman Bicara"
```

**Production (.env):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@temanbicara.com"
MAIL_FROM_NAME="Teman Bicara"
```

### Mailhog Setup

**Start Mailhog:**
```bash
docker-compose up -d mailhog
```

**Access Web UI:**
```
http://localhost:8025
```

**Features:**
- View all sent emails
- Search emails
- View HTML and plain text versions
- Download attachments
- Delete emails

### Email Templates

**Location**: `resources/views/emails/` (if customized)

**Default Laravel Templates**: Used by default via MailMessage

### AppointmentConfirmed Email

**File**: `app/Notifications/AppointmentConfirmed.php`

```php
<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\WhatsappChannel;

class AppointmentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail', WhatsappChannel::class];
    }

    public function toMail($notifiable)
    {
        $appointment = $this->appointment;
        $schedule = $appointment->schedule;
        $professional = $appointment->professional;

        return (new MailMessage)
            ->subject('Appointment Confirmed - Teman Bicara')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Appointment Anda dengan ' . $professional->user->name . ' telah dikonfirmasi.')
            ->line('')
            ->line('**Detail Appointment:**')
            ->line('👨‍⚕️ Professional: ' . $professional->user->name)
            ->line('🏥 Spesialisasi: ' . ucfirst($professional->specialization))
            ->line('📅 Tanggal: ' . $schedule->date->format('d M Y'))
            ->line('🕐 Waktu: ' . $schedule->start_time . ' - ' . $schedule->end_time)
            ->line('⏱️ Durasi: ' . $appointment->duration . ' menit')
            ->line('💰 Harga: Rp ' . number_format($appointment->price, 0, ',', '.'))
            ->line('')
            ->action('Join Video Call', $appointment->video_link)
            ->line('Link video call akan aktif pada waktu appointment.')
            ->line('')
            ->line('Terima kasih telah menggunakan Teman Bicara!');
    }

    public function toWhatsapp($notifiable)
    {
        $appointment = $this->appointment;
        $schedule = $appointment->schedule;
        $professional = $appointment->professional;

        $message = "Halo {$notifiable->name},\n\n";
        $message .= "Appointment Anda telah dikonfirmasi! ✅\n\n";
        $message .= "Detail:\n";
        $message .= "👨‍⚕️ Professional: {$professional->user->name}\n";
        $message .= "🏥 Spesialisasi: " . ucfirst($professional->specialization) . "\n";
        $message .= "📅 Tanggal: {$schedule->date->format('d M Y')}\n";
        $message .= "🕐 Waktu: {$schedule->start_time} - {$schedule->end_time}\n";
        $message .= "⏱️ Durasi: {$appointment->duration} menit\n";
        $message .= "💰 Harga: Rp " . number_format($appointment->price, 0, ',', '.') . "\n\n";
        $message .= "🔗 Video Link:\n{$appointment->video_link}\n\n";
        $message .= "Terima kasih,\nTeman Bicara";

        return $message;
    }
}
```

### AppointmentCancelled Email

**File**: `app/Notifications/AppointmentCancelled.php`

```php
<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\WhatsappChannel;

class AppointmentCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail', WhatsappChannel::class];
    }

    public function toMail($notifiable)
    {
        $appointment = $this->appointment;
        $schedule = $appointment->schedule;
        $professional = $appointment->professional;

        return (new MailMessage)
            ->subject('Appointment Cancelled - Teman Bicara')
            ->greeting('Halo ' . $notifiable->name)
            ->line('Appointment Anda telah dibatalkan.')
            ->line('')
            ->line('**Detail Appointment yang Dibatalkan:**')
            ->line('👨‍⚕️ Professional: ' . $professional->user->name)
            ->line('📅 Tanggal: ' . $schedule->date->format('d M Y'))
            ->line('🕐 Waktu: ' . $schedule->start_time)
            ->line('⏱️ Durasi: ' . $appointment->duration . ' menit')
            ->line('')
            ->line('Jika Anda memiliki pertanyaan, silakan hubungi kami.')
            ->action('Cari Professional Lain', url('/professionals'))
            ->line('Terima kasih,')
            ->line('Teman Bicara');
    }

    public function toWhatsapp($notifiable)
    {
        $appointment = $this->appointment;
        $schedule = $appointment->schedule;
        $professional = $appointment->professional;

        $message = "Halo {$notifiable->name},\n\n";
        $message .= "Appointment Anda telah dibatalkan. ❌\n\n";
        $message .= "Detail:\n";
        $message .= "👨‍⚕️ Professional: {$professional->user->name}\n";
        $message .= "📅 Tanggal: {$schedule->date->format('d M Y')}\n";
        $message .= "🕐 Waktu: {$schedule->start_time}\n";
        $message .= "⏱️ Durasi: {$appointment->duration} menit\n\n";
        $message .= "Jika ada pertanyaan, hubungi kami.\n\n";
        $message .= "Terima kasih,\nTeman Bicara";

        return $message;
    }
}
```

## WhatsApp Notifications

### WAHA (WhatsApp HTTP API)

**Documentation**: https://waha.devlike.pro/

**What is WAHA?**
- Open-source WhatsApp HTTP API
- Runs as Docker container
- Provides REST API for sending messages
- Requires WhatsApp account to be connected

### Configuration

**Docker Compose** (`docker-compose.yml`):
```yaml
waha:
  image: devlikeapro/waha
  platform: linux/amd64
  container_name: teman-bicara-waha
  restart: unless-stopped
  ports:
    - "3000:3000"
  environment:
    WHATSAPP_API_KEY: asdf
    WAHA_DASHBOARD_ENABLED: true
    WAHA_DASHBOARD_USERNAME: admin
    WAHA_DASHBOARD_PASSWORD: asdf
  volumes:
    - waha_data:/app/.waha
```

**Environment Variables** (`.env`):
```env
WAHA_URL=http://localhost:3000
WAHA_SESSION=default
WAHA_API_KEY=asdf
```

**Services Config** (`config/services.php`):
```php
'waha' => [
    'url' => env('WAHA_URL', 'http://localhost:3000'),
    'session' => env('WAHA_SESSION', 'default'),
    'api_key' => env('WAHA_API_KEY', 'asdf'),
],
```

### Start WAHA

```bash
docker-compose up -d waha
```

### WAHA Dashboard

**Access Dashboard:**
```
http://localhost:3000
```

**Login:**
- Username: `admin`
- Password: `asdf`

**Setup Session:**
1. Go to "Sessions" tab
2. Click "Start New Session"
3. Name it: `default`
4. Scan QR code with WhatsApp mobile app
5. Wait for "Connected" status

### WhatsApp Channel Implementation

**File**: `app/Notifications/Channels/WhatsappChannel.php`

```php
<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        // Get phone number from notifiable (user)
        $phone = $notifiable->phone;

        if (empty($phone)) {
            Log::warning('WhatsApp notification not sent: no phone number', [
                'user_id' => $notifiable->id,
                'notification' => get_class($notification),
            ]);
            return;
        }

        // Format phone number to international format
        $phone = $this->formatPhoneNumber($phone);

        // Get message from notification
        $message = $notification->toWhatsapp($notifiable);

        if (empty($message)) {
            Log::warning('WhatsApp notification not sent: empty message', [
                'user_id' => $notifiable->id,
                'notification' => get_class($notification),
            ]);
            return;
        }

        // Send via WAHA API
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
                    'notification' => get_class($notification),
                ]);
            } else {
                Log::error('WhatsApp notification failed', [
                    'user_id' => $notifiable->id,
                    'phone' => $phone,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp notification exception', [
                'user_id' => $notifiable->id,
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Format phone number to international format.
     *
     * Indonesian format: 62812XXXXXXX
     * WhatsApp format: 62812XXXXXXX@c.us
     */
    private function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to 62 format (Indonesian)
        if (!str_starts_with($phone, '62')) {
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } else {
                $phone = '62' . $phone;
            }
        }

        return $phone;
    }
}
```

### Phone Number Format

**Input Examples:**
- `0812-3456-7890`
- `+62 812 3456 7890`
- `62812 3456 7890`
- `812-3456-7890`

**Output (WhatsApp ID):**
- `6281234567890@c.us`

**Logic:**
1. Remove all non-numeric characters
2. If starts with `0`, replace with `62`
3. If doesn't start with `62`, prepend `62`
4. Append `@c.us` for WhatsApp ID

## Queue System

### Why Use Queues?

**Benefits:**
- Don't block HTTP response while sending notifications
- Retry failed notifications automatically
- Better error handling
- Improved user experience (faster response)

### Setup Queue

**1. Create Queue Table:**
```bash
php artisan queue:table
php artisan migrate
```

**2. Configure Queue Driver** (`.env`):
```env
QUEUE_CONNECTION=database
```

**3. Implement ShouldQueue:**
```php
use Illuminate\Contracts\Queue\ShouldQueue;

class AppointmentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    // ...
}
```

**4. Run Queue Worker:**
```bash
php artisan queue:work
```

**For Development** (auto-reload on code changes):
```bash
php artisan queue:work --tries=3 --timeout=60
```

### Supervisor (Production)

**Install Supervisor:**
```bash
sudo apt-get install supervisor
```

**Create Config** (`/etc/supervisor/conf.d/teman-bicara-worker.conf`):
```ini
[program:teman-bicara-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/teman-bicara/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/teman-bicara/storage/logs/worker.log
stopwaitsecs=3600
```

**Start Supervisor:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start teman-bicara-worker:*
```

## Sending Notifications

### Manual Send

```php
use App\Notifications\AppointmentConfirmed;

// Send to single user
$user->notify(new AppointmentConfirmed($appointment));

// Send to multiple users
Notification::send($users, new AppointmentConfirmed($appointment));
```

### Event-Based Send

**Define Event:**
```php
// app/Events/AppointmentCreated.php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentCreated
{
    use Dispatchable, SerializesModels;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }
}
```

**Define Listener:**
```php
// app/Listeners/SendAppointmentConfirmation.php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Notifications\AppointmentConfirmed;

class SendAppointmentConfirmation
{
    public function handle(AppointmentCreated $event)
    {
        $event->appointment->user->notify(
            new AppointmentConfirmed($event->appointment)
        );
    }
}
```

**Register in EventServiceProvider:**
```php
// app/Providers/EventServiceProvider.php

protected $listen = [
    AppointmentCreated::class => [
        SendAppointmentConfirmation::class,
    ],
];
```

**Fire Event:**
```php
event(new AppointmentCreated($appointment));
```

## Testing Notifications

### Email Testing (Mailhog)

1. **Start Mailhog:**
```bash
docker-compose up -d mailhog
```

2. **Send Test Email:**
```bash
php artisan tinker
```
```php
$user = User::first();
$appointment = Appointment::first();
$user->notify(new \App\Notifications\AppointmentConfirmed($appointment));
```

3. **Check Mailhog:**
```
http://localhost:8025
```

### WhatsApp Testing (WAHA)

1. **Start WAHA:**
```bash
docker-compose up -d waha
```

2. **Connect WhatsApp Session:**
- Go to: http://localhost:3000
- Login with admin/asdf
- Start session and scan QR code

3. **Send Test WhatsApp:**
```bash
php artisan tinker
```
```php
$user = User::where('phone', '!=', null)->first();
$appointment = Appointment::first();
$user->notify(new \App\Notifications\AppointmentConfirmed($appointment));
```

4. **Check Phone:**
- Open WhatsApp on your phone
- Check for message from connected number

### Unit Test Example

```php
// tests/Feature/NotificationTest.php

use App\Models\User;
use App\Models\Appointment;
use App\Notifications\AppointmentConfirmed;
use Illuminate\Support\Facades\Notification;

public function test_appointment_confirmed_notification_is_sent()
{
    Notification::fake();

    $user = User::factory()->create();
    $appointment = Appointment::factory()->create([
        'user_id' => $user->id,
    ]);

    $user->notify(new AppointmentConfirmed($appointment));

    Notification::assertSentTo(
        $user,
        AppointmentConfirmed::class,
        function ($notification, $channels) use ($appointment) {
            return $notification->appointment->id === $appointment->id;
        }
    );
}

public function test_notification_uses_mail_and_whatsapp_channels()
{
    $notification = new AppointmentConfirmed(Appointment::factory()->make());
    $user = User::factory()->make();

    $channels = $notification->via($user);

    $this->assertContains('mail', $channels);
    $this->assertContains(WhatsappChannel::class, $channels);
}
```

## Logging

### Log Configuration

**File**: `config/logging.php`

```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'slack'],
    ],

    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
    ],

    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
        'days' => 14,
    ],
],
```

### View Logs

```bash
tail -f storage/logs/laravel.log
```

### Search Logs for WhatsApp

```bash
grep "WhatsApp" storage/logs/laravel.log
```

### Log Levels

- **INFO**: Successful sends
- **WARNING**: Missing phone number, empty message
- **ERROR**: API failures, exceptions

## Troubleshooting

### Email Not Received

**1. Check Mailhog is Running:**
```bash
docker-compose ps mailhog
```

**2. Check .env Settings:**
```env
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

**3. Check Laravel Logs:**
```bash
tail -f storage/logs/laravel.log
```

**4. Test SMTP Connection:**
```bash
telnet 127.0.0.1 1025
```

### WhatsApp Not Sent

**1. Check WAHA is Running:**
```bash
docker-compose ps waha
```

**2. Check Session Connected:**
- Go to http://localhost:3000
- Verify session status is "WORKING"

**3. Check Phone Number Format:**
```bash
php artisan tinker
```
```php
$user = User::find(1);
echo $user->phone; // Should be 62812...
```

**4. Check Logs:**
```bash
grep "WhatsApp" storage/logs/laravel.log
```

**5. Test API Directly:**
```bash
curl -X POST http://localhost:3000/api/sendText \
  -H "X-Api-Key: asdf" \
  -H "Content-Type: application/json" \
  -d '{
    "session": "default",
    "chatId": "628123456789@c.us",
    "text": "Test message"
  }'
```

### Queue Not Processing

**1. Check Queue Worker Running:**
```bash
ps aux | grep queue:work
```

**2. Check Failed Jobs:**
```bash
php artisan queue:failed
```

**3. Retry Failed Jobs:**
```bash
php artisan queue:retry all
```

**4. Clear Failed Jobs:**
```bash
php artisan queue:flush
```

## Best Practices

1. **Always Use Queues**: Never send notifications synchronously
2. **Implement ShouldQueue**: Make all notifications queueable
3. **Validate Phone Numbers**: Check phone exists before sending
4. **Log Everything**: Log success, warnings, and errors
5. **Use Notification Facade**: For sending to multiple users
6. **Test in Development**: Use Mailhog and WAHA before production
7. **Handle Failures Gracefully**: Don't fail entire process if notification fails
8. **Keep Messages Short**: WhatsApp messages should be concise
9. **Use Emojis Wisely**: Make WhatsApp messages friendly but professional
10. **Monitor Queue**: Set up alerts for failed jobs

## Next Documentation

- [07-PROFESSIONAL-FEATURES.md](07-PROFESSIONAL-FEATURES.md) - Features for professionals
- [08-ARTICLE-SYSTEM.md](08-ARTICLE-SYSTEM.md) - Article/blog management
