# Booking Flow - Teman Bicara

## Overview

Booking flow di Teman Bicara adalah proses lengkap dari user memilih professional hingga appointment terkonfirmasi dengan notifikasi yang dikirim.

## Complete Booking Journey

```
┌─────────────────┐
│ Browse          │
│ Professionals   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ View Professional│
│ Detail & Schedule│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Select Schedule │
│ & Add to Cart   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ View Cart       │
│ & Review Items  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Checkout        │
│ (Review Total)  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Process Payment │
│ (Demo - Auto OK)│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Create          │
│ Appointment     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Send            │
│ Notifications   │
│ (Email+WhatsApp)│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Redirect to     │
│ My Appointments │
└─────────────────┘
```

## Step 1: Browse Professionals

### Route
```
GET /professionals
```

### Features

**Filter & Search:**
- Specialization filter (psychiatrist, psychologist, conversationalist)
- Search by name
- Price range filter (optional)

**Professional Card Shows:**
- Profile photo
- Name
- Specialization badge
- Years of experience
- Rating (if implemented)
- Price per 30 minutes
- Price per 60 minutes
- "Lihat Detail" button

### Implementation

```php
// app/Http/Controllers/ProfessionalController.php

public function index(Request $request)
{
    $query = Professional::query();

    // Filter by specialization
    if ($request->filled('specialization')) {
        $query->where('specialization', $request->specialization);
    }

    // Search by name
    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    $professionals = $query->with('user')->paginate(12);

    return view('professionals.index', compact('professionals'));
}
```

## Step 2: View Professional Detail

### Route
```
GET /professionals/{professional}
```

### Shows

**Professional Information:**
- Full name
- Specialization
- Bio/description
- Years of experience
- Education background
- Pricing for 30 & 60 minutes

**Available Schedules:**
- Calendar view (optional) or list view
- Date & time slots
- Duration options (30 min / 60 min)
- "Tambah ke Keranjang" button for each slot

### Implementation

```php
// app/Http/Controllers/ProfessionalController.php

public function show(Professional $professional)
{
    // Get available schedules (future dates only, not booked)
    $schedules = Schedule::where('professional_id', $professional->id)
        ->where('date', '>=', now()->toDateString())
        ->where('is_available', true)
        ->orderBy('date')
        ->orderBy('start_time')
        ->get();

    return view('professionals.show', compact('professional', 'schedules'));
}
```

## Step 3: Add to Cart

### Route
```
POST /cart
```

### Request Data

```php
[
    'schedule_id' => 123,
    'duration' => 30, // or 60
]
```

### Validation Rules

```php
$request->validate([
    'schedule_id' => 'required|exists:schedules,id',
    'duration' => 'required|in:30,60',
]);
```

### Business Logic

1. **Check Authentication:**
   - User must be logged in
   - User must have verified email

2. **Check Schedule Availability:**
   - Schedule must exist
   - Schedule must be available
   - Schedule date must be in the future

3. **Check Duplicate:**
   - User cannot add same schedule twice
   - User cannot add overlapping schedules (optional)

4. **Calculate Price:**
   - Based on duration (30 or 60 minutes)
   - Get price from professional model

5. **Create Cart Item:**
   - Store schedule_id, user_id, duration, price

### Implementation

```php
// app/Http/Controllers/CartController.php

public function store(Request $request)
{
    $validated = $request->validate([
        'schedule_id' => 'required|exists:schedules,id',
        'duration' => 'required|in:30,60',
    ]);

    $schedule = Schedule::findOrFail($validated['schedule_id']);

    // Check availability
    if (!$schedule->is_available) {
        return back()->with('error', 'Jadwal tidak tersedia');
    }

    // Check duplicate
    $exists = Cart::where('user_id', auth()->id())
        ->where('schedule_id', $schedule->id)
        ->exists();

    if ($exists) {
        return back()->with('error', 'Jadwal sudah ada di keranjang');
    }

    // Calculate price
    $price = $validated['duration'] == 30
        ? $schedule->professional->price_30
        : $schedule->professional->price_60;

    // Create cart item
    Cart::create([
        'user_id' => auth()->id(),
        'schedule_id' => $schedule->id,
        'professional_id' => $schedule->professional_id,
        'duration' => $validated['duration'],
        'price' => $price,
    ]);

    return redirect()->route('cart.index')
        ->with('success', 'Berhasil ditambahkan ke keranjang');
}
```

## Step 4: View Cart

### Route
```
GET /cart
```

### Shows

**Cart Items:**
- Professional name
- Professional specialization
- Appointment date & time
- Duration (30 or 60 minutes)
- Price
- Remove button

**Cart Summary:**
- Subtotal
- Total items
- Total price
- "Lanjut ke Checkout" button

### Features

- Remove items from cart
- Empty cart state
- Continue shopping link

### Implementation

```php
// app/Http/Controllers/CartController.php

public function index()
{
    $carts = Cart::where('user_id', auth()->id())
        ->with(['professional.user', 'schedule'])
        ->get();

    $total = $carts->sum('price');

    return view('cart.index', compact('carts', 'total'));
}

public function destroy(Cart $cart)
{
    // Authorization check
    if ($cart->user_id !== auth()->id()) {
        abort(403);
    }

    $cart->delete();

    return back()->with('success', 'Item berhasil dihapus');
}
```

## Step 5: Checkout

### Route
```
GET /checkout
```

### Shows

**Order Summary:**
- All cart items with details
- Professional info
- Schedule details
- Duration & price per item
- Grand total

**User Information:**
- Name (from auth user)
- Email (from auth user)
- Phone (from auth user, editable if empty)

**Payment Information:**
- Payment method: Demo (auto-success)
- Note about demo payment

### Validation

- Cart must not be empty
- All schedules must still be available
- User phone must be filled (for WhatsApp notification)

### Implementation

```php
// app/Http/Controllers/PaymentController.php

public function checkout()
{
    $carts = Cart::where('user_id', auth()->id())
        ->with(['professional.user', 'schedule'])
        ->get();

    if ($carts->isEmpty()) {
        return redirect()->route('cart.index')
            ->with('error', 'Keranjang kosong');
    }

    // Check if all schedules still available
    foreach ($carts as $cart) {
        if (!$cart->schedule->is_available) {
            return redirect()->route('cart.index')
                ->with('error', 'Beberapa jadwal sudah tidak tersedia');
        }
    }

    $total = $carts->sum('price');

    return view('payment.checkout', compact('carts', 'total'));
}
```

## Step 6: Process Payment

### Route
```
POST /payment/process
```

### Request Data

```php
[
    'phone' => '08123456789', // optional, if not set in profile
]
```

### Payment Flow (Demo)

Since this is a **demo payment system**, all payments auto-succeed:

1. Create Payment record with status 'success'
2. Create Appointments for each cart item
3. Mark schedules as unavailable
4. Generate video chat links for each appointment
5. Clear user's cart
6. Send notifications (Email + WhatsApp)
7. Redirect to appointments page

### Implementation

```php
// app/Http/Controllers/PaymentController.php

public function process(Request $request)
{
    $carts = Cart::where('user_id', auth()->id())
        ->with(['professional', 'schedule'])
        ->get();

    if ($carts->isEmpty()) {
        return redirect()->route('cart.index')
            ->with('error', 'Keranjang kosong');
    }

    $total = $carts->sum('price');

    DB::beginTransaction();

    try {
        // 1. Create Payment
        $payment = Payment::create([
            'user_id' => auth()->id(),
            'amount' => $total,
            'status' => 'success',
            'payment_method' => 'demo',
            'transaction_id' => 'DEMO-' . strtoupper(Str::random(10)),
        ]);

        // 2. Create Appointments
        foreach ($carts as $cart) {
            $appointment = Appointment::create([
                'user_id' => auth()->id(),
                'professional_id' => $cart->professional_id,
                'schedule_id' => $cart->schedule_id,
                'payment_id' => $payment->id,
                'duration' => $cart->duration,
                'price' => $cart->price,
                'status' => 'confirmed',
                'video_link' => 'https://meet.jit.si/' . Str::random(20),
                'notes' => null,
            ]);

            // 3. Mark schedule as unavailable
            $cart->schedule->update(['is_available' => false]);

            // 4. Send notifications
            auth()->user()->notify(
                new AppointmentConfirmed($appointment)
            );
        }

        // 5. Clear cart
        Cart::where('user_id', auth()->id())->delete();

        DB::commit();

        return redirect()->route('appointments.index')
            ->with('success', 'Pembayaran berhasil! Appointment Anda telah dikonfirmasi.');

    } catch (\Exception $e) {
        DB::rollBack();

        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
```

## Step 7: Appointment Created

### Appointment Record

**Fields:**
- `id` - Unique ID
- `user_id` - Client
- `professional_id` - Professional
- `schedule_id` - Schedule reference
- `payment_id` - Payment reference
- `duration` - 30 or 60 minutes
- `price` - Final price
- `status` - 'confirmed', 'completed', 'cancelled'
- `video_link` - Jitsi Meet or Google Meet URL
- `notes` - Optional notes

### Video Link Generation

```php
// Generate unique Jitsi Meet link
$videoLink = 'https://meet.jit.si/' . Str::random(20);

// Or Google Meet format
$videoLink = 'https://meet.google.com/' . Str::random(12);
```

## Step 8: Notifications

### Email Notification

**Sent to:** User (client)

**Subject:** "Appointment Confirmed - Teman Bicara"

**Content:**
- Greeting
- Appointment details:
  - Professional name
  - Specialization
  - Date & time
  - Duration
  - Video link
- Instructions to join video call
- Footer

**Implementation:**

```php
// app/Notifications/AppointmentConfirmed.php

public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Appointment Confirmed - Teman Bicara')
        ->greeting('Halo ' . $notifiable->name . '!')
        ->line('Appointment Anda telah dikonfirmasi.')
        ->line('**Detail Appointment:**')
        ->line('Professional: ' . $this->appointment->professional->user->name)
        ->line('Tanggal: ' . $this->appointment->schedule->date->format('d M Y'))
        ->line('Waktu: ' . $this->appointment->schedule->start_time)
        ->line('Durasi: ' . $this->appointment->duration . ' menit')
        ->action('Join Video Call', $this->appointment->video_link)
        ->line('Terima kasih telah menggunakan Teman Bicara!');
}
```

### WhatsApp Notification

**Sent to:** User's phone number

**Message Format:**

```
Halo [User Name],

Appointment Anda telah dikonfirmasi!

Detail:
👨‍⚕️ Professional: [Professional Name]
📅 Tanggal: [Date]
🕐 Waktu: [Time]
⏱️ Durasi: [Duration] menit

🔗 Video Link:
[Video Link]

Terima kasih,
Teman Bicara
```

**Implementation:**

```php
// app/Notifications/AppointmentConfirmed.php

public function toWhatsapp($notifiable)
{
    $appointment = $this->appointment;
    $schedule = $appointment->schedule;
    $professional = $appointment->professional;

    $message = "Halo {$notifiable->name},\n\n";
    $message .= "Appointment Anda telah dikonfirmasi!\n\n";
    $message .= "Detail:\n";
    $message .= "👨‍⚕️ Professional: {$professional->user->name}\n";
    $message .= "📅 Tanggal: {$schedule->date->format('d M Y')}\n";
    $message .= "🕐 Waktu: {$schedule->start_time}\n";
    $message .= "⏱️ Durasi: {$appointment->duration} menit\n\n";
    $message .= "🔗 Video Link:\n{$appointment->video_link}\n\n";
    $message .= "Terima kasih,\nTeman Bicara";

    return $message;
}

public function via($notifiable)
{
    return ['mail', WhatsappChannel::class];
}
```

## Step 9: My Appointments

### Route
```
GET /appointments
```

### Shows

**Appointment List:**

Grouped by status:
- **Upcoming** (confirmed, future dates)
- **Completed** (past dates or status = completed)
- **Cancelled** (status = cancelled)

Each appointment shows:
- Professional info
- Date & time
- Duration
- Status badge
- Video link (for upcoming)
- Cancel button (for upcoming, if allowed)

### Implementation

```php
// app/Http/Controllers/AppointmentController.php

public function index()
{
    $appointments = Appointment::where('user_id', auth()->id())
        ->with(['professional.user', 'schedule'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // Separate by status
    $upcoming = $appointments->filter(function ($appointment) {
        return $appointment->status === 'confirmed'
            && $appointment->schedule->date >= now()->toDateString();
    });

    $completed = $appointments->filter(function ($appointment) {
        return $appointment->status === 'completed'
            || ($appointment->status === 'confirmed'
                && $appointment->schedule->date < now()->toDateString());
    });

    $cancelled = $appointments->filter(function ($appointment) {
        return $appointment->status === 'cancelled';
    });

    return view('appointments.index', compact(
        'appointments', 'upcoming', 'completed', 'cancelled'
    ));
}
```

## Cancellation Flow

### Route
```
POST /appointments/{appointment}/cancel
```

### Business Rules

- Only upcoming appointments can be cancelled
- Cancellation must be at least X hours before appointment (configurable)
- When cancelled:
  - Appointment status = 'cancelled'
  - Schedule becomes available again
  - Refund process (if implemented, currently demo payment)
  - Send cancellation notification

### Implementation

```php
// app/Http/Controllers/AppointmentController.php

public function cancel(Appointment $appointment)
{
    // Authorization
    if ($appointment->user_id !== auth()->id()) {
        abort(403);
    }

    // Check status
    if ($appointment->status !== 'confirmed') {
        return back()->with('error', 'Appointment tidak dapat dibatalkan');
    }

    // Check date (must be future)
    if ($appointment->schedule->date < now()->toDateString()) {
        return back()->with('error', 'Appointment sudah lewat');
    }

    DB::beginTransaction();

    try {
        // Update appointment status
        $appointment->update(['status' => 'cancelled']);

        // Make schedule available again
        $appointment->schedule->update(['is_available' => true]);

        // Send cancellation notification
        auth()->user()->notify(new AppointmentCancelled($appointment));

        DB::commit();

        return back()->with('success', 'Appointment berhasil dibatalkan');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal membatalkan appointment');
    }
}
```

## Error Handling

### Common Errors

**1. Schedule No Longer Available**

```php
if (!$schedule->is_available) {
    return back()->with('error', 'Jadwal sudah tidak tersedia');
}
```

**2. Duplicate Cart Item**

```php
$exists = Cart::where('user_id', auth()->id())
    ->where('schedule_id', $schedule->id)
    ->exists();

if ($exists) {
    return back()->with('error', 'Jadwal sudah ada di keranjang');
}
```

**3. Empty Cart at Checkout**

```php
if ($carts->isEmpty()) {
    return redirect()->route('cart.index')
        ->with('error', 'Keranjang kosong');
}
```

**4. Payment Transaction Failure**

```php
try {
    // Payment logic
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    return back()->with('error', 'Pembayaran gagal: ' . $e->getMessage());
}
```

## Queue Jobs for Notifications

### Setup Queue

```bash
php artisan queue:table
php artisan migrate
```

### Configure Queue Driver

```env
QUEUE_CONNECTION=database
```

### Run Queue Worker

```bash
php artisan queue:work
```

### Notification Job

Notifications are automatically queued by Laravel when using `ShouldQueue` interface:

```php
// app/Notifications/AppointmentConfirmed.php

use Illuminate\Contracts\Queue\ShouldQueue;

class AppointmentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    // ...
}
```

## Testing the Flow

### Manual Test

1. **Login as User:**
```
Email: user@example.com
Password: password
```

2. **Browse Professionals:**
- Go to: http://localhost:8000/professionals
- Click "Lihat Detail" on any professional

3. **Add to Cart:**
- Select a schedule
- Choose duration (30 or 60 minutes)
- Click "Tambah ke Keranjang"

4. **View Cart:**
- Go to: http://localhost:8000/cart
- Verify items are listed
- Check total price

5. **Checkout:**
- Click "Lanjut ke Checkout"
- Review order summary
- Click "Bayar Sekarang"

6. **Check Appointment:**
- Should redirect to /appointments
- See confirmed appointment
- Check video link

7. **Check Notifications:**
- Email: http://localhost:8025 (Mailhog)
- WhatsApp: Check WAHA dashboard logs

### Programmatic Test

```php
// tests/Feature/BookingFlowTest.php

public function test_user_can_complete_booking_flow()
{
    $user = User::factory()->create();
    $professional = Professional::factory()->create();
    $schedule = Schedule::factory()->create([
        'professional_id' => $professional->id,
        'is_available' => true,
    ]);

    // Add to cart
    $response = $this->actingAs($user)->post('/cart', [
        'schedule_id' => $schedule->id,
        'duration' => 30,
    ]);

    $response->assertRedirect(route('cart.index'));
    $this->assertDatabaseHas('carts', [
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
    ]);

    // Process payment
    $response = $this->actingAs($user)->post('/payment/process');

    $response->assertRedirect(route('appointments.index'));
    $this->assertDatabaseHas('appointments', [
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'status' => 'confirmed',
    ]);

    // Check schedule is no longer available
    $this->assertDatabaseHas('schedules', [
        'id' => $schedule->id,
        'is_available' => false,
    ]);

    // Check cart is cleared
    $this->assertDatabaseMissing('carts', [
        'user_id' => $user->id,
    ]);
}
```

## Best Practices

1. **Use Database Transactions**: Wrap payment + appointment creation in DB transaction
2. **Validate Availability**: Always check schedule availability before booking
3. **Queue Notifications**: Don't send notifications synchronously, use queues
4. **Generate Unique Links**: Use random strings for video links
5. **Authorization Checks**: Always verify user owns the cart/appointment
6. **Error Handling**: Use try-catch for payment processing
7. **Clear Feedback**: Show success/error messages to user
8. **Log Important Events**: Log payment success/failure, notification sent/failed

## Next Documentation

- [06-NOTIFICATIONS.md](06-NOTIFICATIONS.md) - Email & WhatsApp notification details
- [07-PROFESSIONAL-FEATURES.md](07-PROFESSIONAL-FEATURES.md) - Features for professionals
