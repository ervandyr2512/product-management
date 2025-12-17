# API Endpoints - Teman Bicara

## Overview

Dokumentasi lengkap semua routes dan endpoints yang tersedia di aplikasi Teman Bicara.

## Route Files

- `routes/web.php` - Web routes (main application)
- `routes/auth.php` - Authentication routes (Laravel Breeze)

## Public Routes (No Authentication)

### Landing Page

```
GET /
```

**Controller**: `HomeController@index` (or welcome.blade.php)

**Description**: Landing page utama aplikasi

**Response**: HTML view

---

### Professionals Listing

```
GET /professionals
```

**Controller**: `ProfessionalController@index`

**Description**: Daftar semua professional

**Query Parameters**:
- `specialization` (optional) - Filter by specialization (psychiatrist|psychologist|conversationalist)
- `search` (optional) - Search by name

**Response**: HTML view with paginated professionals

**Example**:
```
/professionals?specialization=psychiatrist
/professionals?search=dr+sarah
```

---

### Professional Detail

```
GET /professionals/{professional}
```

**Controller**: `ProfessionalController@show`

**Description**: Detail professional dan jadwal yang tersedia

**Parameters**:
- `professional` (required) - Professional ID

**Response**: HTML view with professional details and available schedules

**Example**:
```
/professionals/1
/professionals/5
```

---

### Articles Listing

```
GET /articles
```

**Controller**: `ArticleController@index`

**Description**: Daftar semua artikel yang published

**Query Parameters**:
- `category` (optional) - Filter by category
- `search` (optional) - Search in title, excerpt, content

**Response**: HTML view with paginated articles

**Example**:
```
/articles?category=anxiety
/articles?search=stress+management
```

---

### Article Detail

```
GET /articles/{slug}
```

**Controller**: `ArticleController@show`

**Description**: Detail artikel berdasarkan slug

**Parameters**:
- `slug` (required) - Article slug (SEO-friendly URL)

**Response**: HTML view with article content and related articles

**Example**:
```
/articles/mengatasi-kecemasan-di-tempat-kerja
/articles/tips-menjaga-kesehatan-mental
```

---

### About Us

```
GET /about
```

**Controller**: Static view

**Description**: Halaman tentang Teman Bicara

**Response**: HTML view

---

### Contact Us

```
GET /contact
```

**Controller**: Static view

**Description**: Halaman kontak dan informasi

**Response**: HTML view

---

## Authentication Routes

### Register Page

```
GET /register
```

**Controller**: `RegisteredUserController@create`

**Description**: Form registrasi user baru

**Response**: HTML view

---

### Register Submit

```
POST /register
```

**Controller**: `RegisteredUserController@store`

**Description**: Proses registrasi user baru

**Request Body**:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Validation**:
- `name`: required, string, max:255
- `email`: required, string, email, max:255, unique:users
- `password`: required, string, min:8, confirmed

**Response**: Redirect to dashboard (will be redirected to verify-email)

**Side Effects**:
- Creates user
- Fires `Registered` event (sends verification email)
- Auto-login user

---

### Login Page

```
GET /login
```

**Controller**: `AuthenticatedSessionController@create`

**Description**: Form login

**Response**: HTML view

---

### Login Submit

```
POST /login
```

**Controller**: `AuthenticatedSessionController@store`

**Description**: Proses login

**Request Body**:
```json
{
  "email": "john@example.com",
  "password": "password123",
  "remember": true
}
```

**Validation**:
- `email`: required, email
- `password`: required

**Response**: Redirect to dashboard

---

### Logout

```
POST /logout
```

**Controller**: `AuthenticatedSessionController@destroy`

**Description**: Logout user

**Response**: Redirect to home

---

### Email Verification Notice

```
GET /verify-email
```

**Middleware**: `auth`

**Controller**: `EmailVerificationPromptController`

**Description**: Halaman pemberitahuan untuk verifikasi email

**Response**: HTML view

---

### Email Verification Verify

```
GET /verify-email/{id}/{hash}
```

**Middleware**: `auth`, `signed`, `throttle:6,1`

**Controller**: `VerifyEmailController`

**Description**: Link verifikasi email (signed URL)

**Parameters**:
- `id`: User ID
- `hash`: SHA-1 hash of email

**Query Parameters**:
- `expires`: Expiration timestamp
- `signature`: URL signature

**Response**: Redirect to dashboard with success message

---

### Email Verification Resend

```
POST /email/verification-notification
```

**Middleware**: `auth`, `throttle:6,1`

**Controller**: `EmailVerificationNotificationController@store`

**Description**: Kirim ulang email verifikasi

**Response**: Redirect back with status message

---

### Forgot Password Page

```
GET /forgot-password
```

**Controller**: `PasswordResetLinkController@create`

**Description**: Form request password reset

**Response**: HTML view

---

### Forgot Password Submit

```
POST /forgot-password
```

**Controller**: `PasswordResetLinkController@store`

**Description**: Kirim email reset password

**Request Body**:
```json
{
  "email": "john@example.com"
}
```

**Response**: Redirect back with status message

---

### Reset Password Page

```
GET /reset-password/{token}
```

**Controller**: `NewPasswordController@create`

**Description**: Form reset password dengan token

**Parameters**:
- `token`: Reset token from email

**Response**: HTML view

---

### Reset Password Submit

```
POST /reset-password
```

**Controller**: `NewPasswordController@store`

**Description**: Proses reset password

**Request Body**:
```json
{
  "token": "reset_token",
  "email": "john@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Response**: Redirect to login with status message

---

## Protected Routes (Auth + Verified)

### Dashboard

```
GET /dashboard
```

**Middleware**: `auth`, `verified`

**Controller**: Static view

**Description**: User dashboard

**Response**: HTML view

---

### Profile Edit

```
GET /profile
```

**Middleware**: `auth`, `verified`

**Controller**: `ProfileController@edit`

**Description**: Edit profile page

**Response**: HTML view

---

### Profile Update

```
PATCH /profile
```

**Middleware**: `auth`, `verified`

**Controller**: `ProfileController@update`

**Description**: Update profile information

**Request Body**:
```json
{
  "name": "John Doe Updated",
  "email": "newemail@example.com",
  "phone": "081234567890"
}
```

**Response**: Redirect back with success message

---

### Profile Delete

```
DELETE /profile
```

**Middleware**: `auth`, `verified`

**Controller**: `ProfileController@destroy`

**Description**: Delete user account

**Response**: Redirect to home

---

## Cart Routes

### View Cart

```
GET /cart
```

**Middleware**: `auth`, `verified`

**Controller**: `CartController@index`

**Description**: Lihat keranjang belanja

**Response**: HTML view with cart items and total

---

### Add to Cart

```
POST /cart
```

**Middleware**: `auth`, `verified`

**Controller**: `CartController@store`

**Description**: Tambah item ke keranjang

**Request Body**:
```json
{
  "schedule_id": 123,
  "duration": 30
}
```

**Validation**:
- `schedule_id`: required, exists:schedules,id
- `duration`: required, in:30,60

**Response**: Redirect to cart with success message

**Business Rules**:
- Schedule must be available
- Cannot add duplicate schedule
- User must be authenticated and verified

---

### Remove from Cart

```
DELETE /cart/{cart}
```

**Middleware**: `auth`, `verified`

**Controller**: `CartController@destroy`

**Description**: Hapus item dari keranjang

**Parameters**:
- `cart`: Cart item ID

**Response**: Redirect back with success message

**Authorization**: Only cart owner can delete

---

## Payment Routes

### Checkout Page

```
GET /checkout
```

**Middleware**: `auth`, `verified`

**Controller**: `PaymentController@checkout`

**Description**: Halaman checkout (review order)

**Response**: HTML view with order summary

**Business Rules**:
- Cart must not be empty
- All schedules must still be available

---

### Process Payment

```
POST /payment/process
```

**Middleware**: `auth`, `verified`

**Controller**: `PaymentController@process`

**Description**: Proses pembayaran (demo - auto success)

**Request Body**:
```json
{
  "phone": "081234567890"
}
```

**Response**: Redirect to appointments with success message

**Side Effects**:
- Creates Payment record
- Creates Appointments
- Marks schedules as unavailable
- Sends notifications (Email + WhatsApp)
- Clears cart

**Uses Database Transaction**: Yes

---

## Appointment Routes

### My Appointments

```
GET /appointments
```

**Middleware**: `auth`, `verified`

**Controller**: `AppointmentController@index`

**Description**: Daftar appointment user

**Response**: HTML view with appointments (upcoming, completed, cancelled)

---

### Appointment Detail

```
GET /appointments/{appointment}
```

**Middleware**: `auth`, `verified`

**Controller**: `AppointmentController@show`

**Description**: Detail appointment

**Parameters**:
- `appointment`: Appointment ID

**Response**: HTML view with appointment details

**Authorization**: Only appointment owner can view

---

### Cancel Appointment

```
POST /appointments/{appointment}/cancel
```

**Middleware**: `auth`, `verified`

**Controller**: `AppointmentController@cancel`

**Description**: Batalkan appointment

**Parameters**:
- `appointment`: Appointment ID

**Response**: Redirect back with success message

**Business Rules**:
- Only confirmed appointments can be cancelled
- Appointment date must be in the future
- Schedule becomes available again

**Authorization**: Only appointment owner can cancel

---

## Professional Routes

### Professional Schedules Index

```
GET /professional/schedules
```

**Middleware**: `auth`, `verified`

**Controller**: `Professional\ScheduleController@index`

**Description**: Daftar jadwal professional

**Response**: HTML view with schedules (available and booked)

**Authorization**: Only professional users

---

### Professional Schedules Create

```
GET /professional/schedules/create
```

**Middleware**: `auth`, `verified`

**Controller**: `Professional\ScheduleController@create`

**Description**: Form tambah jadwal

**Response**: HTML view

**Authorization**: Only professional users

---

### Professional Schedules Store

```
POST /professional/schedules
```

**Middleware**: `auth`, `verified`

**Controller**: `Professional\ScheduleController@store`

**Description**: Simpan jadwal baru

**Request Body**:
```json
{
  "date": "2025-12-20",
  "start_time": "09:00",
  "end_time": "10:00"
}
```

**Validation**:
- `date`: required, date, after_or_equal:today
- `start_time`: required, date_format:H:i
- `end_time`: required, date_format:H:i, after:start_time

**Response**: Redirect to schedules index with success message

**Business Rules**:
- Cannot create duplicate schedule (same date + time)

**Authorization**: Only professional users

---

### Professional Schedules Delete

```
DELETE /professional/schedules/{schedule}
```

**Middleware**: `auth`, `verified`

**Controller**: `Professional\ScheduleController@destroy`

**Description**: Hapus jadwal

**Parameters**:
- `schedule`: Schedule ID

**Response**: Redirect back with success message

**Business Rules**:
- Cannot delete booked schedules (is_available = false)

**Authorization**: Only schedule owner (professional)

---

## Route Grouping Summary

### Guest Routes (Unauthenticated Users)

```php
Route::middleware('guest')->group(function () {
    // /register (GET, POST)
    // /login (GET, POST)
    // /forgot-password (GET, POST)
    // /reset-password/{token} (GET)
    // /reset-password (POST)
});
```

### Auth Routes (Logged In, Email Not Verified OK)

```php
Route::middleware('auth')->group(function () {
    // /verify-email (GET)
    // /verify-email/{id}/{hash} (GET)
    // /email/verification-notification (POST)
    // /confirm-password (GET, POST)
    // /password (PUT)
    // /logout (POST)
});
```

### Protected Routes (Logged In + Email Verified)

```php
Route::middleware(['auth', 'verified'])->group(function () {
    // /dashboard
    // /profile (GET, PATCH, DELETE)
    // /cart (GET, POST, DELETE)
    // /checkout (GET)
    // /payment/process (POST)
    // /appointments (GET, POST cancel)
});
```

### Professional Routes (Logged In + Verified + Professional)

```php
Route::middleware(['auth', 'verified'])->prefix('professional')->group(function () {
    // /professional/schedules (GET, POST, DELETE)
});
```

## HTTP Status Codes

- `200 OK` - Successful GET request
- `302 Found` - Redirect (after POST/PUT/DELETE)
- `403 Forbidden` - Unauthorized access
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation error
- `500 Internal Server Error` - Server error

## Error Responses

### Validation Error (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Unauthorized (403)

```
Redirected to /login with error message
```

### Not Found (404)

```
404 | Not Found
```

## Response Formats

All routes return **HTML views** (Blade templates).

For API version (if implemented in future), responses would be JSON:

```json
{
  "success": true,
  "data": {},
  "message": "Success"
}
```

## Rate Limiting

### Email Verification

```
throttle:6,1
```

- Max 6 attempts per minute for:
  - `/verify-email/{id}/{hash}`
  - `/email/verification-notification`

### General

Laravel default throttling:
- 60 requests per minute per IP

## CSRF Protection

All `POST`, `PUT`, `PATCH`, `DELETE` requests require CSRF token:

```html
<form method="POST" action="/cart">
    @csrf
    <!-- form fields -->
</form>
```

## Signed URLs

Email verification uses signed URLs for security:

```php
URL::signedRoute('verification.verify', [
    'id' => $user->id,
    'hash' => sha1($user->email)
]);
```

## Route Naming Convention

```
{resource}.{action}
```

**Examples**:
- `professionals.index` - GET /professionals
- `professionals.show` - GET /professionals/{professional}
- `cart.index` - GET /cart
- `cart.store` - POST /cart
- `cart.destroy` - DELETE /cart/{cart}
- `professional.schedules.index` - GET /professional/schedules

## Testing Routes

### List All Routes

```bash
php artisan route:list
```

### Filter Routes

```bash
php artisan route:list --path=professionals
php artisan route:list --name=cart
php artisan route:list --method=POST
```

### Test Route in Browser

```
http://localhost:8000/professionals
http://localhost:8000/articles
http://localhost:8000/dashboard
```

### Test with cURL

```bash
# GET request
curl http://localhost:8000/professionals

# POST request with CSRF (need session)
curl -X POST http://localhost:8000/cart \
  -H "Content-Type: application/json" \
  -d '{"schedule_id": 1, "duration": 30}'
```

## Future: REST API Endpoints

If implementing REST API in the future:

```
GET    /api/professionals           - List professionals
GET    /api/professionals/{id}      - Professional detail
POST   /api/cart                    - Add to cart
GET    /api/cart                    - View cart
DELETE /api/cart/{id}               - Remove from cart
POST   /api/payment/process         - Process payment
GET    /api/appointments            - My appointments
POST   /api/appointments/{id}/cancel - Cancel appointment
```

**Authentication**: Laravel Sanctum or Passport

**Response Format**: JSON

## Next Documentation

- [10-TESTING.md](10-TESTING.md) - Testing guide and examples
