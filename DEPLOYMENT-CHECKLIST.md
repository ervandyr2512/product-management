# Deployment Checklist - temanbicara.space

## ⚠️ URGENT: Security First!

- [ ] **REGENERATE Hostinger API Key** (the one shared is now public!)
  - Login to Hostinger
  - Go to API section
  - Delete old key
  - Generate new key
  - Store securely (never share again)

---

## Phase 1: Pre-Deployment (Local)

### Build Assets
- [ ] Run `npm install`
- [ ] Run `npm run build`
- [ ] Commit built assets to Git
- [ ] Push to GitHub

### Database Preparation
- [ ] Export seeded data (if needed)
- [ ] Prepare migration files
- [ ] Test migrations on fresh database locally

### Code Review
- [ ] All features working locally
- [ ] No sensitive data in code
- [ ] `.env` not in Git
- [ ] `.gitignore` properly configured
- [ ] All dependencies in `composer.json`

---

## Phase 2: Hostinger Setup

### Access & SSH
- [ ] Login to Hostinger hPanel
- [ ] Enable SSH access
- [ ] Generate/Add SSH key
- [ ] Test SSH connection:
  ```bash
  ssh u[YOUR_NUMBER]@ssh.hostinger.com -p 65002
  ```

### Database Creation
- [ ] Create MySQL database
- [ ] Create database user
- [ ] Grant all privileges
- [ ] Save credentials securely
- [ ] Test connection from phpMyAdmin

### PHP Configuration
- [ ] Set PHP version to 8.2+
- [ ] Enable required extensions:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - PDO_MySQL
  - Tokenizer
  - XML
  - cURL
  - GD
  - Zip

- [ ] Update PHP settings:
  ```ini
  memory_limit = 256M
  upload_max_filesize = 64M
  post_max_size = 64M
  max_execution_time = 300
  ```

### Email Setup
- [ ] Create email: `noreply@temanbicara.space`
- [ ] Create email: `info@temanbicara.space`
- [ ] Create email: `support@temanbicara.space`
- [ ] Save email passwords
- [ ] Test SMTP settings

---

## Phase 3: Deploy Application

### Clone Repository
```bash
# SSH to Hostinger
ssh u[YOUR_NUMBER]@ssh.hostinger.com -p 65002

# Clone repo
cd ~
git clone git@github.com:ervandyr2512/product-management.git teman-bicara
cd teman-bicara
```

- [ ] Repository cloned successfully
- [ ] All files present

### Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

- [ ] Composer dependencies installed
- [ ] No errors during installation

### Environment Configuration
```bash
# Copy production env template
cp .env.production.example .env

# Edit environment file
nano .env
```

**Fill in these values:**
- [ ] `APP_KEY=` (will generate next)
- [ ] `DB_DATABASE=` (your database name)
- [ ] `DB_USERNAME=` (your database user)
- [ ] `DB_PASSWORD=` (your database password)
- [ ] `MAIL_USERNAME=` (noreply@temanbicara.space)
- [ ] `MAIL_PASSWORD=` (email password)
- [ ] `MIDTRANS_SERVER_KEY=` (from Midtrans dashboard)
- [ ] `MIDTRANS_CLIENT_KEY=` (from Midtrans dashboard)

### Generate Application Key
```bash
php artisan key:generate
```

- [ ] Application key generated
- [ ] `.env` file updated

### Set Permissions
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

- [ ] Storage writable
- [ ] Cache writable

### Run Migrations
```bash
php artisan migrate --force
```

- [ ] Migrations successful
- [ ] All tables created
- [ ] No errors

### Seed Database (Optional)
```bash
php artisan db:seed --force
```

- [ ] Seeders run successfully
- [ ] Sample data populated (if needed)

### Create Symlink
```bash
# Remove default public_html
rm -rf ~/domains/temanbicara.space/public_html

# Create symlink
ln -s ~/teman-bicara/public ~/domains/temanbicara.space/public_html
```

- [ ] Symlink created
- [ ] public_html points to Laravel public folder

### Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
composer dump-autoload --optimize
```

- [ ] Configuration cached
- [ ] Routes cached
- [ ] Views cached
- [ ] Storage linked
- [ ] Autoloader optimized

---

## Phase 4: SSL & Security

### Enable SSL
- [ ] Go to SSL section in hPanel
- [ ] Select domain: temanbicara.space
- [ ] Install Let's Encrypt SSL
- [ ] Wait for activation (5-15 min)
- [ ] Verify HTTPS works

### Force HTTPS
- [ ] Add HTTPS redirect to `.htaccess`
- [ ] Test HTTP → HTTPS redirect
- [ ] Update `APP_URL` in `.env` to https://

### Security Headers
Add to `public/.htaccess`:
```apache
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

- [ ] Security headers added
- [ ] Test with securityheaders.com

---

## Phase 5: Testing

### Basic Functionality
- [ ] Homepage loads (https://temanbicara.space)
- [ ] All pages accessible
- [ ] Navigation works
- [ ] Images load
- [ ] CSS/JS load correctly

### Authentication
- [ ] Registration works
- [ ] Login works
- [ ] Logout works
- [ ] Password reset works

### Core Features
- [ ] Browse professionals
- [ ] View professional details
- [ ] Browse articles
- [ ] View article details
- [ ] Contact form works
- [ ] Email sending works

### User Features (After Login)
- [ ] Book appointment
- [ ] Add to favorites
- [ ] Add to cart
- [ ] Checkout works
- [ ] Payment gateway works (test mode)
- [ ] Chat functionality
- [ ] Profile update

### Multilingual
- [ ] Language switcher works
- [ ] Indonesian content displays
- [ ] English content displays
- [ ] All pages translated

### Dark Mode
- [ ] Dark mode toggle works
- [ ] Preferences saved
- [ ] All pages support dark mode

### Responsive Design
- [ ] Test on mobile (375px)
- [ ] Test on tablet (768px)
- [ ] Test on desktop (1920px)

---

## Phase 6: Production Setup

### Cron Jobs
Add in Hostinger cron jobs:
```bash
* * * * * cd ~/teman-bicara && php artisan schedule:run >> /dev/null 2>&1
```

- [ ] Cron job added
- [ ] Scheduler running

### Queue Worker (If Using)
```bash
*/5 * * * * cd ~/teman-bicara && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

- [ ] Queue worker configured
- [ ] Jobs processing

### Logging
- [ ] Check log file exists: `storage/logs/laravel.log`
- [ ] Verify errors are being logged
- [ ] Setup log rotation if needed

### Backups
- [ ] Enable Hostinger automatic backups
- [ ] Setup database backup cron
- [ ] Test backup restoration

---

## Phase 7: Monitoring & Analytics

### Error Monitoring
- [ ] Setup error email notifications
- [ ] Monitor `storage/logs/laravel.log`
- [ ] Setup Sentry (optional)

### Uptime Monitoring
- [ ] Add to UptimeRobot (free)
- [ ] Configure alerts
- [ ] Test notifications

### Performance
- [ ] Test page load speed (GTmetrix)
- [ ] Optimize images
- [ ] Enable caching
- [ ] Check database queries

### Analytics
- [ ] Add Google Analytics
- [ ] Add Google Search Console
- [ ] Submit sitemap
- [ ] Verify ownership

---

## Phase 8: Go Live!

### Final Checks
- [ ] All tests passing
- [ ] No errors in logs
- [ ] SSL working
- [ ] Emails sending
- [ ] Payments working (production mode)

### DNS & Domain
- [ ] Domain pointing to Hostinger
- [ ] SSL certificate active
- [ ] WWW redirect working
- [ ] DNS propagation complete

### Communication
- [ ] Notify team deployment complete
- [ ] Update documentation
- [ ] Prepare support team

### Post-Launch
- [ ] Monitor for 24 hours
- [ ] Check error logs
- [ ] Monitor traffic
- [ ] Test user flows

---

## Emergency Rollback Plan

If something goes wrong:

```bash
# Enable maintenance mode
php artisan down

# Restore database backup
mysql -u [user] -p [database] < backup.sql

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Pull previous version
git checkout [previous-commit-hash]
composer install --no-dev
php artisan migrate:rollback

# Disable maintenance mode
php artisan up
```

---

## Support Contacts

### Hostinger Support
- Live Chat: Available 24/7 in hPanel
- Email: support@hostinger.com
- Knowledge Base: support.hostinger.com

### Payment Gateway (Midtrans)
- Dashboard: dashboard.midtrans.com
- Support: support@midtrans.com
- Docs: docs.midtrans.com

### Emergency Contacts
- Your Name: [Your Phone]
- Technical Lead: [Phone]
- Backup: [Phone]

---

## Notes & Reminders

- ⚠️ Never share API keys or passwords
- 🔒 Always use HTTPS
- 💾 Backup before major changes
- 📊 Monitor logs regularly
- 🔄 Keep dependencies updated
- 🧪 Test in staging first (if available)

---

**Deployment Date:** _________________

**Deployed By:** _________________

**Version:** _________________

**Status:** [ ] Success  [ ] Issues  [ ] Rolled Back

**Notes:**
_____________________________________________
_____________________________________________
_____________________________________________

---

*Good luck with your deployment! 🚀*
