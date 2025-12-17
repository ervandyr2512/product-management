# Teman Bicara - Overview Dokumentasi

## Daftar Isi Dokumentasi

1. **[00-OVERVIEW.md](00-OVERVIEW.md)** - Dokumen ini, overview keseluruhan
2. **[01-INSTALLATION.md](01-INSTALLATION.md)** - Panduan instalasi lengkap
3. **[02-FEATURES.md](02-FEATURES.md)** - Daftar lengkap fitur aplikasi
4. **[03-DATABASE.md](03-DATABASE.md)** - Struktur database dan relasi
5. **[04-AUTHENTICATION.md](04-AUTHENTICATION.md)** - Sistem autentikasi dan email verification
6. **[05-BOOKING-FLOW.md](05-BOOKING-FLOW.md)** - Flow booking appointment
7. **[06-NOTIFICATIONS.md](06-NOTIFICATIONS.md)** - Email dan WhatsApp notifications
8. **[07-PROFESSIONAL-FEATURES.md](07-PROFESSIONAL-FEATURES.md)** - Fitur untuk professionals
9. **[08-ARTICLE-SYSTEM.md](08-ARTICLE-SYSTEM.md)** - Sistem artikel/blog
10. **[09-API-ENDPOINTS.md](09-API-ENDPOINTS.md)** - Daftar routes dan endpoints
11. **[10-TESTING.md](10-TESTING.md)** - Panduan testing aplikasi

## Tentang Teman Bicara

**Teman Bicara** adalah platform konsultasi kesehatan mental yang menghubungkan pengguna dengan profesional kesehatan mental (psikiater, psikolog, dan conversationalist) untuk konsultasi virtual melalui video chat.

## Tech Stack

- **Backend**: Laravel 12
- **Database**: MySQL 8.0
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Email**: Mailhog (development)
- **WhatsApp**: WAHA (WhatsApp HTTP API)
- **Containerization**: Docker/Docker Compose

## Arsitektur Aplikasi

```
teman-bicara/
├── app/
│   ├── Http/Controllers/
│   │   ├── AppointmentController.php
│   │   ├── ArticleController.php
│   │   ├── CartController.php
│   │   ├── PageController.php
│   │   ├── PaymentController.php
│   │   ├── ProfessionalController.php
│   │   └── Professional/
│   │       └── ScheduleController.php
│   ├── Models/
│   │   ├── User.php (implements MustVerifyEmail)
│   │   ├── Professional.php
│   │   ├── Schedule.php
│   │   ├── Appointment.php
│   │   ├── Cart.php
│   │   ├── Payment.php
│   │   └── Article.php
│   └── Notifications/
│       ├── AppointmentConfirmed.php
│       └── Channels/
│           └── WhatsappChannel.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   └── views/
│       ├── welcome.blade.php (landing page)
│       ├── auth/ (authentication views)
│       ├── professionals/ (browse & view professionals)
│       ├── cart/ (shopping cart)
│       ├── payment/ (checkout)
│       ├── appointments/ (user appointments)
│       ├── professional/schedules/ (schedule management)
│       ├── articles/ (articles/blog)
│       └── pages/ (about, contact)
└── docker-compose.yml (MySQL, Mailhog, WAHA)
```

## Key Features Overview

### Untuk User (Pengguna)
1. **Registrasi & Login** dengan email verification wajib
2. **Browse Professionals** dengan filter dan search
3. **Booking System** dengan shopping cart
4. **Payment** dengan demo gateway (auto success)
5. **Appointment Management** (view, cancel)
6. **Notifications** via email dan WhatsApp
7. **Article/Blog** tentang kesehatan mental
8. **Contact & About** pages

### Untuk Professional
1. **Schedule Management** - tambah/hapus jadwal tersedia
2. **View Appointments** - lihat appointment yang sudah dibooking
3. **Professional Profile** - tampilkan info dan spesialisasi

## User Roles

- **user** (default) - Regular user yang bisa booking konsultasi
- **professional** - Psikiater, Psikolog, atau Conversationalist

## Workflow Utama

```
1. User Register → Email Verification → Login
2. Browse Professionals → View Details → Add to Cart
3. Checkout → Payment → Appointment Created
4. Email & WhatsApp Notification Sent
5. User receives video chat link
6. Join consultation at scheduled time
```

## Design System

### Color Palette
- **Primary**: Purple (#667eea) & Indigo (#764ba2)
- **Success**: Green (#10b981)
- **Warning**: Yellow (#f59e0b)
- **Danger**: Red (#ef4444)
- **Neutral**: Gray shades

### Typography
- **Font**: Figtree (dari Bunny Fonts)
- **Sizes**:
  - Headings: 2xl-6xl
  - Body: sm-base
  - Small: xs

### Components
- Modern card-based layouts
- Rounded corners (rounded-lg, rounded-xl)
- Shadows (shadow-sm, hover:shadow-md)
- Smooth transitions
- Responsive design (mobile-first)

## Environment Requirements

- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Docker**: For MySQL, Mailhog, WAHA
- **Node.js**: Optional (using CDN for Tailwind & Alpine)

## Quick Start

```bash
# 1. Clone & Install
git clone <repo-url>
cd teman-bicara
composer install

# 2. Setup Environment
cp .env.example .env
php artisan key:generate

# 3. Start Docker Services
docker-compose up -d

# 4. Run Migrations & Seeders
php artisan migrate
php artisan db:seed

# 5. Start Laravel
php artisan serve

# 6. Access Application
http://localhost:8000
```

## Support & Resources

- **Mailhog UI**: http://localhost:8025
- **WAHA Dashboard**: http://localhost:3000
- **Database Port**: 3305 (MySQL)

## Next Steps

Baca dokumentasi berikutnya:
- [01-INSTALLATION.md](01-INSTALLATION.md) - Panduan instalasi detail
- [02-FEATURES.md](02-FEATURES.md) - Penjelasan lengkap semua fitur
