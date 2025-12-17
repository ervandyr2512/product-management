# Features Documentation - Teman Bicara

## Complete Feature List

### 1. Landing Page (Homepage)

**Route**: `/` (home)

**Features**:
- Modern hero section dengan gradient background
- Stats section (professionals, consultations, ratings)
- 6 key features dengan icons:
  - Profesional Terverifikasi
  - Privasi Terjamin
  - Jadwal Fleksibel
  - Video Consultation
  - Harga Terjangkau
  - Mudah & Cepat
- How it works (4 steps)
- Call-to-action section
- Footer dengan navigasi lengkap

**File**: `resources/views/welcome.blade.php`

---

### 2. Authentication System

**Routes**:
- `/register` - Registration
- `/login` - Login
- `/logout` - Logout
- `/verify-email` - Email verification
- `/forgot-password` - Password reset

**Features**:
- Email verification wajib setelah registrasi
- Modern UI dengan purple theme
- Info notice pada registration page
- Resend verification email
- Password reset functionality

**Files**:
- `resources/views/auth/register.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/verify-email.blade.php`
- `resources/views/auth/forgot-password.blade.php`

**User Roles**:
- `user` (default) - Regular user
- `professional` - Psikiater, Psikolog, Conversationalist

---

### 3. Professional Management

#### 3.1 Browse Professionals

**Route**: `/professionals`

**Features**:
- Grid layout dengan cards
- Search by name atau spesialisasi
- Filter by type (psychiatrist, psychologist, conversationalist)
- Display info:
  - Name & avatar (initial letter)
  - Type & specialization
  - Experience years
  - Rates (30 min & 60 min)
- Pagination

**Controller**: `ProfessionalController@index`

#### 3.2 Professional Detail

**Route**: `/professionals/{professional}`

**Features**:
- Full professional information
- Bio & specialization
- Available schedules (calendar view)
- Add to cart functionality
- Duration selection (30 or 60 minutes)

**Controller**: `ProfessionalController@show`

---

### 4. Shopping Cart System

**Route**: `/cart`

**Features**:
- View all items in cart
- Display:
  - Professional name & type
  - Schedule (date, time)
  - Duration
  - Price
- Remove items
- Proceed to checkout button
- Empty cart message

**Controller**: `CartController`

**Actions**:
- `index` - View cart
- `store` - Add to cart
- `destroy` - Remove from cart

---

### 5. Payment & Checkout

**Route**: `/checkout`

**Features**:
- Review all appointments in cart
- Total price calculation
- Payment method selection:
  - Credit Card (demo)
  - Bank Transfer (demo)
  - E-Wallet (demo)
- Demo payment (auto success)
- Create appointments after payment
- Generate video chat links

**Controller**: `PaymentController`

**Actions**:
- `checkout` - Show checkout page
- `process` - Process payment

---

### 6. Appointment Management

#### 6.1 Appointment List

**Route**: `/appointments`

**Features**:
- List all user appointments
- Filter tabs:
  - All
  - Pending
  - Confirmed
  - Completed
  - Cancelled
- Display info:
  - Professional name & type
  - Date & time
  - Duration
  - Price
  - Status badge
- View detail link

**Controller**: `AppointmentController@index`

#### 6.2 Appointment Detail

**Route**: `/appointments/{appointment}`

**Features**:
- Full appointment details
- Payment information
- Video chat link (if confirmed)
- Cancel appointment button (if pending/confirmed)
- Status badge with color coding

**Controller**: `AppointmentController@show`

#### 6.3 Cancel Appointment

**Route**: `POST /appointments/{appointment}/cancel`

**Features**:
- Cancel pending/confirmed appointments
- Cannot cancel completed/cancelled appointments
- Mark schedule as available again

**Controller**: `AppointmentController@cancel`

---

### 7. Professional Schedule Management

**Routes** (for professionals only):
- `/professional/schedules` - List schedules
- `/professional/schedules/create` - Add schedule
- `POST /professional/schedules` - Store schedule
- `DELETE /professional/schedules/{schedule}` - Delete schedule

**Features**:

#### 7.1 Schedule List
- View all schedules (past & future)
- Status indicators:
  - Tersedia (green)
  - Terbooking (yellow)
  - Tidak Tersedia (gray)
- Delete schedule (if not booked)
- Pagination

#### 7.2 Add Schedule
- Date picker (today or future)
- Start time & end time
- Validation:
  - Date must be today or future
  - End time after start time
  - No duplicate schedules
- Info notice about deletion rules

**Controller**: `Professional\ScheduleController`

**Authorization**: Only users with role 'professional'

---

### 8. Article/Blog System

#### 8.1 Article List

**Route**: `/articles`

**Features**:
- Grid layout (3 columns)
- Search by title, excerpt, content
- Filter by category:
  - Kesehatan Mental
  - Kecemasan
  - Depresi
  - Stress
  - Perawatan Diri
  - Terapi
  - Lainnya
- Display:
  - Featured image or gradient placeholder
  - Category badge
  - Published date
  - Title & excerpt
  - Author
- Pagination

**Controller**: `ArticleController@index`

#### 8.2 Article Detail

**Route**: `/articles/{slug}`

**Features**:
- Full article content
- Category & publish date
- Author name
- Related articles (same category)
- Back to articles link

**Controller**: `ArticleController@show`

---

### 9. Static Pages

#### 9.1 About Us

**Route**: `/about`

**Features**:
- Hero section dengan gradient
- Misi & Visi
- Cerita perusahaan
- Nilai-nilai perusahaan (3 core values)
- Team section dengan CTA

**Controller**: `PageController@about`

#### 9.2 Contact Us

**Route**: `/contact`

**Features**:
- Contact form dengan validasi:
  - Name (required)
  - Email (required)
  - Phone (optional)
  - Subject (required, dropdown)
  - Message (required)
- Contact information:
  - Email addresses
  - Phone numbers
  - Office address
  - Business hours
- Success message after submit
- Quick link to articles/FAQ

**Controller**: `PageController@contact`, `PageController@contactSubmit`

---

### 10. Notification System

#### 10.1 Email Notifications

**Service**: Mailhog (development)

**Triggers**:
- Email verification after registration
- Appointment confirmation after payment

**Content**:
- Appointment details
- Professional information
- Schedule (date, time)
- Video chat link
- Cancellation policy

**File**: `app/Notifications/AppointmentConfirmed.php`

#### 10.2 WhatsApp Notifications

**Service**: WAHA (WhatsApp HTTP API)

**Triggers**:
- Appointment confirmation after payment

**Content** (same as email):
- Appointment details
- Professional name
- Schedule
- Video chat link

**Features**:
- Auto format phone number (add 62 prefix)
- Error logging
- Success logging
- API key authentication

**File**: `app/Notifications/Channels/WhatsappChannel.php`

---

### 11. Dashboard

**Route**: `/dashboard`

**Features**:
- Welcome message
- Quick links:
  - Browse Professionals
  - My Appointments
  - Shopping Cart
- Stats overview (for future enhancement)

**Middleware**: `auth`, `verified`

---

## Feature Access Control

### Public Access (No login required)
- Landing page
- Browse professionals
- View professional details
- Articles (list & detail)
- About Us
- Contact Us
- Registration
- Login

### Authenticated Access (Login required, email not verified)
- Email verification page
- Resend verification email
- Logout

### Verified User Access (Login + email verified)
- Dashboard
- Profile management
- Shopping cart
- Checkout & payment
- View appointments
- Cancel appointments

### Professional Access (Role: professional, verified)
- All verified user access
- Schedule management (add, view, delete)

---

## Design Patterns Used

### 1. MVC Pattern
- Models: Eloquent ORM
- Views: Blade templates
- Controllers: Handle business logic

### 2. Repository Pattern
- Not explicitly implemented, but can be added

### 3. Observer Pattern
- Laravel Events & Listeners (can be added)

### 4. Factory Pattern
- Database factories for testing & seeding

### 5. Strategy Pattern
- Notification channels (Mail, WhatsApp)

---

## Security Features

1. **CSRF Protection** - All forms protected
2. **SQL Injection Prevention** - Eloquent ORM & prepared statements
3. **XSS Prevention** - Blade escaping
4. **Email Verification** - Mandatory before access
5. **Authentication** - Laravel Breeze
6. **Authorization** - Middleware & policies
7. **Password Hashing** - Bcrypt
8. **Rate Limiting** - On verification emails
9. **Signed Routes** - For email verification links

---

## Performance Optimizations

1. **Eager Loading** - Prevent N+1 queries
2. **Pagination** - For large datasets
3. **Database Indexing** - On foreign keys
4. **Caching** - Config, routes, views (in production)
5. **Queue Jobs** - For email & WhatsApp notifications
6. **CDN** - For Tailwind CSS & Alpine.js

---

## Future Enhancements

Possible features to add:
1. Rating & Review system
2. Real-time chat
3. Video consultation integration (Jitsi/Zoom)
4. Payment gateway integration (Midtrans/Xendit)
5. Professional availability calendar
6. Appointment reminders
7. Prescription management
8. Medical records
9. Multi-language support
10. Admin panel

---

## Next Documentation

- [03-DATABASE.md](03-DATABASE.md) - Database structure
- [04-AUTHENTICATION.md](04-AUTHENTICATION.md) - Authentication details
- [05-BOOKING-FLOW.md](05-BOOKING-FLOW.md) - Booking process
