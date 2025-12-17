# Quick Reference Guide

Fast reference for common tasks in Teman Bicara development.

---

## Table of Contents

1. [Development Setup](#development-setup)
2. [Common Commands](#common-commands)
3. [Database Operations](#database-operations)
4. [Notification Testing](#notification-testing)
5. [Git Workflow](#git-workflow)
6. [Debugging](#debugging)
7. [Configuration](#configuration)

---

## Development Setup

### First Time Setup

```bash
# Clone repository
git clone git@github.com:ervandyr2512/product-management.git
cd product-management

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database
touch database/database.sqlite
php artisan migrate --seed

# Build assets
npm run dev

# Start services
docker-compose up -d  # Mailhog + WAHA
php artisan serve     # Laravel server
```

### Environment Variables (.env)

```env
# App
APP_NAME="Teman Bicara"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Database (SQLite)
DB_CONNECTION=sqlite

# Mail (Mailhog)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@temanbicara.com"

# WhatsApp (WAHA)
WAHA_URL=http://localhost:3000
WAHA_API_KEY=asdf
WAHA_SESSION=default
```

---

## Common Commands

### Laravel Artisan

```bash
# Development
php artisan serve                    # Start development server
php artisan tinker                   # Interactive shell

# Cache
php artisan optimize:clear          # Clear all caches
php artisan config:clear            # Clear config cache
php artisan cache:clear             # Clear app cache
php artisan view:clear              # Clear compiled views
php artisan route:clear             # Clear route cache

# Database
php artisan migrate                 # Run migrations
php artisan migrate:fresh --seed    # Fresh database with seeds
php artisan db:seed                 # Run seeders only

# Queue
php artisan queue:work              # Process queued jobs
php artisan queue:failed            # List failed jobs
php artisan queue:retry all         # Retry failed jobs

# Info
php artisan route:list              # List all routes
php artisan config:show             # Show configuration
php artisan db:show                 # Show database info
```

### NPM/Vite

```bash
npm run dev             # Watch and compile assets
npm run build           # Build for production
npm run preview         # Preview production build
```

### Docker Compose

```bash
docker-compose up -d                  # Start all services
docker-compose up -d mailhog          # Start Mailhog only
docker-compose up -d waha             # Start WAHA only
docker-compose ps                     # List running services
docker-compose logs -f waha           # View WAHA logs
docker-compose down                   # Stop all services
docker-compose restart waha           # Restart WAHA
```

---

## Database Operations

### Reset Database

```bash
# Full reset (destroys all data)
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# Check migration status
php artisan migrate:status
```

### Create New Models

```bash
# Model + Migration + Controller + Seeder + Factory
php artisan make:model Product -mcfs

# Individual components
php artisan make:model Product
php artisan make:migration create_products_table
php artisan make:controller ProductController
php artisan make:seeder ProductSeeder
php artisan make:factory ProductFactory
```

### Tinker Quick Commands

```bash
php artisan tinker
```

```php
// Users
User::count()
User::first()
User::where('role', 'client')->get()
User::factory()->create(['role' => 'professional'])

// Appointments
Appointment::with('user', 'professional')->get()
Appointment::where('status', 'confirmed')->count()

// Notifications (test)
$user = User::first();
$appointment = Appointment::first();
$user->notify(new \App\Notifications\AppointmentConfirmed($appointment));

// Update phone format
DB::table('users')
    ->where('phone', 'LIKE', '08%')
    ->update(['phone' => DB::raw("CONCAT('62', SUBSTRING(phone, 2))")]);
```

---

## Notification Testing

### Email (Mailhog)

```bash
# 1. Start Mailhog
docker-compose up -d mailhog

# 2. Open web interface
open http://127.0.0.1:8025

# 3. Send test email
php artisan tinker
>>> $user = User::first();
>>> $appointment = Appointment::first();
>>> $user->notify(new \App\Notifications\AppointmentConfirmed($appointment));

# 4. Check Mailhog for received email
```

### WhatsApp (WAHA)

```bash
# 1. Start WAHA
docker-compose up -d waha

# 2. Connect WhatsApp session
open http://localhost:3000
# Login: admin / asdf
# Start session "default" and scan QR code

# 3. Test API
curl -X POST http://localhost:3000/api/sendText \
  -H "X-Api-Key: asdf" \
  -H "Content-Type: application/json" \
  -d '{
    "session": "default",
    "chatId": "6281234567890@c.us",
    "text": "Test message"
  }'

# 4. Test via Laravel
php artisan tinker
>>> $user = User::where('phone', '6281234567890')->first();
>>> $appointment = $user->appointments()->first();
>>> $user->notify(new \App\Notifications\AppointmentConfirmed($appointment));
```

### Direct Test Script

Create `test-notification.php`:
```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('phone', '6281234567890')->first();
$appointment = $user->appointments()->latest()->first();
$user->notify(new \App\Notifications\AppointmentConfirmed($appointment));

echo "Notification sent!\n";
```

Run:
```bash
php test-notification.php
```

---

## Git Workflow

### Initial Setup

```bash
git init
git remote add origin git@github.com:ervandyr2512/product-management.git
git add .
git commit -m "Initial commit"
git push -u origin main
```

### Daily Workflow

```bash
# Start work
git pull origin main
git checkout -b feature/new-feature

# Make changes
git add .
git commit -m "Add new feature"

# Push to remote
git push origin feature/new-feature

# Create pull request on GitHub
# After merge, cleanup
git checkout main
git pull origin main
git branch -d feature/new-feature
```

### Commit Message Format

```bash
# Feature
git commit -m "Add country code dropdown to registration form"

# Fix
git commit -m "Fix payment timeout issue with WhatsApp API"

# Update
git commit -m "Update email notification content"

# Refactor
git commit -m "Refactor phone number formatting logic"

# Docs
git commit -m "Update notification documentation"
```

### Useful Git Commands

```bash
# Status
git status
git log --oneline -10

# Undo
git reset HEAD~1               # Undo last commit (keep changes)
git reset --hard HEAD~1        # Undo last commit (discard changes)
git checkout -- file.php       # Discard changes in file

# Branches
git branch                     # List branches
git branch -a                  # List all branches (including remote)
git branch -d branch-name      # Delete branch
git checkout -b new-branch     # Create and switch to branch

# Stash
git stash                      # Save changes temporarily
git stash pop                  # Restore stashed changes
git stash list                 # List stashes
```

---

## Debugging

### Laravel Logs

```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Search for errors
grep ERROR storage/logs/laravel.log

# Search for WhatsApp logs
grep "WhatsApp" storage/logs/laravel.log

# Last 50 lines
tail -50 storage/logs/laravel.log

# Clear logs
> storage/logs/laravel.log
```

### Enable Debug Mode

```env
# .env
APP_DEBUG=true
LOG_LEVEL=debug
```

⚠️ **Never enable in production!**

### Query Debugging

```php
// In tinker or controller
DB::enableQueryLog();

// Run your queries
User::with('appointments')->get();

// View queries
dd(DB::getQueryLog());
```

### Debug Bar (Optional)

```bash
composer require barryvdh/laravel-debugbar --dev
```

Auto-shows at bottom of page with:
- Queries
- Request/Response
- Views
- Routes
- Logs

### Common Issues

**1. Permission Denied**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**2. Class Not Found**
```bash
composer dump-autoload
php artisan optimize:clear
```

**3. Migration Errors**
```bash
php artisan migrate:fresh --seed
```

**4. Assets Not Loading**
```bash
npm run build
php artisan storage:link
```

---

## Configuration

### Check Configuration

```bash
# View all config
php artisan config:show

# Specific config
php artisan config:show database
php artisan config:show mail
php artisan config:show services.waha
```

### Config Files

```
config/
├── app.php           # App settings (name, timezone, locale)
├── auth.php          # Authentication guards, providers
├── database.php      # Database connections
├── mail.php          # Email settings
├── services.php      # Third-party services (WAHA)
└── queue.php         # Queue configuration
```

### Important Settings

**Timezone:**
```php
// config/app.php
'timezone' => 'Asia/Jakarta',
```

**Locale:**
```php
// config/app.php
'locale' => 'id',
'fallback_locale' => 'en',
```

**Session:**
```php
// config/session.php
'lifetime' => 120,  // minutes
'expire_on_close' => false,
```

---

## API Endpoints (Internal)

### WAHA API

```bash
# Check session
GET http://localhost:3000/api/sessions/default
Headers: X-Api-Key: asdf

# Send text message
POST http://localhost:3000/api/sendText
Headers: X-Api-Key: asdf, Content-Type: application/json
Body: {
  "session": "default",
  "chatId": "6281234567890@c.us",
  "text": "Hello!"
}

# Get QR code
GET http://localhost:3000/api/sessions/default/qr

# Start session
POST http://localhost:3000/api/sessions/default/start

# Stop session
POST http://localhost:3000/api/sessions/default/stop
```

### Testing with cURL

```bash
# Test WAHA is running
curl http://localhost:3000/api/sessions

# Test with authentication
curl -H "X-Api-Key: asdf" http://localhost:3000/api/sessions/default

# Send test message
curl -X POST http://localhost:3000/api/sendText \
  -H "X-Api-Key: asdf" \
  -H "Content-Type: application/json" \
  -d '{
    "session": "default",
    "chatId": "6281234567890@c.us",
    "text": "Test from cURL"
  }'
```

---

## Performance Tips

### Optimization Commands

```bash
# Production optimization
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear optimization (development)
php artisan optimize:clear
```

### Eager Loading

```php
// ❌ N+1 Query Problem
$appointments = Appointment::all();
foreach ($appointments as $appointment) {
    echo $appointment->professional->name; // Query each time
}

// ✅ Eager Loading
$appointments = Appointment::with('professional.user')->get();
foreach ($appointments as $appointment) {
    echo $appointment->professional->user->name; // Already loaded
}
```

### Caching

```php
// Cache for 1 hour
$professionals = Cache::remember('professionals', 3600, function () {
    return Professional::with('user')->get();
});

// Clear cache
Cache::forget('professionals');
```

---

## Useful Aliases

Add to `~/.bashrc` or `~/.zshrc`:

```bash
# Laravel
alias pa='php artisan'
alias pas='php artisan serve'
alias pat='php artisan tinker'
alias pam='php artisan migrate'
alias pamf='php artisan migrate:fresh --seed'
alias paoc='php artisan optimize:clear'

# Composer
alias ci='composer install'
alias cu='composer update'
alias cda='composer dump-autoload'

# NPM
alias nrd='npm run dev'
alias nrb='npm run build'

# Docker
alias dcu='docker-compose up -d'
alias dcd='docker-compose down'
alias dcp='docker-compose ps'
alias dcl='docker-compose logs -f'

# Logs
alias ltail='tail -f storage/logs/laravel.log'
alias lgrep='grep -r --include="*.log"'
```

Usage:
```bash
pa migrate:fresh --seed
dcu mailhog
ltail
```

---

## Cheat Sheet

### Most Common Tasks

| Task | Command |
|------|---------|
| Start server | `php artisan serve` |
| Run migrations | `php artisan migrate` |
| Reset database | `php artisan migrate:fresh --seed` |
| Clear cache | `php artisan optimize:clear` |
| Interactive shell | `php artisan tinker` |
| Watch assets | `npm run dev` |
| View logs | `tail -f storage/logs/laravel.log` |
| Start services | `docker-compose up -d` |
| Test email | Open `http://127.0.0.1:8025` |
| Test WhatsApp | Open `http://localhost:3000` |

### File Locations

| What | Where |
|------|-------|
| Controllers | `app/Http/Controllers/` |
| Models | `app/Models/` |
| Views | `resources/views/` |
| Routes | `routes/web.php` |
| Migrations | `database/migrations/` |
| Seeders | `database/seeders/` |
| Config | `config/` |
| Logs | `storage/logs/laravel.log` |
| Notifications | `app/Notifications/` |
| Tests | `tests/Feature/` |

---

## Emergency Fixes

### Site Down

```bash
# 1. Check logs
tail -50 storage/logs/laravel.log

# 2. Clear all caches
php artisan optimize:clear

# 3. Fix permissions
chmod -R 775 storage bootstrap/cache

# 4. Restart services
docker-compose restart
php artisan serve
```

### Database Corrupted

```bash
# SQLite
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate:fresh --seed

# Other databases
php artisan migrate:fresh --seed
```

### WhatsApp Not Working

```bash
# 1. Check WAHA
docker-compose ps waha

# 2. Restart WAHA
docker-compose restart waha

# 3. Check session
curl -H "X-Api-Key: asdf" http://localhost:3000/api/sessions/default

# 4. Rescan QR code
open http://localhost:3000
```

### Mailhog Not Working

```bash
# 1. Restart Mailhog
docker-compose restart mailhog

# 2. Check .env
grep MAIL_ .env

# 3. Clear config
php artisan config:clear

# 4. Test connection
telnet 127.0.0.1 1025
```

---

**Last Updated:** 2025-12-17
