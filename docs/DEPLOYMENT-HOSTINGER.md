# Deployment Guide - Hostinger (temanbicara.space)

## Overview
This guide covers the deployment process for Teman Bicara to Hostinger hosting with the domain `temanbicara.space`.

---

## Pre-Deployment Checklist

### 1. Hostinger Requirements
- [x] Domain: `temanbicara.space`
- [x] Hosting: Hostinger Web Hosting
- [ ] SSL Certificate (Let's Encrypt - Free)
- [ ] PHP Version: 8.2+ required
- [ ] MySQL Database created
- [ ] Database user with privileges

### 2. Application Requirements
- [ ] All environment variables configured
- [ ] Database credentials ready
- [ ] Email credentials (SMTP)
- [ ] Midtrans credentials (Payment Gateway)
- [ ] Production `.env` file prepared

### 3. Security Checklist
- [ ] Change all default passwords
- [ ] Generate new APP_KEY
- [ ] Set APP_DEBUG=false
- [ ] Configure CORS properly
- [ ] Enable HTTPS only
- [ ] Set secure session cookies

---

## Hostinger Configuration

### 1. Access Hostinger Control Panel (hPanel)

**URL:** https://hpanel.hostinger.com

**IMPORTANT:**
- **DO NOT use API key in production code**
- **DO NOT commit API key to Git**
- **Regenerate your API key immediately** (the one you shared is now public)

### 2. Create MySQL Database

1. Go to **Databases** → **MySQL Databases**
2. Click **Create Database**
3. Database Configuration:
   ```
   Database Name: u123456789_temanbicara
   Username: u123456789_admin
   Password: [Generate Strong Password]
   ```
4. Save credentials securely (use password manager)

### 3. Configure PHP Version

1. Go to **Advanced** → **PHP Configuration**
2. Select **PHP 8.2** or higher
3. Enable required extensions:
   ```
   ✓ BCMath
   ✓ Ctype
   ✓ Fileinfo
   ✓ JSON
   ✓ Mbstring
   ✓ OpenSSL
   ✓ PDO
   ✓ PDO_MySQL
   ✓ Tokenizer
   ✓ XML
   ✓ cURL
   ✓ GD
   ✓ Zip
   ```

### 4. Set PHP Configuration

Update `php.ini` settings:
```ini
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
max_input_time = 300
```

---

## File Structure on Hostinger

### Recommended Directory Structure

```
/home/u123456789/
├── domains/
│   └── temanbicara.space/
│       └── public_html/          # Laravel public folder contents go here
├── teman-bicara/                 # Laravel application root
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                   # Symlinked to public_html
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
└── .ssh/                          # SSH keys
```

---

## Deployment Methods

### Method 1: Git Deployment (Recommended)

#### Step 1: SSH Access Setup

1. **Enable SSH in Hostinger:**
   - Go to **Advanced** → **SSH Access**
   - Enable SSH
   - Note your SSH details:
     ```
     Host: ssh.hostinger.com
     Port: 65002
     Username: u123456789
     ```

2. **Add SSH Key to Hostinger:**
   ```bash
   # Generate SSH key locally (if you don't have one)
   ssh-keygen -t rsa -b 4096 -C "your@email.com"

   # Copy public key
   cat ~/.ssh/id_rsa.pub

   # Add to Hostinger: Advanced → SSH Access → SSH Keys
   ```

3. **Test SSH Connection:**
   ```bash
   ssh u123456789@ssh.hostinger.com -p 65002
   ```

#### Step 2: Clone Repository

```bash
# SSH to Hostinger
ssh u123456789@ssh.hostinger.com -p 65002

# Navigate to home directory
cd ~

# Clone repository
git clone git@github.com:ervandyr2512/product-management.git teman-bicara

# Navigate to project
cd teman-bicara
```

#### Step 3: Install Dependencies

```bash
# Install Composer dependencies
composer install --optimize-autoloader --no-dev

# Note: npm/node might not be available on shared hosting
# Build assets locally and commit the compiled files
```

#### Step 4: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit environment file
nano .env
```

**Production `.env` configuration:**
```env
APP_NAME="Teman Bicara"
APP_ENV=production
APP_KEY=          # Generate with: php artisan key:generate
APP_DEBUG=false
APP_URL=https://temanbicara.space

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_temanbicara
DB_USERNAME=u123456789_admin
DB_PASSWORD=[YOUR_DB_PASSWORD]

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=.temanbicara.space
SESSION_SECURE_COOKIE=true

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=[YOUR_EMAIL@temanbicara.space]
MAIL_PASSWORD=[YOUR_EMAIL_PASSWORD]
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@temanbicara.space"
MAIL_FROM_NAME="Teman Bicara"

# Midtrans Configuration
MIDTRANS_SERVER_KEY=[YOUR_MIDTRANS_SERVER_KEY]
MIDTRANS_CLIENT_KEY=[YOUR_MIDTRANS_CLIENT_KEY]
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

#### Step 5: Generate Application Key

```bash
php artisan key:generate
```

#### Step 6: Set Permissions

```bash
# Storage and cache permissions
chmod -R 775 storage bootstrap/cache
chmod -R 775 storage/framework
chmod -R 775 storage/logs

# If needed, change ownership
chown -R u123456789:u123456789 storage bootstrap/cache
```

#### Step 7: Run Migrations

```bash
# Run migrations
php artisan migrate --force

# Seed database (if needed)
php artisan db:seed --force
```

#### Step 8: Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

#### Step 9: Create Symbolic Link

```bash
# Remove default public_html if exists
rm -rf ~/domains/temanbicara.space/public_html

# Create symlink to Laravel public folder
ln -s ~/teman-bicara/public ~/domains/temanbicara.space/public_html
```

---

### Method 2: FTP/SFTP Upload (Alternative)

#### Step 1: Build Assets Locally

```bash
# On your local machine
npm install
npm run build

# This creates production assets in public/build
```

#### Step 2: Upload Files via FTP

1. **Use FileZilla or similar FTP client**
2. **Connection Details:**
   ```
   Protocol: SFTP
   Host: ssh.hostinger.com
   Port: 65002
   Username: u123456789
   Password: [Your Password]
   ```

3. **Upload Structure:**
   - Upload all Laravel files to `~/teman-bicara/`
   - Symlink public folder to `public_html`

4. **Set Permissions** via File Manager:
   - `storage/`: 775
   - `bootstrap/cache/`: 775

---

## SSL Certificate Setup

### 1. Enable SSL in Hostinger

1. Go to **SSL** section in hPanel
2. Select your domain: `temanbicara.space`
3. Click **Install SSL**
4. Choose **Free Let's Encrypt SSL**
5. Wait for installation (5-15 minutes)

### 2. Force HTTPS

Add to `.htaccess` in `public` folder:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Laravel routing
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## Domain Configuration

### 1. Point Domain to Hostinger

If domain is not already pointing to Hostinger:

1. Go to your domain registrar
2. Update nameservers to:
   ```
   ns1.dns-parking.com
   ns2.dns-parking.com
   ```
3. Wait for DNS propagation (up to 24 hours)

### 2. Add Domain in Hostinger

1. Go to **Domains** → **Add Domain**
2. Enter: `temanbicara.space`
3. Point to your hosting

### 3. Configure Subdomain (Optional)

For `www.temanbicara.space`:
- It should auto-redirect to main domain
- Or configure in **Domains** → **Subdomains**

---

## Email Setup

### 1. Create Email Accounts

1. Go to **Email** → **Email Accounts**
2. Create accounts:
   ```
   info@temanbicara.space
   support@temanbicara.space
   noreply@temanbicara.space
   ```

### 2. Configure SMTP in Laravel

Already configured in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@temanbicara.space
MAIL_PASSWORD=[PASSWORD]
MAIL_ENCRYPTION=tls
```

---

## Post-Deployment Tasks

### 1. Test Application

- [ ] Homepage loads correctly
- [ ] All navigation links work
- [ ] Database connection successful
- [ ] User registration works
- [ ] User login works
- [ ] Payment gateway works (test mode first)
- [ ] Email sending works
- [ ] File uploads work
- [ ] Dark mode toggle works
- [ ] Language switcher works

### 2. Configure Cron Jobs

1. Go to **Advanced** → **Cron Jobs**
2. Add Laravel scheduler:
   ```bash
   # Run every minute
   * * * * * cd ~/teman-bicara && php artisan schedule:run >> /dev/null 2>&1
   ```

### 3. Setup Queue Worker (If Using Queues)

```bash
# In SSH, run in background
nohup php artisan queue:work --daemon > /dev/null 2>&1 &
```

Or use Hostinger's cron:
```bash
*/5 * * * * cd ~/teman-bicara && php artisan queue:work --stop-when-empty
```

### 4. Setup Monitoring

1. **Application Monitoring:**
   - Enable error logging
   - Configure log rotation
   - Set up uptime monitoring (e.g., UptimeRobot)

2. **Performance Monitoring:**
   - Enable Laravel Telescope (development only)
   - Monitor database query performance
   - Check page load times

---

## Troubleshooting

### Issue: 500 Internal Server Error

**Solutions:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check PHP error log
tail -f ~/logs/error.log

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Check file permissions
chmod -R 775 storage bootstrap/cache
```

### Issue: Database Connection Error

**Check:**
1. Database credentials in `.env`
2. Database user has proper privileges
3. Database host is `localhost`
4. MySQL service is running

### Issue: Assets Not Loading

**Solutions:**
1. Check `.htaccess` file exists in public folder
2. Verify symbolic link is correct
3. Run `php artisan storage:link`
4. Check file permissions

### Issue: Session Issues

**Solutions:**
```bash
# Clear sessions
php artisan session:clear

# Check session driver in .env
SESSION_DRIVER=file

# Verify storage/framework/sessions is writable
chmod -R 775 storage/framework/sessions
```

---

## Backup Strategy

### 1. Database Backup

**Automated via Cron:**
```bash
# Daily at 2 AM
0 2 * * * mysqldump -u [user] -p[password] [database] > ~/backups/db_$(date +\%Y\%m\%d).sql
```

**Via Hostinger:**
- Use **Backups** feature in hPanel
- Schedule automatic backups

### 2. File Backup

```bash
# Weekly full backup
0 3 * * 0 tar -czf ~/backups/files_$(date +\%Y\%m\%d).tar.gz ~/teman-bicara
```

### 3. Download Backups

```bash
# From local machine
scp -P 65002 u123456789@ssh.hostinger.com:~/backups/* ./local-backups/
```

---

## Security Best Practices

### 1. Environment Security
- ✅ Never commit `.env` to Git
- ✅ Use strong, unique passwords
- ✅ Rotate credentials regularly
- ✅ Keep framework and dependencies updated

### 2. Application Security
- ✅ Enable CSRF protection (default in Laravel)
- ✅ Sanitize all user inputs
- ✅ Use prepared statements (Eloquent does this)
- ✅ Implement rate limiting
- ✅ Keep sessions secure

### 3. Server Security
- ✅ Keep PHP updated
- ✅ Disable directory listing
- ✅ Use HTTPS only
- ✅ Configure firewall rules
- ✅ Monitor access logs

---

## Maintenance Mode

### Enable Maintenance Mode
```bash
php artisan down --secret="my-secret-token"
```

### Access During Maintenance
```
https://temanbicara.space/my-secret-token
```

### Disable Maintenance Mode
```bash
php artisan up
```

---

## Performance Optimization

### 1. Enable OPcache

Add to `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

### 2. Configure Laravel Caching

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

### 3. Database Optimization

```bash
# Optimize tables
php artisan db:optimize

# Add database indexes (in migrations)
```

---

## Useful Commands

### SSH Quick Reference
```bash
# Connect to server
ssh u123456789@ssh.hostinger.com -p 65002

# Pull latest changes
cd ~/teman-bicara && git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan optimize:clear
php artisan optimize

# Check disk usage
du -sh ~/teman-bicara

# View real-time logs
tail -f storage/logs/laravel.log
```

---

## Support Resources

### Hostinger Support
- **Help Center:** https://support.hostinger.com
- **Live Chat:** Available 24/7
- **Email:** support@hostinger.com
- **Phone:** Check your account

### Laravel Documentation
- **Deployment:** https://laravel.com/docs/11.x/deployment
- **Configuration:** https://laravel.com/docs/11.x/configuration

---

## Next Steps After Deployment

1. [ ] Test all functionality thoroughly
2. [ ] Setup monitoring and alerts
3. [ ] Configure backup automation
4. [ ] Setup CDN (optional - Cloudflare)
5. [ ] Configure analytics (Google Analytics)
6. [ ] Setup error tracking (Sentry)
7. [ ] Perform security audit
8. [ ] Load testing
9. [ ] SEO optimization
10. [ ] Submit sitemap to search engines

---

*Last Updated: January 14, 2026*
