# Auto-Deployment Guide - GitHub to Hostinger

## Overview
This guide covers setting up automatic deployment from GitHub to Hostinger whenever you push to the main branch.

---

## Available Methods

### Method 1: GitHub Actions + SSH (Recommended)
- ✅ Most flexible and powerful
- ✅ Can run tests before deploy
- ✅ Full control over deployment process
- ✅ Free for public repositories
- ❌ Requires SSH access to Hostinger

### Method 2: GitHub Webhooks + Deploy Script
- ✅ Simple setup
- ✅ Fast deployment
- ❌ Less control over process
- ❌ Requires publicly accessible endpoint

### Method 3: Hostinger Git Integration
- ✅ Native Hostinger feature
- ✅ Simple setup via hPanel
- ❌ Limited to basic deployments
- ❌ May not be available on all plans

---

## Method 1: GitHub Actions (Recommended)

### Step 1: Generate SSH Key for Deployment

On your **local machine**:

```bash
# Generate new SSH key specifically for deployment
ssh-keygen -t ed25519 -C "deploy@temanbicara.space" -f ~/.ssh/hostinger_deploy

# This creates two files:
# - hostinger_deploy (private key)
# - hostinger_deploy.pub (public key)
```

### Step 2: Add Public Key to Hostinger

1. **Copy public key:**
   ```bash
   cat ~/.ssh/hostinger_deploy.pub
   ```

2. **Add to Hostinger:**
   - Login to hPanel
   - Go to **Advanced** → **SSH Access**
   - Click **Manage SSH Keys**
   - Click **Add New Key**
   - Paste public key
   - Name it: `GitHub Actions Deploy`

3. **Test connection:**
   ```bash
   ssh -i ~/.ssh/hostinger_deploy u123456789@ssh.hostinger.com -p 65002
   ```

### Step 3: Add Private Key to GitHub Secrets

1. **Copy private key:**
   ```bash
   cat ~/.ssh/hostinger_deploy
   ```

2. **Add to GitHub:**
   - Go to your repository: https://github.com/ervandyr2512/product-management
   - Click **Settings** → **Secrets and variables** → **Actions**
   - Click **New repository secret**
   - Add these secrets:

   | Name | Value |
   |------|-------|
   | `SSH_PRIVATE_KEY` | Content of hostinger_deploy file |
   | `SSH_HOST` | `ssh.hostinger.com` |
   | `SSH_PORT` | `65002` |
   | `SSH_USER` | Your Hostinger username (e.g., `u123456789`) |
   | `DEPLOY_PATH` | `/home/u123456789/teman-bicara` |

### Step 4: Create GitHub Actions Workflow

Create file: `.github/workflows/deploy.yml`

```yaml
name: Deploy to Hostinger

on:
  push:
    branches:
      - main

jobs:
  deploy:
    name: Deploy to Production
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, xml, ctype, curl, gd, zip, pdo_mysql
          coverage: none

      - name: Install Composer dependencies
        run: composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'

      - name: Install NPM dependencies
        run: npm ci

      - name: Build assets
        run: npm run build

      - name: Run tests
        run: php artisan test

      - name: Deploy to Hostinger
        if: success()
        uses: easingthemes/ssh-deploy@v4.1.10
        env:
          SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
          REMOTE_HOST: ${{ secrets.SSH_HOST }}
          REMOTE_PORT: ${{ secrets.SSH_PORT }}
          REMOTE_USER: ${{ secrets.SSH_USER }}
          TARGET: ${{ secrets.DEPLOY_PATH }}
          EXCLUDE: "/node_modules/, /.git/, /.github/, /tests/, /storage/logs/, /storage/framework/cache/, /storage/framework/sessions/, /storage/framework/views/"

      - name: Run post-deployment commands
        uses: appleboy/ssh-action@v1.0.0
        with:
          host: ${{ secrets.SSH_HOST }}
          port: ${{ secrets.SSH_PORT }}
          username: ${{ secrets.SSH_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd ${{ secrets.DEPLOY_PATH }}

            # Put site in maintenance mode
            php artisan down

            # Pull latest changes (if any manual changes)
            git pull origin main

            # Install/update dependencies
            composer install --no-dev --optimize-autoloader --no-interaction

            # Clear all caches
            php artisan cache:clear
            php artisan config:clear
            php artisan route:clear
            php artisan view:clear

            # Run migrations
            php artisan migrate --force

            # Optimize for production
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache

            # Restart queue workers (if using)
            php artisan queue:restart

            # Bring site back online
            php artisan up

            echo "✅ Deployment completed successfully!"

      - name: Notify on success
        if: success()
        run: echo "🚀 Deployment to production successful!"

      - name: Notify on failure
        if: failure()
        run: echo "❌ Deployment failed! Check logs."
```

### Step 5: Create `.github/workflows` Directory

```bash
# In your local repository
mkdir -p .github/workflows
```

### Step 6: Add Workflow File

Copy the YAML content above and save it to `.github/workflows/deploy.yml`

### Step 7: Commit and Push

```bash
git add .github/workflows/deploy.yml
git commit -m "Add GitHub Actions auto-deployment workflow"
git push origin main
```

### Step 8: Monitor Deployment

1. Go to your GitHub repository
2. Click **Actions** tab
3. Watch the deployment progress in real-time
4. Check for any errors

---

## Method 2: Simplified Deployment (Without Tests)

If you want faster deployment without running tests:

`.github/workflows/deploy-simple.yml`:

```yaml
name: Quick Deploy

on:
  push:
    branches:
      - main

jobs:
  deploy:
    name: Deploy
    runs-on: ubuntu-latest

    steps:
      - name: Deploy via SSH
        uses: appleboy/ssh-action@v1.0.0
        with:
          host: ${{ secrets.SSH_HOST }}
          port: ${{ secrets.SSH_PORT }}
          username: ${{ secrets.SSH_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd ${{ secrets.DEPLOY_PATH }}

            # Maintenance mode
            php artisan down || true

            # Pull changes
            git pull origin main

            # Install dependencies
            composer install --no-dev --optimize-autoloader

            # Build assets (if Node.js available)
            # npm install && npm run build

            # Run migrations
            php artisan migrate --force

            # Clear and cache
            php artisan optimize:clear
            php artisan optimize

            # Back online
            php artisan up

            echo "Deployed at $(date)"
```

---

## Method 3: Deploy Script on Server

### Step 1: Create Deploy Script on Hostinger

SSH to Hostinger and create `/home/u123456789/deploy.sh`:

```bash
#!/bin/bash

# Configuration
APP_PATH="/home/u123456789/teman-bicara"
BRANCH="main"
LOG_FILE="/home/u123456789/deploy.log"

# Log function
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "🚀 Starting deployment..."

# Navigate to app directory
cd "$APP_PATH" || exit 1

# Enable maintenance mode
log "📋 Enabling maintenance mode..."
php artisan down || true

# Pull latest changes
log "📥 Pulling latest changes from GitHub..."
git fetch origin
git reset --hard origin/$BRANCH

# Install/Update Composer dependencies
log "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Run database migrations
log "🗄️ Running database migrations..."
php artisan migrate --force

# Clear all caches
log "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
log "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
log "🔒 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Restart queue workers (if using queues)
log "🔄 Restarting queue workers..."
php artisan queue:restart || true

# Disable maintenance mode
log "✅ Disabling maintenance mode..."
php artisan up

log "🎉 Deployment completed successfully!"

# Send notification (optional)
# curl -X POST "YOUR_SLACK_WEBHOOK_URL" -d '{"text":"Deployment completed!"}'
```

### Step 2: Make Script Executable

```bash
chmod +x /home/u123456789/deploy.sh
```

### Step 3: Setup GitHub Webhook

1. **Get webhook secret:**
   ```bash
   # Generate random secret
   openssl rand -hex 20
   # Save this secret!
   ```

2. **Create webhook endpoint:**

   Create `public/deploy-webhook.php` in your Laravel app:

   ```php
   <?php

   // Webhook secret (same as GitHub)
   define('WEBHOOK_SECRET', 'your-random-secret-here');

   // Get payload
   $payload = file_get_contents('php://input');
   $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

   // Verify signature
   $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, WEBHOOK_SECRET);

   if (!hash_equals($expectedSignature, $signature)) {
       http_response_code(403);
       die('Invalid signature');
   }

   // Decode payload
   $data = json_decode($payload, true);

   // Only deploy on push to main branch
   if (isset($data['ref']) && $data['ref'] === 'refs/heads/main') {
       // Execute deploy script in background
       exec('/home/u123456789/deploy.sh > /dev/null 2>&1 &');

       http_response_code(200);
       echo json_encode(['status' => 'Deployment triggered']);
   } else {
       http_response_code(200);
       echo json_encode(['status' => 'Ignored (not main branch)']);
   }
   ```

3. **Add webhook to GitHub:**
   - Go to repository Settings → Webhooks
   - Click **Add webhook**
   - Payload URL: `https://temanbicara.space/deploy-webhook.php`
   - Content type: `application/json`
   - Secret: Your webhook secret
   - Events: Just the `push` event
   - Click **Add webhook**

4. **Test webhook:**
   - Make a small commit and push
   - Check webhook deliveries in GitHub
   - Check deploy log: `tail -f /home/u123456789/deploy.log`

---

## Method 4: Hostinger Git Integration (If Available)

Some Hostinger plans have built-in Git integration:

1. **In hPanel:**
   - Go to **Files** → **Git**
   - Click **Create Repository**
   - Connect to GitHub
   - Select repository
   - Configure auto-pull on push

2. **Benefits:**
   - No manual setup needed
   - Automatic pull on push
   - Simple interface

3. **Limitations:**
   - Doesn't run migrations automatically
   - Doesn't clear caches
   - May not be available on all plans

---

## Comparison of Methods

| Feature | GitHub Actions | Webhook Script | Hostinger Git |
|---------|---------------|----------------|---------------|
| **Runs Tests** | ✅ Yes | ❌ No | ❌ No |
| **Build Assets** | ✅ Yes | ⚠️ Manual | ❌ No |
| **Run Migrations** | ✅ Yes | ✅ Yes | ❌ No |
| **Clear Caches** | ✅ Yes | ✅ Yes | ❌ No |
| **Maintenance Mode** | ✅ Yes | ✅ Yes | ❌ No |
| **Setup Complexity** | Medium | Low | Very Low |
| **Flexibility** | High | Medium | Low |
| **Free Tier** | ✅ Yes | ✅ Yes | Plan dependent |
| **Recommended For** | Production | Small projects | Simple sites |

---

## Best Practices

### 1. Use Staging Environment

Create a staging branch for testing:

```yaml
on:
  push:
    branches:
      - main      # Production
      - staging   # Staging
```

### 2. Add Deployment Notifications

**Slack notification example:**

```yaml
- name: Notify Slack
  if: always()
  uses: 8398a7/action-slack@v3
  with:
    status: ${{ job.status }}
    text: 'Deployment ${{ job.status }}'
    webhook_url: ${{ secrets.SLACK_WEBHOOK }}
```

**Email notification:**

```yaml
- name: Send email notification
  if: failure()
  uses: dawidd6/action-send-mail@v3
  with:
    server_address: smtp.hostinger.com
    server_port: 587
    username: ${{ secrets.MAIL_USERNAME }}
    password: ${{ secrets.MAIL_PASSWORD }}
    subject: 'Deployment Failed'
    to: admin@temanbicara.space
    from: noreply@temanbicara.space
    body: 'Deployment to production failed. Check logs.'
```

### 3. Implement Rollback Mechanism

Add rollback job:

```yaml
- name: Rollback on failure
  if: failure()
  uses: appleboy/ssh-action@v1.0.0
  with:
    host: ${{ secrets.SSH_HOST }}
    port: ${{ secrets.SSH_PORT }}
    username: ${{ secrets.SSH_USER }}
    key: ${{ secrets.SSH_PRIVATE_KEY }}
    script: |
      cd ${{ secrets.DEPLOY_PATH }}
      git reset --hard HEAD~1
      composer install --no-dev
      php artisan migrate:rollback
      php artisan optimize
      php artisan up
```

### 4. Backup Before Deploy

```bash
# Add to deploy script
BACKUP_DIR="/home/u123456789/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > "$BACKUP_DIR/db_$DATE.sql"

# Backup files
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" -C $APP_PATH .
```

### 5. Health Check After Deploy

```bash
# Check if site is responding
HEALTH_CHECK=$(curl -s -o /dev/null -w "%{http_code}" https://temanbicara.space)

if [ "$HEALTH_CHECK" != "200" ]; then
    echo "❌ Health check failed! Rolling back..."
    git reset --hard HEAD~1
    php artisan optimize
    php artisan up
    exit 1
fi
```

---

## Troubleshooting

### Issue: SSH Connection Failed

```bash
# Test SSH connection
ssh -i ~/.ssh/hostinger_deploy u123456789@ssh.hostinger.com -p 65002 -v

# Check key permissions
chmod 600 ~/.ssh/hostinger_deploy
```

### Issue: Composer Install Fails

```bash
# Check Composer version
composer --version

# Clear Composer cache
composer clear-cache

# Install with verbose output
composer install --no-dev --optimize-autoloader --verbose
```

### Issue: Permission Denied

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R u123456789:u123456789 storage bootstrap/cache
```

### Issue: Deployment Stuck in Maintenance Mode

```bash
# Manually disable maintenance mode
cd /home/u123456789/teman-bicara
php artisan up
```

---

## Security Considerations

### 1. Protect Deploy Endpoint

```php
// In deploy-webhook.php
// Add IP whitelist (GitHub webhook IPs)
$allowedIPs = [
    '140.82.112.0/20',
    '143.55.64.0/20',
    '185.199.108.0/22',
    '192.30.252.0/22',
];

$clientIP = $_SERVER['REMOTE_ADDR'];
$allowed = false;

foreach ($allowedIPs as $range) {
    if (ipInRange($clientIP, $range)) {
        $allowed = true;
        break;
    }
}

if (!$allowed) {
    http_response_code(403);
    die('Forbidden');
}

function ipInRange($ip, $range) {
    list($subnet, $mask) = explode('/', $range);
    return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) == ip2long($subnet);
}
```

### 2. Rate Limiting

```php
// Add rate limiting to prevent abuse
$lockFile = '/tmp/deploy.lock';

if (file_exists($lockFile)) {
    $lockTime = filemtime($lockFile);
    if (time() - $lockTime < 60) { // 1 minute cooldown
        http_response_code(429);
        die('Too many requests. Try again later.');
    }
}

touch($lockFile);
```

### 3. Log All Deployments

```php
// Log deployment attempts
$logEntry = sprintf(
    "[%s] Deployment triggered by: %s\n",
    date('Y-m-d H:i:s'),
    $data['pusher']['name'] ?? 'Unknown'
);

file_put_contents('/home/u123456789/deploy-webhook.log', $logEntry, FILE_APPEND);
```

---

## Monitoring & Alerts

### 1. Setup Deployment Dashboard

Use GitHub Actions status badge:

```markdown
![Deployment Status](https://github.com/ervandyr2512/product-management/actions/workflows/deploy.yml/badge.svg)
```

### 2. Monitor Deployment Times

Track deployment duration:

```yaml
- name: Record deployment time
  run: |
    START_TIME=${{ steps.deploy.outputs.start-time }}
    END_TIME=$(date +%s)
    DURATION=$((END_TIME - START_TIME))
    echo "Deployment took $DURATION seconds"
```

---

## Next Steps

1. Choose deployment method (GitHub Actions recommended)
2. Set up SSH keys
3. Configure GitHub secrets
4. Test deployment
5. Monitor first few deployments
6. Set up notifications
7. Document any custom steps

---

*Happy Auto-Deploying! 🚀*
