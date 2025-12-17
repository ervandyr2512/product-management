# Authentication & Email Verification - Teman Bicara

## Overview

Teman Bicara menggunakan **Laravel Breeze** untuk authentication system dengan **Email Verification Wajib**. User harus verifikasi email sebelum bisa mengakses fitur-fitur protected.

## Authentication Flow

```
┌──────────────┐
│  Register    │
└──────┬───────┘
       │
       ▼
┌──────────────┐     ┌─────────────────┐
│ Auto Login   │────▶│ Send Verification│
└──────┬───────┘     │     Email       │
       │             └─────────────────┘
       ▼
┌──────────────────┐
│ Redirect to      │
│ Verify Email Page│
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ User Checks Email│
│   (Mailhog)      │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Click Verify Link│
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│  Email Verified  │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Access Dashboard │
│  & All Features  │
└──────────────────┘
```

## User Model Implementation

### MustVerifyEmail Interface

```php
// app/Models/User.php

use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    // ...
}
```

### Key Attributes

- `email_verified_at`: Timestamp ketika email diverifikasi
- Null = belum verified
- Not null = sudah verified

## Routes & Middleware

### Public Routes (No Authentication)
```php
Route::get('/', 'home');
Route::get('/professionals', 'professionals.index');
Route::get('/professionals/{professional}', 'professionals.show');
Route::get('/articles', 'articles.index');
Route::get('/articles/{slug}', 'articles.show');
Route::get('/about', 'about');
Route::get('/contact', 'contact');
```

### Auth Routes (Login Required, Not Verified OK)
```php
Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::post('email/verification-notification',
        [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});
```

### Protected Routes (Login + Verified Required)
```php
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart/{cart}', [CartController::class, 'destroy']);

    // Payment
    Route::get('/checkout', [PaymentController::class, 'checkout']);
    Route::post('/payment/process', [PaymentController::class, 'process']);

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);

    // Professional Schedules
    Route::prefix('professional')->group(function () {
        Route::get('/schedules', [ScheduleController::class, 'index']);
        Route::get('/schedules/create', [ScheduleController::class, 'create']);
        Route::post('/schedules', [ScheduleController::class, 'store']);
        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy']);
    });
});
```

## Email Verification

### Verification Email

**Trigger**: Setelah user register

**Content**:
- Subject: "Verify Email Address"
- Button: "Verify Email Address"
- Alternative text link
- Auto-generated oleh Laravel

**Laravel Default Template**:
`vendor/laravel/framework/src/Illuminate/Auth/Notifications/VerifyEmail.php`

### Verify Email Page

**Route**: `/verify-email`

**File**: `resources/views/auth/verify-email.blade.php`

**Features**:
- Email icon dengan purple background
- Informative message
- Success alert ketika email re-sent
- Button "Kirim Ulang Email Verifikasi"
- Button "Logout"
- Help text tentang spam folder

### Verification Link Format

```
http://localhost:8000/verify-email/{id}/{hash}?expires=...&signature=...
```

**Parameters**:
- `id`: User ID
- `hash`: SHA-1 hash dari email
- `expires`: Unix timestamp
- `signature`: Signed URL signature

**Middleware**: `signed`, `throttle:6,1`

### Resend Verification

**Route**: `POST /email/verification-notification`

**Throttle**: 6 attempts per minute

**Response**: Redirect back dengan session flash `status = 'verification-link-sent'`

## Registration Flow

### 1. Registration Form

**Route**: `/register`

**Fields**:
- Name (required)
- Email (required, unique)
- Password (required, min:8)
- Password Confirmation (required, same as password)
- Phone (optional) - dapat ditambahkan manual

**Features**:
- Info notice tentang email verification
- Blue alert box dengan icon
- Modern UI dengan purple theme

### 2. After Registration

```php
// RegisteredUserController.php

public function store(Request $request)
{
    // Validate
    $request->validate([...]);

    // Create user
    $user = User::create([...]);

    // Fire Registered event (sends verification email)
    event(new Registered($user));

    // Auto login
    Auth::login($user);

    // Redirect to dashboard
    // Middleware will redirect to verification page
    return redirect(route('dashboard', absolute: false));
}
```

## Authorization Checks

### Check if Email Verified

```php
// In controller
if (!Auth::user()->hasVerifiedEmail()) {
    return redirect()->route('verification.notice');
}

// In Blade
@if(Auth::user()->hasVerifiedEmail())
    <!-- Show content -->
@else
    <!-- Show verification required message -->
@endif
```

### Manually Verify Email (Seeder)

```php
// In DatabaseSeeder.php
User::factory()->create([
    'email' => 'user@example.com',
    'email_verified_at' => now(), // Already verified
]);
```

## Middleware Behavior

### `auth` Middleware

- Checks if user is logged in
- If not, redirect to `/login`

### `verified` Middleware

- Checks if user's email is verified
- If not verified, redirect to `/verify-email`
- Only works with authenticated users

### Combined `auth`, `verified`

```php
Route::middleware(['auth', 'verified'])->group(function () {
    // These routes require login AND email verification
});
```

## Testing Email Verification

### Manual Test

1. **Start Services**:
```bash
docker-compose up -d
php artisan serve
```

2. **Register New User**:
- Go to: http://localhost:8000/register
- Fill form with any email
- Submit

3. **Check Verification Page**:
- Should auto-redirect to `/verify-email`
- See message about verification

4. **Check Email in Mailhog**:
- Open: http://localhost:8025
- Find email "Verify Email Address"
- Click verification link

5. **Verified**:
- Should redirect to dashboard
- Can now access all protected features

### Programmatic Test

```php
// tests/Feature/EmailVerificationTest.php

public function test_email_verification_screen_can_be_rendered()
{
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertStatus(200);
}

public function test_email_can_be_verified()
{
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    $this->assertTrue($user->fresh()->hasVerifiedEmail());
    $response->assertRedirect('/dashboard');
}
```

## Security Features

### 1. Signed URLs

Verification links menggunakan signed URLs untuk mencegah tampering:

```php
URL::signedRoute('verification.verify', [
    'id' => $user->id,
    'hash' => sha1($user->email)
]);
```

### 2. Rate Limiting

Resend verification email dibatasi 6 attempts per minute:

```php
->middleware('throttle:6,1')
```

### 3. Hash Validation

Email hash di-validate untuk memastikan link match dengan user:

```php
if (! hash_equals((string) $request->route('hash'), sha1($user->email))) {
    // Invalid link
}
```

### 4. Expiration

Verification links expire setelah 60 menit (configurable):

```php
URL::temporarySignedRoute(
    'verification.verify',
    now()->addMinutes(60), // Expiration
    [...]
);
```

## Customization

### Custom Verification Email

Create notification:

```bash
php artisan make:notification CustomVerifyEmail
```

```php
// app/Notifications/CustomVerifyEmail.php

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class CustomVerifyEmail extends BaseVerifyEmail
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verifikasi Email Anda - Teman Bicara')
            ->line('Terima kasih telah mendaftar!')
            ->action('Verifikasi Email', $this->verificationUrl($notifiable))
            ->line('Link ini akan expired dalam 60 menit.');
    }
}
```

Override in User model:

```php
public function sendEmailVerificationNotification()
{
    $this->notify(new CustomVerifyEmail);
}
```

### Custom Redirect After Verification

In `app/Providers/AuthServiceProvider.php`:

```php
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

VerifyEmail::toMailUsing(function ($notifiable, $url) {
    return (new MailMessage)
        ->subject('Verify Email Address')
        ->line('Click button to verify.')
        ->action('Verify', $url);
});
```

### Custom Verification Page

Edit: `resources/views/auth/verify-email.blade.php`

## Troubleshooting

### Email Not Received

1. Check Mailhog is running:
```bash
docker-compose ps
```

2. Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

3. Verify .env settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

### Verification Link Invalid

1. Check if link expired (60 minutes)
2. Resend verification email
3. Clear cache:
```bash
php artisan config:clear
php artisan route:clear
```

### Already Verified Error

User already verified, no action needed. Can login normally.

## Best Practices

1. **Always Use Middleware**: Protect routes with `verified` middleware
2. **Clear Instructions**: Show clear message on verification page
3. **Easy Resend**: Make it easy to resend verification email
4. **Mobile Friendly**: Ensure verification page works on mobile
5. **Error Handling**: Handle expired/invalid links gracefully
6. **Testing**: Always test verification flow after changes

## Next Documentation

- [05-BOOKING-FLOW.md](05-BOOKING-FLOW.md) - Complete booking process
- [06-NOTIFICATIONS.md](06-NOTIFICATIONS.md) - Email & WhatsApp notifications
