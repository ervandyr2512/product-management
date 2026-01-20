# 🚀 Quick Deploy Guide - Teman Bicara

Panduan cepat untuk deploy/update aplikasi ke server Hostinger.

---

## ⚡ Cara Tercepat (Recommended)

### Metode 1: Auto Deploy via Browser (Setelah Setup)

```
https://temanbicara.id/deploy.php?key=TemanbIcara2025!Deploy
```

**Kelebihan:**
- ✅ Tidak perlu SSH
- ✅ Tidak perlu terminal
- ✅ Bisa dari browser mana saja
- ✅ Otomatis pull, install, dan clear cache
- ✅ Tampilan visual progress deployment

**Langkah:**
1. Buka URL di atas
2. Tunggu proses selesai
3. Refresh website untuk melihat perubahan

---

## 🔧 Setup Awal (Hanya Sekali)

Untuk bisa menggunakan auto-deploy, lakukan ini **satu kali** saja:

### Via cPanel Terminal

1. Login ke [hPanel Hostinger](https://hpanel.hostinger.com)
2. Klik website → **Advanced** → **Terminal**
3. Copy-paste command ini:

```bash
cd ~/teman-bicara && git pull origin main && php artisan view:clear && php artisan cache:clear && php artisan config:clear && php artisan route:clear
```

4. Setelah selesai, file `deploy.php` sudah ada
5. Selanjutnya bisa pakai auto-deploy via browser

---

## 📝 Metode Alternatif

### Via SSH (Manual)

```bash
# Connect to server
ssh u162866096@temanbicara.space
# Password: Vandyganteng93!

# Navigate to project
cd ~/teman-bicara

# Pull latest changes
git pull origin main

# Clear all caches
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Optimize for production (optional)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Via cPanel File Manager (Upload Manual)

1. Download files dari GitHub
2. Login cPanel → File Manager
3. Upload ke folder yang sesuai
4. Clear cache via terminal atau buat file PHP

---

## 🎯 After Deploy Checklist

Setelah deploy, selalu test:

- [ ] **Homepage**: https://temanbicara.id
- [ ] **Admin Panel**: https://temanbicara.id/admin/dashboard
- [ ] **Professionals**: https://temanbicara.id/professionals
- [ ] **Articles**: https://temanbicara.id/articles
- [ ] **Contact**: https://temanbicara.id/contact

---

## 🔐 Security Notes

### Auto Deploy Password

```
Key: TemanbIcara2025!Deploy
URL: https://temanbicara.id/deploy.php?key=TemanbIcara2025!Deploy
```

**PENTING:**
- Jangan share URL ini ke orang lain
- Hapus file `deploy.php` jika sudah tidak digunakan
- Atau ubah password di line 15 file `deploy.php`

### Admin Credentials

```
Email: admin@temanbicara.id
Password: PasswordBaruYangKuat123!
```

### SSH Credentials

```
Host: temanbicara.space
Username: u162866096
Password: Vandyganteng93!
```

---

## 🆘 Troubleshooting

### Error 404 pada deploy.php

**Solusi:** File belum ada di server, lakukan setup awal dulu.

### Error "Unable to locate component"

**Solusi:** Clear view cache:
```bash
php artisan view:clear
php artisan cache:clear
```

### Admin panel tidak bisa diakses

**Solusi:**
1. Pull latest changes
2. Clear all caches
3. Check file permissions

### Git pull failed

**Solusi:**
```bash
cd ~/teman-bicara
git stash
git pull origin main
```

---

## 📊 What Gets Updated

Setiap deploy akan update:

1. ✅ All code files (PHP, Blade, JavaScript)
2. ✅ Routes and configurations
3. ✅ Views and components
4. ✅ Public assets (if changed)
5. ✅ Dependencies (composer)

**NOT included:**
- ❌ Database data
- ❌ .env file (manual update required)
- ❌ Uploaded files in storage

---

## 🎓 Common Scenarios

### Scenario 1: Fix a Bug

```
1. Fix bug locally
2. Test locally
3. Commit & push to GitHub
4. Open deploy.php URL
5. Done!
```

### Scenario 2: Add New Feature

```
1. Develop feature locally
2. Test thoroughly
3. Commit & push
4. Deploy via browser
5. Test on production
```

### Scenario 3: Update Configuration

```
1. Update .env locally
2. SSH to server
3. Edit .env on server manually
4. Run: php artisan config:clear
5. Run: php artisan config:cache
```

---

## 📞 Need Help?

If deployment fails:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check server error logs in cPanel
3. Try manual SSH deployment
4. Clear all caches manually

---

## 🔄 Deployment History

Track your deployments:

- Check git log: `git log --oneline -10`
- Check deployment time in deploy.php output
- Monitor via GitHub Actions (if enabled)

---

**Last Updated:** January 20, 2026
**Version:** 1.0.0
