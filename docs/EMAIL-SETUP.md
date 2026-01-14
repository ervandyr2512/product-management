# Email Configuration for Hostinger

## 📧 Step-by-Step Email Setup

### 1. Create Email Accounts in Hostinger

1. Login to **Hostinger hPanel**: https://hpanel.hostinger.com
2. Go to **Email** → **Email Accounts**
3. Select domain: **temanbicara.space**
4. Click **Create Email Account**

**Recommended email accounts:**
- `noreply@temanbicara.space` - For system notifications
- `info@temanbicara.space` - For contact form
- `support@temanbicara.space` - For customer support

### 2. Configure SMTP in `.env` File

SSH to your server:
```bash
ssh -p 65002 u162866096@82.29.191.90
cd teman-bicara
nano .env
```

Update the MAIL section:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@temanbicara.space
MAIL_PASSWORD=your_email_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@temanbicara.space"
MAIL_FROM_NAME="Teman Bicara"
```

**Important:**
- Replace `your_email_password_here` with your actual email password
- Use the full email address as username
- Make sure there are no spaces around the `=` sign

### 3. Clear Configuration Cache

After editing `.env`:
```bash
php artisan config:clear
php artisan config:cache
```

### 4. Test Email Configuration

#### Option A: Using Web Route

Add this to your `routes/web.php`:
```php
Route::get('/test-email', function () {
    try {
        Mail::raw('Test email from Teman Bicara', function ($message) {
            $message->to('your-email@gmail.com')
                    ->subject('Test Email - Teman Bicara');
        });

        return 'Email sent successfully! Check your inbox.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
```

Then visit: `https://temanbicara.space/test-email`

#### Option B: Using Artisan Command

Create a test command:
```bash
php artisan make:command TestEmail
```

Edit `app/Console/Commands/TestEmail.php`:
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'email:test {recipient}';
    protected $description = 'Test email configuration';

    public function handle()
    {
        $recipient = $this->argument('recipient');

        try {
            Mail::raw('This is a test email from Teman Bicara', function ($message) use ($recipient) {
                $message->to($recipient)
                        ->subject('Test Email - Teman Bicara');
            });

            $this->info('✅ Email sent successfully to: ' . $recipient);
            $this->info('📬 Check your inbox (and spam folder)');
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
```

Run the test:
```bash
php artisan email:test your-email@gmail.com
```

### 5. Hostinger SMTP Settings Reference

**Primary SMTP Settings:**
- **Host:** `smtp.hostinger.com`
- **Port:** `587` (TLS recommended)
- **Encryption:** `tls`

**Alternative Settings (if 587 doesn't work):**
- **Port:** `465` (SSL)
- **Encryption:** `ssl`

**IMAP Settings (for reading emails):**
- **Host:** `imap.hostinger.com`
- **Port:** `993`
- **Encryption:** `ssl`

**POP3 Settings:**
- **Host:** `pop.hostinger.com`
- **Port:** `995`
- **Encryption:** `ssl`

### 6. Common Issues & Solutions

#### Issue 1: "Connection could not be established"
**Solution:**
- Check if port 587 is open
- Try alternative port 465 with SSL encryption
- Verify email account is active in hPanel

```env
# Try this configuration
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

#### Issue 2: "Authentication failed"
**Solution:**
- Double-check email password (copy-paste to avoid typos)
- Ensure using full email address as username
- Verify email account is not suspended

#### Issue 3: "Stream context error"
**Solution:**
```env
# Add this to .env
MAIL_VERIFY_PEER=false
```

#### Issue 4: Emails going to spam
**Solution:**
1. Add SPF record to DNS
2. Add DKIM record to DNS
3. Set up DMARC policy

Check in hPanel → Email → Email Authentication

### 7. Email Templates

Create email templates in `resources/views/emails/`:

**Example: Welcome Email**
```php
// resources/views/emails/welcome.blade.php
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Teman Bicara</h1>
        </div>
        <div class="content">
            <p>Hello {{ $name }},</p>
            <p>Thank you for joining Teman Bicara!</p>
        </div>
    </div>
</body>
</html>
```

**Sending the template:**
```php
Mail::send('emails.welcome', ['name' => $user->name], function($message) use ($user) {
    $message->to($user->email)
            ->subject('Welcome to Teman Bicara');
});
```

### 8. Production Best Practices

✅ **DO:**
- Use queue for sending emails (Laravel Queues)
- Set up email logging for debugging
- Use rate limiting to prevent spam
- Validate email addresses before sending
- Handle bounced emails properly

❌ **DON'T:**
- Don't send emails synchronously in production
- Don't expose email credentials in code
- Don't send large attachments without compression
- Don't send emails without user consent

### 9. Queue Configuration (Recommended)

Edit `.env`:
```env
QUEUE_CONNECTION=database
```

Run migrations:
```bash
php artisan queue:table
php artisan migrate
```

Send email via queue:
```php
Mail::to($user)->queue(new WelcomeEmail($user));
```

Start queue worker:
```bash
php artisan queue:work
```

**For Hostinger, add to cron jobs:**
```bash
*/5 * * * * cd /home/u162866096/teman-bicara && php artisan queue:work --stop-when-empty
```

### 10. Monitoring & Logs

Check email sending logs:
```bash
tail -f storage/logs/laravel.log | grep mail
```

Check email configuration:
```bash
php artisan config:show mail
```

### 11. Testing Checklist

- [ ] Email credentials configured in `.env`
- [ ] Configuration cache cleared
- [ ] Test email sent successfully
- [ ] Email received (check spam folder)
- [ ] Reply-to address works
- [ ] Email templates render correctly
- [ ] Attachments work (if needed)
- [ ] Queue workers running (if using queues)

---

## Quick Setup Commands

```bash
# 1. Edit environment
nano .env

# 2. Clear cache
php artisan config:clear
php artisan config:cache

# 3. Test email (create test route first)
curl https://temanbicara.space/test-email

# 4. Check logs
tail -f storage/logs/laravel.log
```

---

## Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify email credentials in hPanel
3. Test SMTP connection with telnet: `telnet smtp.hostinger.com 587`
4. Contact Hostinger support if SMTP is blocked

---

*Last Updated: January 14, 2026*
