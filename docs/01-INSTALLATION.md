# Installation Guide - Teman Bicara

## Prerequisites

Sebelum memulai instalasi, pastikan sistem Anda memiliki:

- **PHP** 8.2 or higher
- **Composer** (latest version)
- **Docker** or **OrbStack** (untuk MySQL, Mailhog, dan WAHA)
- **Git** (untuk clone repository)
- **Node.js & NPM** (optional, kita menggunakan CDN)

## Step-by-Step Installation

### 1. Clone Repository

```bash
git clone <repository-url>
cd teman-bicara
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Setup Environment File

Copy file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Configure Environment Variables

Edit file `.env` dan sesuaikan konfigurasi:

```env
APP_NAME="Teman Bicara"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3305
DB_DATABASE=mydatabase
DB_USERNAME=myuser
DB_PASSWORD=mypassword

# Mail Configuration (Mailhog)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@temanbicara.com"
MAIL_FROM_NAME="${APP_NAME}"

# WhatsApp Configuration (WAHA)
WAHA_URL=http://localhost:3000
WAHA_SESSION=default
WAHA_API_KEY=asdf

# Queue Configuration
QUEUE_CONNECTION=database
```

### 5. Setup Docker Services

File `docker-compose.yml` sudah mencakup 3 services:
- MySQL (port 3305)
- Mailhog (ports 1025, 8025)
- WAHA (port 3000)

Start semua services dengan satu command:

```bash
docker-compose up -d
```

Verify services are running:

```bash
docker-compose ps
```

You should see 3 containers running:
- `teman-bicara-mysql`
- `teman-bicara-mailhog`
- `teman-bicara-waha`

### 6. Run Database Migrations

```bash
php artisan migrate
```

Expected output:
```
INFO  Running migrations.

2014_10_12_000000_create_users_table ................... DONE
2014_10_12_100000_create_password_reset_tokens_table ... DONE
2019_08_19_000000_create_failed_jobs_table ............. DONE
2019_12_14_000001_create_personal_access_tokens_table .. DONE
... (and more)
```

### 7. Run Database Seeders

```bash
php artisan db:seed
```

This will create:
- 1 test user: `user@example.com` / `password`
- 10 professionals with schedules
- 5 regular users with sample appointments
- 20 articles about mental health

### 8. Setup WhatsApp Session (WAHA)

1. Open WAHA Dashboard: http://localhost:3000
2. Login with:
   - Username: `admin`
   - Password: `asdf`
3. Click "Start" on session "default"
4. Scan QR code with your WhatsApp
5. Wait until status shows "WORKING"

### 9. Start Laravel Development Server

```bash
php artisan serve
```

Application will be available at: http://localhost:8000

### 10. Start Queue Worker (Optional)

For processing notifications (email & WhatsApp):

```bash
php artisan queue:work
```

Or for development (auto-reload):

```bash
php artisan queue:listen
```

## Verify Installation

### Check Services

1. **Laravel App**: http://localhost:8000
   - Should show landing page

2. **Mailhog UI**: http://localhost:8025
   - Should show email inbox (empty initially)

3. **WAHA Dashboard**: http://localhost:3000
   - Should show WhatsApp session status

### Test Login

1. Navigate to: http://localhost:8000/login
2. Login with:
   - Email: `user@example.com`
   - Password: `password`
3. Should redirect to dashboard

### Test Email Verification

1. Navigate to: http://localhost:8000/register
2. Register new user with any email
3. Check email in Mailhog: http://localhost:8025
4. Click verification link
5. Should redirect to dashboard

## Troubleshooting

### Database Connection Failed

```bash
# Check MySQL container
docker-compose ps

# View MySQL logs
docker-compose logs mysql

# Restart MySQL
docker-compose restart mysql
```

### Mailhog Not Receiving Emails

```bash
# Check Mailhog logs
docker-compose logs mailhog

# Verify MAIL_PORT in .env is 1025
# Verify MAIL_MAILER=smtp
```

### WAHA Not Working

```bash
# Check WAHA logs
docker-compose logs waha

# Restart WAHA
docker-compose restart waha

# Make sure you've scanned QR code
# Check session status in dashboard
```

### Queue Not Processing

```bash
# Make sure queue worker is running
php artisan queue:work

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Permission Issues

```bash
# Fix storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Clear All Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Fresh Installation

If you need to reset everything:

```bash
# Drop all tables and re-run migrations
php artisan migrate:fresh

# With seeders
php artisan migrate:fresh --seed

# Or completely remove and recreate database
docker-compose down -v
docker-compose up -d
php artisan migrate --seed
```

## Development Tips

### Use Tinker for Quick Testing

```bash
php artisan tinker

# Create test user
User::factory()->create(['email' => 'test@example.com']);

# Create professional
$user = User::factory()->professional()->create();
Professional::factory()->create(['user_id' => $user->id]);
```

### Watch for File Changes

For auto-reload during development:

```bash
# Watch queue
php artisan queue:listen

# In another terminal, run Laravel
php artisan serve
```

### Database GUI

You can use any MySQL client to connect:
- Host: `127.0.0.1`
- Port: `3305`
- Database: `mydatabase`
- Username: `myuser`
- Password: `mypassword`

Recommended tools:
- DataGrip
- TablePlus
- MySQL Workbench
- phpMyAdmin (can run in Docker)

## Next Steps

After successful installation:
1. Read [02-FEATURES.md](02-FEATURES.md) to understand all features
2. Read [04-AUTHENTICATION.md](04-AUTHENTICATION.md) for authentication details
3. Read [10-TESTING.md](10-TESTING.md) for testing guide
