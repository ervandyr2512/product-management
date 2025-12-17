# Troubleshooting Guide

This guide helps you diagnose and fix common issues in the Teman Bicara platform.

---

## Table of Contents

1. [Notification Issues](#notification-issues)
2. [Payment Issues](#payment-issues)
3. [WhatsApp Integration Issues](#whatsapp-integration-issues)
4. [Database Issues](#database-issues)
5. [Authentication Issues](#authentication-issues)
6. [Performance Issues](#performance-issues)

---

## Notification Issues

### Issue: Email Not Received After Payment

#### Symptoms
- Payment completes successfully
- No email in MailHog or inbox
- No error messages visible

#### Diagnosis
Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

Look for:
```
[ERROR] Failed to send email notification
```

#### Common Causes & Solutions

**1. MailHog Not Running**

Check if MailHog is running:
```bash
curl http://127.0.0.1:8025
```

Start MailHog:
```bash
# Using Docker
docker-compose up -d mailhog

# Or standalone
mailhog
```

**2. Wrong Mail Configuration**

Check `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@temanbicara.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Clear config cache:
```bash
php artisan config:clear
php artisan cache:clear
```

**3. Queue Not Processing**

If using queues, ensure worker is running:
```bash
php artisan queue:work
```

Or check if notification has `ShouldQueue`:
```php
// In app/Notifications/AppointmentConfirmed.php
// Should NOT implement ShouldQueue for synchronous sending
class AppointmentConfirmed extends Notification // Remove: implements ShouldQueue
```

---

### Issue: WhatsApp Notification Not Received

#### Symptoms
- Email received but no WhatsApp message
- Payment completes successfully
- User has valid phone number

#### Diagnosis

**Step 1: Check Laravel Logs**
```bash
grep "WhatsApp" storage/logs/laravel.log | tail -20
```

Look for:
```
[INFO] WhatsApp Notification Debug
[ERROR] WhatsApp API returned error
[WARNING] WhatsApp notification failed but payment succeeded
```

**Step 2: Check WAHA Service**
```bash
# Check if WAHA is running
curl http://localhost:3000/api/sessions

# Check specific session
curl -H "X-Api-Key: your_api_key" http://localhost:3000/api/sessions/default
```

**Step 3: Test Direct API Call**
```bash
curl -X POST http://localhost:3000/api/sendText \
  -H "X-Api-Key: your_api_key" \
  -H "Content-Type: application/json" \
  -d '{
    "session": "default",
    "chatId": "6281234567890@c.us",
    "text": "Test message"
  }'
```

#### Common Causes & Solutions

**1. WAHA Service Not Running**

Start WAHA:
```bash
docker-compose up -d waha
```

Or using Docker directly:
```bash
docker run -d -p 3000:3000 \
  --name waha \
  -e WAHA_API_KEY=your_api_key \
  devlikeapro/waha
```

**2. Invalid API Key**

Check `.env`:
```env
WAHA_API_KEY=your_actual_api_key
```

Test authentication:
```bash
curl -H "X-Api-Key: your_api_key" http://localhost:3000/api/sessions
```

Should return 200, not 401.

**3. Session Not Started**

Check session status:
```bash
curl -H "X-Api-Key: your_api_key" \
  http://localhost:3000/api/sessions/default
```

If not started, scan QR code:
```bash
# Get QR code
curl -H "X-Api-Key: your_api_key" \
  http://localhost:3000/api/sessions/default/start
```

Scan the QR code with WhatsApp mobile app.

**4. Wrong Phone Format**

Phone numbers must be in international format WITHOUT leading zeros:

❌ Wrong:
- `081234567890` (starts with 0)
- `+62 812 3456 7890` (has spaces/plus)
- `812-3456-7890` (has dashes)

✅ Correct:
- `6281234567890` (country code + number)
- `601234567890` (Malaysia)
- `6581234567` (Singapore)

Fix in database:
```sql
UPDATE users
SET phone = CONCAT('62', SUBSTRING(phone, 2))
WHERE phone LIKE '08%';
```

**5. First Message to New Number**

WAHA may fail on first message with error:
```
"Cannot read properties of undefined (reading 'getChat')"
```

**Solution:** This is expected behavior. The notification system is non-blocking, so payment still succeeds. Subsequent messages work fine.

Test manually:
```bash
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $appointment = $user->appointments()->first();
>>> $user->notify(new \App\Notifications\AppointmentConfirmed($appointment));
```

---

## Payment Issues

### Issue: Payment Timeout (Maximum execution time exceeded)

#### Symptoms
- Error: "Maximum execution time of 30 seconds exceeded"
- Payment page hangs/crashes
- Browser shows timeout error

#### Diagnosis
Check logs:
```bash
grep "Maximum execution time" storage/logs/laravel.log
```

#### Common Causes & Solutions

**1. WhatsApp API Hanging**

The notification system has a 10-second timeout. If error persists, check:

In [app/Notifications/AppointmentConfirmed.php](../app/Notifications/AppointmentConfirmed.php):
```php
$response = Http::timeout(10) // Should have timeout
    ->withHeaders(['X-Api-Key' => config('services.waha.api_key')])
    ->post(config('services.waha.url') . '/api/sendText', [
        // ...
    ]);
```

**2. Database Lock**

Check for long-running queries:
```bash
php artisan tinker
>>> DB::enableQueryLog();
>>> // Do your action
>>> DB::getQueryLog();
```

**3. Increase PHP Timeout (Temporary)**

In `php.ini` or `.htaccess`:
```ini
max_execution_time = 60
```

⚠️ **Warning:** This is a workaround. The real solution is to optimize the code or use queue workers.

---

### Issue: Payment Not Confirmed

#### Symptoms
- Money deducted but appointment status still "pending"
- No notification received
- Cart still shows items

#### Diagnosis

**Step 1: Check Appointment Status**
```bash
php artisan tinker
>>> $appointment = App\Models\Appointment::latest()->first();
>>> $appointment->status; // Should be "confirmed"
>>> $appointment->payment; // Should exist
```

**Step 2: Check Payment Record**
```bash
>>> $payment = App\Models\Payment::latest()->first();
>>> $payment->status; // Should be "completed"
>>> $payment->appointment_id; // Should match appointment
```

#### Solutions

**Manual Fix:**
```bash
php artisan tinker
>>> $appointment = App\Models\Appointment::find(123);
>>> $appointment->update(['status' => 'confirmed']);
>>> $appointment->user->notify(new \App\Notifications\AppointmentConfirmed($appointment));
```

**Prevent Future Issues:**

Ensure [app/Http/Controllers/PaymentController.php](../app/Http/Controllers/PaymentController.php) has:
```php
DB::transaction(function () use ($request, $cart) {
    // Create payment
    $payment = Payment::create([...]);

    // Update appointment
    $appointment->update(['status' => 'confirmed']);

    // Clear cart
    $cart->delete();

    // Send notification
    $user->notify(new AppointmentConfirmed($appointment));
});
```

---

## WhatsApp Integration Issues

### Issue: WAHA Returns 500 Error

#### Error Messages

**1. "No LID for user"**
```json
{
  "statusCode": 500,
  "exception": {
    "message": "No LID for user"
  }
}
```

**Cause:** Phone number not found in WhatsApp or incorrect format.

**Solution:**
- Verify phone number is registered on WhatsApp
- Ensure format is correct: `6281234567890@c.us`
- Test with your own number first

**2. "Cannot read properties of undefined (reading 'getChat')"**

**Cause:** WAHA session needs warm-up or chat doesn't exist yet.

**Solution:**
- Wait 5-10 seconds and retry
- Send a test message first to create the chat
- Check WAHA logs: `docker logs waha`

**3. "Session not found"**

**Cause:** Session name mismatch or not started.

**Solution:**
```bash
# List all sessions
curl -H "X-Api-Key: your_api_key" http://localhost:3000/api/sessions

# Start session
curl -X POST -H "X-Api-Key: your_api_key" \
  http://localhost:3000/api/sessions/default/start
```

Update `.env`:
```env
WAHA_SESSION=default  # Must match session name
```

---

### Issue: WhatsApp Session Disconnected

#### Symptoms
- WhatsApp worked before but suddenly stopped
- Error: "Session not authenticated"
- QR code required again

#### Solutions

**1. Check Session Status**
```bash
curl -H "X-Api-Key: your_api_key" \
  http://localhost:3000/api/sessions/default
```

Look for `status: "WORKING"`.

**2. Restart Session**
```bash
# Stop session
curl -X POST -H "X-Api-Key: your_api_key" \
  http://localhost:3000/api/sessions/default/stop

# Start again and scan QR
curl -X POST -H "X-Api-Key: your_api_key" \
  http://localhost:3000/api/sessions/default/start
```

**3. Check WAHA Logs**
```bash
docker logs waha -f
```

Look for disconnection reasons.

---

## Database Issues

### Issue: SQLite Database Locked

#### Error
```
SQLSTATE[HY000]: General error: 5 database is locked
```

#### Causes & Solutions

**1. Multiple Processes**
- Close all `php artisan serve` instances
- Close database GUI tools (DB Browser, TablePlus)
- Restart server

**2. Upgrade to PostgreSQL/MySQL**

For production, use a proper database:

```env
# PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=teman_bicara
DB_USERNAME=postgres
DB_PASSWORD=password

# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=teman_bicara
DB_USERNAME=root
DB_PASSWORD=password
```

Run migrations:
```bash
php artisan migrate:fresh --seed
```

---

### Issue: Migration Failed

#### Common Errors

**1. "Table already exists"**
```bash
php artisan migrate:rollback
php artisan migrate
```

Or fresh install:
```bash
php artisan migrate:fresh --seed
```

⚠️ **Warning:** This deletes all data!

**2. "Column not found"**
```bash
php artisan migrate:status  # Check which migrations ran
php artisan migrate:fresh   # Start clean
```

---

## Authentication Issues

### Issue: Cannot Login

#### Symptoms
- Valid credentials but login fails
- No error message shown
- Redirected back to login page

#### Diagnosis

**Step 1: Check User Exists**
```bash
php artisan tinker
>>> App\Models\User::where('email', 'user@example.com')->first();
```

**Step 2: Test Password**
```bash
>>> $user = App\Models\User::where('email', 'user@example.com')->first();
>>> Hash::check('password123', $user->password);
// Should return true
```

**Step 3: Check Session**
```bash
# Check session driver
grep SESSION_DRIVER .env

# Should be 'file' or 'database'
SESSION_DRIVER=file
```

Clear sessions:
```bash
php artisan cache:clear
php artisan session:clear  # If using database sessions
```

#### Solutions

**1. Reset Password**
```bash
php artisan tinker
>>> $user = App\Models\User::where('email', 'user@example.com')->first();
>>> $user->password = Hash::make('newpassword123');
>>> $user->save();
```

**2. Check Email Verification**
```bash
>>> $user->email_verified_at;
// If null, verify manually:
>>> $user->email_verified_at = now();
>>> $user->save();
```

---

### Issue: Registration Failed

#### Error: "Phone number already exists"

User tried to register with existing phone.

**Solution:**
```bash
php artisan tinker
>>> $user = App\Models\User::where('phone', '6281234567890')->first();
>>> $user->email;  # Show which account has this phone
```

Options:
1. User should login instead of registering
2. Update existing user's phone if incorrect

---

## Performance Issues

### Issue: Slow Page Load

#### Diagnosis

**1. Enable Debug Bar**
```bash
composer require barryvdh/laravel-debugbar --dev
```

**2. Check Database Queries**

Look for N+1 queries:
```bash
php artisan tinker
>>> DB::enableQueryLog();
>>> // Navigate to slow page
>>> DB::getQueryLog();
```

**3. Profile with Telescope**
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Visit: `http://127.0.0.1:8000/telescope`

#### Solutions

**1. Eager Loading**

❌ Bad (N+1 query):
```php
$appointments = Appointment::all();
foreach ($appointments as $appointment) {
    echo $appointment->professional->name;  // Query for each
}
```

✅ Good:
```php
$appointments = Appointment::with('professional')->all();
foreach ($appointments as $appointment) {
    echo $appointment->professional->name;  // Already loaded
}
```

**2. Caching**
```php
// Cache professional list
$professionals = Cache::remember('professionals', 3600, function () {
    return Professional::with('user')->get();
});
```

**3. Queue Background Tasks**
```php
// For notifications, use queues:
class AppointmentConfirmed extends Notification implements ShouldQueue
{
    // ...
}
```

Run worker:
```bash
php artisan queue:work
```

---

## Debug Commands

### Useful Artisan Commands

```bash
# Clear all caches
php artisan optimize:clear

# Check configuration
php artisan config:show

# Check routes
php artisan route:list

# Check database connection
php artisan db:show

# Run tests
php artisan test

# Check logs
tail -f storage/logs/laravel.log

# Interactive shell
php artisan tinker
```

### Check System Health

```bash
# Check PHP version
php -v

# Check required extensions
php -m | grep -E "pdo|mbstring|tokenizer|xml|ctype|json"

# Check disk space
df -h

# Check memory
free -m

# Check processes
ps aux | grep php
```

---

## Getting Help

If issue persists:

1. **Check Logs**
   - Laravel: `storage/logs/laravel.log`
   - WAHA: `docker logs waha`
   - Web server: `/var/log/nginx/error.log` or Apache logs

2. **Enable Debug Mode**
   ```env
   APP_DEBUG=true
   ```
   ⚠️ Only in development!

3. **Search Documentation**
   - Check [docs/](../docs/) folder
   - Laravel docs: https://laravel.com/docs
   - WAHA docs: https://waha.devlike.pro

4. **Create Test Case**
   ```bash
   php artisan make:test IssueReproductionTest
   ```

5. **Contact Support**
   - GitHub Issues: [Report Bug]
   - Email: support@temanbicara.com

---

**Last Updated:** 2025-12-17
