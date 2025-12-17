# Testing Guide - Teman Bicara

## Overview

Panduan lengkap untuk testing aplikasi Teman Bicara menggunakan PHPUnit dan Laravel's testing framework.

## Test Types

1. **Feature Tests** - Test full HTTP requests and responses
2. **Unit Tests** - Test individual methods and classes
3. **Browser Tests (Dusk)** - Test JavaScript interactions (optional)

## Test Directory Structure

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── RegistrationTest.php
│   │   ├── EmailVerificationTest.php
│   │   └── AuthenticationTest.php
│   ├── ProfessionalTest.php
│   ├── CartTest.php
│   ├── PaymentTest.php
│   ├── AppointmentTest.php
│   ├── ArticleTest.php
│   └── ScheduleTest.php
├── Unit/
│   ├── Models/
│   │   ├── UserTest.php
│   │   ├── ProfessionalTest.php
│   │   ├── AppointmentTest.php
│   │   └── ArticleTest.php
│   └── Notifications/
│       └── AppointmentConfirmedTest.php
├── TestCase.php
└── CreatesApplication.php
```

## Running Tests

### Run All Tests

```bash
php artisan test
```

### Run Specific Test File

```bash
php artisan test tests/Feature/CartTest.php
```

### Run Specific Test Method

```bash
php artisan test --filter test_user_can_add_to_cart
```

### Run Tests with Coverage

```bash
php artisan test --coverage
```

### Run Tests in Parallel

```bash
php artisan test --parallel
```

## Database Setup for Testing

### Test Database Configuration

**File**: `phpunit.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="MAIL_MAILER" value="array"/>
    </php>
</phpunit>
```

### Using In-Memory SQLite

Testing menggunakan SQLite in-memory untuk performa:

```php
// In TestCase.php or specific test
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    // Tests will run on fresh database each time
}
```

## Authentication Tests

### Registration Test

**File**: `tests/Feature/Auth/RegistrationTest.php`

```php
<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_sends_verification_email(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo(
            $user,
            \Illuminate\Auth\Notifications\VerifyEmail::class
        );
    }
}
```

### Email Verification Test

**File**: `tests/Feature/Auth/EmailVerificationTest.php`

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
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
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
```

## Feature Tests

### Professional Test

**File**: `tests/Feature/ProfessionalTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Professional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfessionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_professionals_page_displays_professionals(): void
    {
        Professional::factory()->count(3)->create();

        $response = $this->get('/professionals');

        $response->assertStatus(200);
        $response->assertViewIs('professionals.index');
        $response->assertViewHas('professionals');
    }

    public function test_can_filter_professionals_by_specialization(): void
    {
        Professional::factory()->create(['specialization' => 'psychiatrist']);
        Professional::factory()->create(['specialization' => 'psychologist']);

        $response = $this->get('/professionals?specialization=psychiatrist');

        $response->assertStatus(200);
        $response->assertSee('psychiatrist');
    }

    public function test_professional_detail_page_shows_schedules(): void
    {
        $professional = Professional::factory()
            ->hasSchedules(3)
            ->create();

        $response = $this->get("/professionals/{$professional->id}");

        $response->assertStatus(200);
        $response->assertViewIs('professionals.show');
        $response->assertViewHas('schedules');
    }
}
```

### Cart Test

**File**: `tests/Feature/CartTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_to_cart(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $professional = Professional::factory()->create();
        $schedule = Schedule::factory()->create([
            'professional_id' => $professional->id,
            'is_available' => true,
        ]);

        $response = $this->actingAs($user)->post('/cart', [
            'schedule_id' => $schedule->id,
            'duration' => 30,
        ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'duration' => 30,
        ]);
    }

    public function test_cannot_add_unavailable_schedule_to_cart(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $schedule = Schedule::factory()->create([
            'is_available' => false,
        ]);

        $response = $this->actingAs($user)->post('/cart', [
            'schedule_id' => $schedule->id,
            'duration' => 30,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
        ]);
    }

    public function test_cannot_add_duplicate_schedule_to_cart(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $schedule = Schedule::factory()->create(['is_available' => true]);

        Cart::factory()->create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
        ]);

        $response = $this->actingAs($user)->post('/cart', [
            'schedule_id' => $schedule->id,
            'duration' => 30,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_user_can_remove_item_from_cart(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/cart/{$cart->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }

    public function test_user_cannot_remove_other_users_cart_item(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $otherUser = User::factory()->create(['email_verified_at' => now()]);
        $cart = Cart::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete("/cart/{$cart->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('carts', ['id' => $cart->id]);
    }
}
```

### Payment Test

**File**: `tests/Feature/PaymentTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_process_payment(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'phone' => '081234567890',
        ]);

        $schedule = Schedule::factory()->create(['is_available' => true]);

        Cart::factory()->create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'duration' => 30,
            'price' => 150000,
        ]);

        $response = $this->actingAs($user)->post('/payment/process');

        $response->assertRedirect(route('appointments.index'));
        $response->assertSessionHas('success');

        // Check payment created
        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'status' => 'success',
        ]);

        // Check appointment created
        $this->assertDatabaseHas('appointments', [
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'status' => 'confirmed',
        ]);

        // Check schedule marked as unavailable
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'is_available' => false,
        ]);

        // Check cart cleared
        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_process_payment_with_empty_cart(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->post('/payment/process');

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    public function test_payment_generates_video_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'phone' => '081234567890',
        ]);

        $schedule = Schedule::factory()->create(['is_available' => true]);

        Cart::factory()->create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
        ]);

        $this->actingAs($user)->post('/payment/process');

        $appointment = Appointment::where('user_id', $user->id)->first();

        $this->assertNotNull($appointment->video_link);
        $this->assertStringContainsString('meet.jit.si', $appointment->video_link);
    }
}
```

### Appointment Test

**File**: `tests/Feature/AppointmentTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_appointments(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        Appointment::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/appointments');

        $response->assertStatus(200);
        $response->assertViewIs('appointments.index');
        $response->assertViewHas('appointments');
    }

    public function test_user_can_cancel_their_appointment(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $schedule = Schedule::factory()->create([
            'is_available' => false,
            'date' => now()->addDays(7),
        ]);

        $appointment = Appointment::factory()->create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post("/appointments/{$appointment->id}/cancel");

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
        ]);

        // Check schedule is available again
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'is_available' => true,
        ]);
    }

    public function test_cannot_cancel_past_appointment(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $schedule = Schedule::factory()->create([
            'date' => now()->subDays(1),
        ]);

        $appointment = Appointment::factory()->create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post("/appointments/{$appointment->id}/cancel");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }
}
```

### Article Test

**File**: `tests/Feature/ArticleTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_articles_page_displays_published_articles(): void
    {
        Article::factory()->count(3)->create([
            'is_published' => true,
            'published_at' => now(),
        ]);

        Article::factory()->create([
            'is_published' => false,
        ]);

        $response = $this->get('/articles');

        $response->assertStatus(200);
        $response->assertViewIs('articles.index');
    }

    public function test_can_view_article_detail(): void
    {
        $article = Article::factory()->create([
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get("/articles/{$article->slug}");

        $response->assertStatus(200);
        $response->assertSee($article->title);
        $response->assertSee($article->content);
    }

    public function test_unpublished_article_is_not_accessible(): void
    {
        $article = Article::factory()->create([
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get("/articles/{$article->slug}");

        $response->assertStatus(404);
    }

    public function test_can_filter_articles_by_category(): void
    {
        Article::factory()->create([
            'category' => 'anxiety',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Article::factory()->create([
            'category' => 'depression',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/articles?category=anxiety');

        $response->assertStatus(200);
    }

    public function test_can_search_articles(): void
    {
        Article::factory()->create([
            'title' => 'Managing Anxiety at Work',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/articles?search=anxiety');

        $response->assertStatus(200);
        $response->assertSee('Managing Anxiety at Work');
    }
}
```

## Unit Tests

### User Model Test

**File**: `tests/Unit/Models/UserTest.php`

```php
<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Professional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_professional(): void
    {
        $user = User::factory()->create(['role' => 'professional']);

        $this->assertTrue($user->isProfessional());
        $this->assertFalse($user->isUser());
    }

    public function test_user_has_professional_relationship(): void
    {
        $user = User::factory()->create(['role' => 'professional']);
        $professional = Professional::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Professional::class, $user->professional);
        $this->assertEquals($professional->id, $user->professional->id);
    }

    public function test_user_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create(['email' => 'test@example.com']);
    }
}
```

### Article Model Test

**File**: `tests/Unit/Models/ArticleTest.php`

```php
<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_automatically_generates_slug(): void
    {
        $article = Article::factory()->create([
            'title' => 'Managing Anxiety at Work',
            'slug' => null,
        ]);

        $this->assertEquals('managing-anxiety-at-work', $article->slug);
    }

    public function test_article_category_label_returns_correct_label(): void
    {
        $article = Article::factory()->create(['category' => 'anxiety']);

        $this->assertEquals('Kecemasan', $article->category_label);
    }

    public function test_article_reading_time_is_calculated(): void
    {
        $article = Article::factory()->create([
            'content' => str_repeat('word ', 400), // 400 words
        ]);

        $this->assertEquals(2, $article->reading_time); // 400 words / 200 wpm = 2 min
    }

    public function test_published_scope_only_returns_published_articles(): void
    {
        Article::factory()->create([
            'is_published' => true,
            'published_at' => now(),
        ]);

        Article::factory()->create([
            'is_published' => false,
        ]);

        $publishedArticles = Article::published()->get();

        $this->assertEquals(1, $publishedArticles->count());
    }
}
```

## Notification Tests

**File**: `tests/Unit/Notifications/AppointmentConfirmedTest.php`

```php
<?php

namespace Tests\Unit\Notifications;

use App\Models\Appointment;
use App\Models\User;
use App\Notifications\AppointmentConfirmed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentConfirmedTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_uses_mail_and_whatsapp_channels(): void
    {
        $notification = new AppointmentConfirmed(Appointment::factory()->make());
        $user = User::factory()->make();

        $channels = $notification->via($user);

        $this->assertContains('mail', $channels);
        $this->assertContains(\App\Notifications\Channels\WhatsappChannel::class, $channels);
    }

    public function test_notification_mail_message_contains_appointment_details(): void
    {
        $appointment = Appointment::factory()->make([
            'duration' => 30,
        ]);

        $user = User::factory()->make();

        $notification = new AppointmentConfirmed($appointment);
        $mailMessage = $notification->toMail($user);

        $this->assertStringContainsString($user->name, $mailMessage->greeting);
        $this->assertStringContainsString('30 menit', implode(' ', $mailMessage->introLines));
    }
}
```

## Test Helpers

### Custom Assertions

```php
// tests/TestCase.php

protected function assertDatabaseCount(string $table, int $count): void
{
    $actual = DB::table($table)->count();
    $this->assertEquals($count, $actual);
}

protected function assertModelExists(Model $model): void
{
    $this->assertDatabaseHas($model->getTable(), [
        $model->getKeyName() => $model->getKey(),
    ]);
}
```

### Factory States

```php
// database/factories/UserFactory.php

public function professional(): static
{
    return $this->state(fn (array $attributes) => [
        'role' => 'professional',
    ]);
}

public function unverified(): static
{
    return $this->state(fn (array $attributes) => [
        'email_verified_at' => null,
    ]);
}

// Usage in tests
$user = User::factory()->professional()->create();
$unverifiedUser = User::factory()->unverified()->create();
```

## Mocking External Services

### Mock WAHA API

```php
use Illuminate\Support\Facades\Http;

public function test_whatsapp_notification_is_sent(): void
{
    Http::fake([
        'localhost:3000/api/sendText' => Http::response(['success' => true], 200),
    ]);

    // Trigger notification

    Http::assertSent(function ($request) {
        return $request->url() == 'http://localhost:3000/api/sendText' &&
               $request['session'] == 'default';
    });
}
```

### Mock Email

```php
use Illuminate\Support\Facades\Mail;

public function test_appointment_confirmation_email_is_sent(): void
{
    Mail::fake();

    // Trigger email send

    Mail::assertSent(\App\Mail\AppointmentConfirmed::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
}
```

## Continuous Integration (CI)

### GitHub Actions Example

**File**: `.github/workflows/tests.yml`

```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          extensions: mbstring, pdo, pdo_sqlite

      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress

      - name: Copy .env
        run: cp .env.example .env

      - name: Generate key
        run: php artisan key:generate

      - name: Run Tests
        run: php artisan test
```

## Best Practices

1. **Use RefreshDatabase**: Always use for fresh database state
2. **Factory Instead of Manual Creation**: Use factories for test data
3. **Fake External Services**: Mock HTTP, Mail, Notifications
4. **Descriptive Test Names**: Use `test_user_can_do_something` format
5. **Arrange-Act-Assert**: Structure tests clearly
6. **One Assertion Per Test**: Keep tests focused
7. **Test Edge Cases**: Not just happy path
8. **Clean Up**: Use `tearDown()` if needed
9. **Fast Tests**: Keep tests fast by using in-memory database
10. **Continuous Testing**: Run tests on every commit

## Code Coverage

### Generate Coverage Report

```bash
php artisan test --coverage --min=80
```

### HTML Coverage Report

```bash
php artisan test --coverage-html coverage-report
```

Then open `coverage-report/index.html` in browser.

## Next Steps

After reading this documentation:
1. Write tests for existing features
2. Achieve at least 80% code coverage
3. Set up CI/CD pipeline
4. Run tests before every deployment
5. Add tests for new features before implementation (TDD)
