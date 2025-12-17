# Database Structure - Teman Bicara

## Database Overview

- **Database Type**: MySQL 8.0
- **Connection**: Port 3305 (Docker)
- **Charset**: utf8mb4_unicode_ci
- **Engine**: InnoDB

## Entity Relationship Diagram (ERD)

```
┌─────────────┐         ┌──────────────────┐         ┌────────────┐
│    Users    │────────▶│  Professionals   │────────▶│ Schedules  │
└─────────────┘         └──────────────────┘         └────────────┘
      │                          │                          │
      │                          │                          │
      │                          │                          │
      ▼                          ▼                          ▼
┌─────────────┐         ┌──────────────────┐         ┌────────────┐
│ Appointments│◀────────│    Payments      │         │   Carts    │
└─────────────┘         └──────────────────┘         └────────────┘
```

## Tables

### 1. users

Menyimpan data semua user (regular users dan professionals)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'professional') DEFAULT 'user',
    phone VARCHAR(20) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Indexes**:
- PRIMARY KEY (`id`)
- UNIQUE KEY (`email`)

**Relationships**:
- hasOne: Professional
- hasMany: Appointments
- hasMany: Carts
- hasMany: Payments

---

### 2. professionals

Menyimpan data profesional (psikiater, psikolog, conversationalist)

```sql
CREATE TABLE professionals (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('psychiatrist', 'psychologist', 'conversationalist') NOT NULL,
    license_number VARCHAR(255) NULL,
    bio TEXT NULL,
    specialization VARCHAR(255) NOT NULL,
    experience_years INT NOT NULL DEFAULT 0,
    rate_30min DECIMAL(10,2) DEFAULT 0,
    rate_60min DECIMAL(10,2) DEFAULT 0,
    profile_photo VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Indexes**:
- PRIMARY KEY (`id`)
- FOREIGN KEY (`user_id`)
- INDEX (`type`)
- INDEX (`is_active`)

**Relationships**:
- belongsTo: User
- hasMany: Schedules
- hasMany: Appointments

---

### 3. schedules

Menyimpan jadwal tersedia profesional

```sql
CREATE TABLE schedules (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    professional_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (professional_id) REFERENCES professionals(id) ON DELETE CASCADE,
    UNIQUE KEY unique_schedule (professional_id, date, start_time)
);
```

**Indexes**:
- PRIMARY KEY (`id`)
- FOREIGN KEY (`professional_id`)
- UNIQUE KEY (`professional_id`, `date`, `start_time`)
- INDEX (`date`)
- INDEX (`is_available`)

**Relationships**:
- belongsTo: Professional
- hasMany: Appointments
- hasMany: Carts

---

### 4. appointments

Menyimpan data janji temu/konsultasi

```sql
CREATE TABLE appointments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    professional_id BIGINT UNSIGNED NOT NULL,
    schedule_id BIGINT UNSIGNED NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration ENUM('30', '60') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    video_chat_link VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (professional_id) REFERENCES professionals(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE
);
```

**Indexes**:
- PRIMARY KEY (`id`)
- FOREIGN KEY (`user_id`)
- FOREIGN KEY (`professional_id`)
- FOREIGN KEY (`schedule_id`)
- INDEX (`status`)
- INDEX (`appointment_date`)

**Relationships**:
- belongsTo: User
- belongsTo: Professional
- belongsTo: Schedule
- hasOne: Payment

---

### 5. carts

Menyimpan item di shopping cart sebelum checkout

```sql
CREATE TABLE carts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    schedule_id BIGINT UNSIGNED NOT NULL,
    professional_id BIGINT UNSIGNED NOT NULL,
    duration ENUM('30', '60') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (professional_id) REFERENCES professionals(id) ON DELETE CASCADE
);
```

**Indexes**:
- PRIMARY KEY (`id`)
- FOREIGN KEY (`user_id`)
- FOREIGN KEY (`schedule_id`)
- FOREIGN KEY (`professional_id`)

**Relationships**:
- belongsTo: User
- belongsTo: Schedule
- belongsTo: Professional

---

### 6. payments

Menyimpan data pembayaran

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    appointment_id BIGINT UNSIGNED NOT NULL,
    payment_gateway_id VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50) NOT NULL,
    payment_details JSON NULL,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
);
```

**Indexes**:
- PRIMARY KEY (`id`)
- FOREIGN KEY (`user_id`)
- FOREIGN KEY (`appointment_id`)
- INDEX (`status`)
- INDEX (`payment_gateway_id`)

**Relationships**:
- belongsTo: User
- belongsTo: Appointment

---

### 7. articles

Menyimpan artikel/blog tentang kesehatan mental

```sql
CREATE TABLE articles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255) NULL,
    author VARCHAR(255) DEFAULT 'Admin',
    category ENUM('mental_health', 'anxiety', 'depression', 'stress', 'self_care', 'therapy', 'other') DEFAULT 'mental_health',
    is_published BOOLEAN DEFAULT false,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Indexes**:
- PRIMARY KEY (`id`)
- UNIQUE KEY (`slug`)
- INDEX (`category`)
- INDEX (`is_published`)
- INDEX (`published_at`)

**Relationships**:
- None (standalone table)

---

## Laravel Default Tables

### password_reset_tokens
```sql
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);
```

### failed_jobs
```sql
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### personal_access_tokens
```sql
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX tokenable (tokenable_type, tokenable_id)
);
```

### sessions
```sql
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,

    INDEX user_sessions (user_id),
    INDEX last_activity_index (last_activity)
);
```

### cache
```sql
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
);

CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
);
```

### jobs
```sql
CREATE TABLE jobs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,

    INDEX queue_index (queue)
);
```

---

## Database Relationships Summary

### User Relationships
```php
// User.php
public function professional() // hasOne
public function appointments() // hasMany
public function carts() // hasMany
public function payments() // hasMany
```

### Professional Relationships
```php
// Professional.php
public function user() // belongsTo
public function schedules() // hasMany
public function appointments() // hasMany
```

### Schedule Relationships
```php
// Schedule.php
public function professional() // belongsTo
public function appointments() // hasMany
public function carts() // hasMany
```

### Appointment Relationships
```php
// Appointment.php
public function user() // belongsTo
public function professional() // belongsTo
public function schedule() // belongsTo
public function payment() // hasOne
```

### Cart Relationships
```php
// Cart.php
public function user() // belongsTo
public function schedule() // belongsTo
public function professional() // belongsTo
```

### Payment Relationships
```php
// Payment.php
public function user() // belongsTo
public function appointment() // belongsTo
```

---

## Common Queries

### Get all available schedules for a professional
```php
$schedules = Schedule::where('professional_id', $professionalId)
    ->where('is_available', true)
    ->where('date', '>=', now()->toDateString())
    ->orderBy('date')
    ->orderBy('start_time')
    ->get();
```

### Get user appointments with professional info
```php
$appointments = Appointment::with(['professional.user', 'payment'])
    ->where('user_id', $userId)
    ->latest()
    ->get();
```

### Get professional with schedules
```php
$professional = Professional::with(['user', 'schedules' => function($query) {
    $query->where('is_available', true)
          ->where('date', '>=', now()->toDateString());
}])->findOrFail($id);
```

### Get published articles
```php
$articles = Article::published()
    ->latest('published_at')
    ->paginate(9);
```

---

## Seeder Data

After running `php artisan db:seed`:

- **Users**: 16 (1 test user + 10 professionals + 5 regular users)
- **Professionals**: 10 (mixed types)
- **Schedules**: 100 (10 per professional)
- **Appointments**: 5 (sample bookings)
- **Articles**: 20 (various categories)

---

## Database Backup

### Manual Backup
```bash
docker exec teman-bicara-mysql mysqldump -u myuser -pmypassword mydatabase > backup.sql
```

### Restore Backup
```bash
docker exec -i teman-bicara-mysql mysql -u myuser -pmypassword mydatabase < backup.sql
```

### Automated Backup (Production)
Add to cron:
```bash
0 2 * * * cd /path/to/app && docker exec teman-bicara-mysql mysqldump -u myuser -pmypassword mydatabase > storage/backups/backup_$(date +\%Y\%m\%d).sql
```

---

## Next Documentation

- [04-AUTHENTICATION.md](04-AUTHENTICATION.md) - Authentication system
- [05-BOOKING-FLOW.md](05-BOOKING-FLOW.md) - Booking process
