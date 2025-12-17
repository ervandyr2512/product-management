# Teman Bicara - Platform Konsultasi Mental Health

Platform yang menghubungkan pengguna dengan dokter psikiater, psikolog, atau conversationalist profesional untuk konsultasi virtual melalui video chat.

## Fitur Utama

- **Authentication System** menggunakan Laravel Breeze dengan **Email Verification Wajib**
- **Email Verification** - User harus verifikasi email setelah registrasi sebelum bisa login
- **Professional Management** - Psikiater, Psikolog, dan Conversationalist dengan jadwal masing-masing
- **Schedule Management** - Professional dapat menambah/menghapus jadwal tersedia mereka
- **Booking System** - Pengguna dapat memilih jadwal dan menambahkan ke keranjang
- **Shopping Cart** - Sistem keranjang belanja untuk multiple bookings
- **Payment Gateway Integration** - Demo payment gateway untuk pembayaran
- **Email Notifications** - Konfirmasi booking via email menggunakan Mailhog
- **WhatsApp Notifications** - Konfirmasi booking via WhatsApp menggunakan WAHA
- **Video Chat Links** - Generate unique link untuk video consultation
- **Appointment Management** - Manajemen janji temu (view, cancel)
- **Landing Page** - Homepage dengan hero section, features, dan call-to-action
- **Article/Blog System** - Sistem artikel kesehatan mental dengan kategori
- **Contact Us** - Form kontak dengan validasi
- **About Us** - Halaman tentang platform Teman Bicara

## Tech Stack

- Laravel 12
- MySQL 8.0
- Mailhog (untuk development email)
- WAHA (WhatsApp HTTP API)
- Blade Templates
- Tailwind CSS

## Prerequisites

- PHP 8.2 or higher
- Composer
- Docker/OrbStack (untuk MySQL, Mailhog, dan WAHA)
- Node.js & NPM (optional, untuk Vite)

## Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd teman-bicara
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` sesuai dengan konfigurasi Docker:

```env
APP_NAME="Teman Bicara"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3305
DB_DATABASE=mydatabase
DB_USERNAME=myuser
DB_PASSWORD=mypassword

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@temanbicara.com"
MAIL_FROM_NAME="${APP_NAME}"

WAHA_URL=http://localhost:3000
WAHA_SESSION=default
WAHA_API_KEY=asdf
```

### 4. Setup Docker Services

Jalankan semua services (MySQL, Mailhog, dan WAHA) dengan satu command:

```bash
docker-compose up -d
```

**Services yang akan berjalan:**
- **MySQL**: Port 3305 (database)
- **Mailhog**: Port 1025 (SMTP), Port 8025 (Web UI)
- **WAHA**: Port 3000 (WhatsApp API & Dashboard)

**Setup WhatsApp Session:**
1. Buka WAHA Dashboard: http://localhost:3000
2. Login dengan:
   - Username: `admin`
   - Password: `asdf`
   - API Key: `asdf` (sudah dikonfigurasi di `.env`)
3. Start session bernama "default"
4. Scan QR code dengan WhatsApp Anda

**Akses Services:**
- Mailhog UI: http://localhost:8025
- WAHA Dashboard: http://localhost:3000

### 5. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
```

Seeder akan membuat:
- 10 Professionals dengan jadwal masing-masing
- 5 Regular users
- Beberapa sample appointments
- Test user: `user@example.com` / `password`

### 6. Setup Queue Worker

Untuk menjalankan notifikasi (email & WhatsApp), jalankan queue worker:

```bash
php artisan queue:work
```

Atau untuk development:

```bash
php artisan queue:listen
```

### 7. Run Application

```bash
php artisan serve
```

Aplikasi akan berjalan di: http://localhost:8000

## Struktur Database

### Users Table
- id
- name
- email
- password
- role (user, professional)
- phone
- timestamps

### Professionals Table
- id
- user_id
- type (psychiatrist, psychologist, conversationalist)
- license_number
- bio
- specialization
- experience_years
- rate_30min
- rate_60min
- profile_photo
- is_active
- timestamps

### Schedules Table
- id
- professional_id
- date
- start_time
- end_time
- is_available
- timestamps

### Appointments Table
- id
- user_id
- professional_id
- schedule_id
- appointment_date
- start_time
- end_time
- duration (30, 60)
- price
- status (pending, confirmed, completed, cancelled)
- video_chat_link
- notes
- timestamps

### Carts Table
- id
- user_id
- schedule_id
- professional_id
- duration (30, 60)
- price
- timestamps

### Payments Table
- id
- user_id
- appointment_id
- payment_gateway_id
- amount
- status (pending, success, failed, refunded)
- payment_method
- payment_details
- paid_at
- timestamps

## Usage Flow

1. **Browse Professionals**: User dapat melihat daftar professionals dan filter berdasarkan tipe atau search
2. **View Professional Detail**: User dapat melihat detail professional dan jadwal yang tersedia
3. **Add to Cart**: User memilih jadwal dan durasi (30 atau 60 menit), kemudian tambahkan ke keranjang
4. **Checkout**: User melakukan checkout dari keranjang dan memilih metode pembayaran
5. **Payment**: Sistem memproses pembayaran (auto success untuk demo)
6. **Confirmation**: User menerima konfirmasi via email dan WhatsApp dengan link video chat
7. **View Appointments**: User dapat melihat daftar janji temu di halaman "Janji Temu Saya"
8. **Join Video Chat**: User dapat join video chat saat waktunya tiba
9. **Cancel Appointment**: User dapat membatalkan appointment yang masih pending/confirmed

## Notification System

### Email Notification
- Menggunakan Mailhog untuk development
- Email dikirim setelah payment sukses
- Berisi detail appointment dan link video chat

### WhatsApp Notification
- Menggunakan WAHA (WhatsApp HTTP API)
- Pesan dikirim ke nomor phone user
- Format nomor: dimulai dengan 62 (Indonesia)
- Berisi detail appointment dan link video chat

## Testing

### Test Accounts
Setelah run seeder, gunakan:
- Email: `user@example.com`
- Password: `password`
- **Note**: User dari seeder sudah ter-verifikasi emailnya

### Testing Email Verification
1. **Register User Baru**:
   - Buka http://localhost:8000/register
   - Isi form registrasi dengan email valid
   - Submit form

2. **Verifikasi Email**:
   - Setelah register, akan redirect ke halaman "Verifikasi Email Anda"
   - Buka Mailhog: http://localhost:8025
   - Cari email "Verify Email Address"
   - Klik link verifikasi di email
   - Email berhasil diverifikasi!

3. **Coba Login Tanpa Verifikasi**:
   - Jika mencoba login tanpa verifikasi email
   - Akan otomatis redirect ke halaman verifikasi email
   - User tidak bisa akses fitur protected sampai email terverifikasi

### Manual Testing Flow
1. Login dengan test account (atau register account baru & verifikasi)
2. Browse professionals di homepage atau menu
3. Pilih professional dan lihat jadwal tersedia
4. Tambahkan jadwal ke keranjang (pilih durasi 30 atau 60 menit)
5. Checkout dan pilih metode pembayaran
6. Payment otomatis sukses (demo mode)
7. Cek email konfirmasi di Mailhog (http://localhost:8025)
8. Cek WhatsApp untuk notifikasi (jika sudah setup WAHA)
9. View appointments di menu "Janji Temu"
10. Bisa cancel appointment yang masih pending/confirmed

## Development Notes

- Payment gateway adalah simulasi (auto success)
- Video chat link adalah placeholder (perlu integrasi dengan Jitsi/Zoom/etc)
- WhatsApp notification memerlukan WAHA setup dengan QR scan
- Mailhog untuk email testing (tidak mengirim email real)

## Production Checklist

- [ ] Ganti dengan real payment gateway (Midtrans, Xendit, etc)
- [ ] Integrate dengan real video chat provider
- [ ] Setup proper email SMTP
- [ ] Setup proper WhatsApp Business API
- [ ] Configure queue dengan Redis/SQS
- [ ] Setup proper storage untuk profile photos
- [ ] Add proper validation dan error handling
- [ ] Add rate limiting
- [ ] Setup monitoring dan logging
- [ ] Configure proper HTTPS
- [ ] Add backup strategy

## Documentation

Dokumentasi lengkap tersedia di folder `docs/`:

### Core Documentation
1. **[00-OVERVIEW.md](docs/00-OVERVIEW.md)** - Overview aplikasi, arsitektur, dan tech stack
2. **[01-INSTALLATION.md](docs/01-INSTALLATION.md)** - Panduan instalasi step-by-step
3. **[02-FEATURES.md](docs/02-FEATURES.md)** - Detail semua fitur aplikasi
4. **[03-DATABASE.md](docs/03-DATABASE.md)** - Struktur database dan ERD
5. **[04-AUTHENTICATION.md](docs/04-AUTHENTICATION.md)** - Sistem authentication dan email verification
6. **[05-BOOKING-FLOW.md](docs/05-BOOKING-FLOW.md)** - Complete booking flow dari browse hingga payment
7. **[06-NOTIFICATIONS.md](docs/06-NOTIFICATIONS.md)** - Email dan WhatsApp notification system
8. **[07-PROFESSIONAL-FEATURES.md](docs/07-PROFESSIONAL-FEATURES.md)** - Fitur khusus untuk professional users
9. **[08-ARTICLE-SYSTEM.md](docs/08-ARTICLE-SYSTEM.md)** - Sistem artikel/blog kesehatan mental
10. **[09-API-ENDPOINTS.md](docs/09-API-ENDPOINTS.md)** - Referensi lengkap semua routes dan endpoints
11. **[10-TESTING.md](docs/10-TESTING.md)** - Panduan testing dengan PHPUnit

### Development Guides
12. **[11-CHANGELOG.md](docs/11-CHANGELOG.md)** - 📝 **Development progress dan changelog lengkap**
13. **[12-TROUBLESHOOTING.md](docs/12-TROUBLESHOOTING.md)** - 🔧 **Troubleshooting guide untuk common issues**
14. **[13-QUICK-REFERENCE.md](docs/13-QUICK-REFERENCE.md)** - ⚡ **Quick reference untuk developer**

### Recommended Reading Order

**Untuk Developer Baru:**
1. Start: [00-OVERVIEW.md](docs/00-OVERVIEW.md) - Pahami arsitektur
2. Setup: [01-INSTALLATION.md](docs/01-INSTALLATION.md) - Install aplikasi
3. Quick Start: [13-QUICK-REFERENCE.md](docs/13-QUICK-REFERENCE.md) - Command yang sering dipakai
4. Deep Dive: Baca dokumentasi lainnya sesuai kebutuhan

**Untuk Troubleshooting:**
1. [12-TROUBLESHOOTING.md](docs/12-TROUBLESHOOTING.md) - Solusi untuk masalah umum
2. [11-CHANGELOG.md](docs/11-CHANGELOG.md) - Lihat perubahan terbaru
3. [13-QUICK-REFERENCE.md](docs/13-QUICK-REFERENCE.md) - Emergency fixes

## License

MIT License
