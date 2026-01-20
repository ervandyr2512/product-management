# Admin User Management - Promote/Demote Feature

## Overview

Super Admin sekarang dapat:
- ✅ Promote user biasa menjadi professional
- ✅ Setup lengkap professional profile saat promote
- ✅ Demote professional kembali ke user biasa
- ✅ Manage semua user dan professional dari satu dashboard

---

## Fitur Promote User ke Professional

### Cara Menggunakan

1. **Login sebagai Super Admin**
   - Email: `admin@temanbicara.id`
   - Password: (yang sudah Anda set)

2. **Buka User Management**
   - Go to: `/admin/users`
   - Atau klik "User Management" di admin sidebar

3. **Pilih User yang Akan Dipromote**
   - Cari user dengan role "User"
   - Klik tombol **"⬆️ Promote"**

4. **Isi Form Professional Profile**
   - **Professional Type**: Pilih tipe (Psychiatrist/Psychologist/Conversationalist)
   - **Specialization**: Keahlian khusus (e.g., "Anxiety Disorders, Depression")
   - **License Number**: Nomor lisensi (optional)
   - **Years of Experience**: Lama pengalaman (tahun)
   - **Bio**: Biografi professional
   - **Rate 30 Minutes**: Harga untuk sesi 30 menit (IDR)
   - **Rate 60 Minutes**: Harga untuk sesi 60 menit (IDR)

5. **Submit**
   - Klik **"✅ Promote to Professional"**
   - User akan otomatis:
     - Role berubah dari `user` → `professional`
     - Professional profile ter-create
     - Status active
     - Muncul di halaman /professionals

---

## Fitur Demote Professional ke User

### Cara Menggunakan

1. **Buka User Management**
   - Go to: `/admin/users`

2. **Pilih Professional yang Akan Didemote**
   - Cari user dengan role "Professional"
   - Klik tombol **"⬇️ Demote"**

3. **Konfirmasi**
   - Sistem akan check appointment aktif
   - Jika ada appointment aktif, demote akan ditolak
   - Jika aman, professional profile akan dihapus
   - User kembali menjadi role `user`

### ⚠️ Catatan Penting

- **Tidak bisa demote** jika professional masih punya appointment aktif
- **Professional profile akan dihapus** permanen
- **Reviews dan ratings** tetap tersimpan (jika sudah ada)
- **Past appointments** tetap tercatat di database

---

## Professional Types

### 1. Psychiatrist (Psikiater)
- Medical doctor specializing in mental health
- Can prescribe medication
- Typically higher price point

### 2. Psychologist (Psikolog)
- Licensed psychologist
- Focus on therapy and counseling
- Cannot prescribe medication

### 3. Conversationalist (Teman Bicara)
- Supportive listener
- Peer counselor
- Lower price point
- More accessible

---

## Pricing Guidelines

### Rate 30 Minutes

| Type | Recommended Price Range |
|------|------------------------|
| Psychiatrist | Rp 200,000 - Rp 400,000 |
| Psychologist | Rp 100,000 - Rp 250,000 |
| Conversationalist | Rp 50,000 - Rp 100,000 |

**Default:** Rp 100,000

### Rate 60 Minutes

| Type | Recommended Price Range |
|------|------------------------|
| Psychiatrist | Rp 300,000 - Rp 800,000 |
| Psychologist | Rp 200,000 - Rp 500,000 |
| Conversationalist | Rp 75,000 - Rp 200,000 |

**Default:** Rp 150,000

---

## Form Fields Reference

### Required Fields (*)

| Field | Type | Description | Example |
|-------|------|-------------|---------|
| Professional Type* | Select | Type of professional | Psychologist |
| Specialization* | Text | Area of expertise | Anxiety, Depression, Trauma |
| Experience Years* | Number | Years of practice | 5 |
| Bio* | Textarea | Professional biography | "I am a licensed psychologist..." |
| Price per Session* | Number | Session price in IDR | 250000 |
| Session Duration* | Select | Minutes per session | 60 |

### Optional Fields

| Field | Description | Example |
|-------|-------------|---------|
| License Number | Professional license/STR | PSI-123456 |
| Education | Educational background | S1 & S2 Psychology - UI |
| Languages | Languages spoken | Indonesian, English, Mandarin |

---

## Business Logic

### Promote Process

```php
1. Validate user is not already professional
2. Validate user is not admin
3. Validate form data
4. Update user role to 'professional'
5. Create professional profile with form data
6. Set is_active = true
7. Redirect with success message
```

### Demote Process

```php
1. Validate user is professional
2. Check for active appointments
3. If has active appointments → reject
4. Delete professional profile
5. Update user role to 'user'
6. Redirect with success message
```

---

## Security & Permissions

### Who Can Access?

✅ **Super Admin** only
- Role must be `admin`
- Protected by `auth` and `admin` middleware

❌ **Cannot promote/demote:**
- Admin users (protection against accidental demotion)
- Users already in target role

---

## Database Changes

### Users Table
```php
// Before promote
role: 'user'

// After promote
role: 'professional'
```

### Professionals Table
```php
// New record created with:
user_id, type, specialization, license_number,
bio, price_per_session, session_duration,
experience_years, education, languages, is_active
```

---

## UI/UX Features

### User List Table

**For Users (role: user):**
- Show **⬆️ Promote** button

**For Professionals (role: professional):**
- Show **⬇️ Demote** button

**For Admins:**
- No promote/demote buttons (protected)

### Promote Form

- Clean, organized form layout
- Helpful placeholder text
- Field descriptions
- Real-time validation
- Required field indicators (*)
- Default values for common fields

---

## Error Handling

### Common Errors

**1. User already professional**
```
Error: User sudah menjadi professional.
Solution: Check user role before promoting
```

**2. Cannot demote admin**
```
Error: Tidak dapat mempromote admin.
Solution: Don't attempt to promote admin users
```

**3. Professional has active appointments**
```
Error: Tidak dapat demote professional yang masih memiliki appointment aktif.
Solution: Wait until all appointments are completed/cancelled
```

**4. Validation errors**
```
Error: Bio field is required.
Solution: Fill all required fields
```

---

## Testing Checklist

### Promote Feature
- [ ] Can access promote form for regular user
- [ ] Form validation works (required fields)
- [ ] Successfully creates professional profile
- [ ] User role changes from 'user' to 'professional'
- [ ] Professional appears in /professionals page
- [ ] Cannot promote admin user
- [ ] Cannot promote already-professional user

### Demote Feature
- [ ] Can demote professional without active appointments
- [ ] Cannot demote professional with active appointments
- [ ] Professional profile gets deleted
- [ ] User role changes from 'professional' to 'user'
- [ ] Professional disappears from /professionals page
- [ ] Cannot demote regular user (already not professional)

---

## Routes

```php
// Promote routes
GET  /admin/users/{user}/promote  → Show promote form
POST /admin/users/{user}/promote  → Process promotion

// Demote route
POST /admin/users/{user}/demote   → Process demotion
```

---

## API Reference

### Promote Method

```php
/**
 * Promote user to professional with profile setup
 *
 * @param Request $request
 * @param User $user
 * @return RedirectResponse
 */
public function promote(Request $request, User $user)
```

### Demote Method

```php
/**
 * Demote professional back to regular user
 *
 * @param User $user
 * @return RedirectResponse
 */
public function demote(User $user)
```

---

## Future Enhancements

### Possible Improvements

- [ ] Bulk promote multiple users
- [ ] Email notification to user when promoted
- [ ] Approval workflow (request → review → approve)
- [ ] Professional verification process
- [ ] Upload license documents
- [ ] Profile photo upload during promote
- [ ] Scheduled promotion (effective date)
- [ ] Promote history/audit log

---

## Screenshots

### User List with Promote/Demote Buttons

```
| Name          | Role         | Actions              |
|---------------|--------------|----------------------|
| John Doe      | User         | View Edit ⬆️ Promote |
| Jane Smith    | Professional | View Edit ⬇️ Demote  |
| Admin User    | Admin        | View Edit            |
```

### Promote Form

```
┌─────────────────────────────────────────┐
│ Promote User to Professional            │
│ Setup professional profile for: John Doe│
├─────────────────────────────────────────┤
│ Professional Information                 │
│ ┌─────────────────────────────────────┐ │
│ │ Professional Type: [Psychologist ▼] │ │
│ │ Specialization: [Anxiety, Depr...] │ │
│ │ License Number: [PSI-123456]       │ │
│ │ Experience: [5 years]              │ │
│ │ Bio: [I am a licensed...]          │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ Pricing & Session Details               │
│ ┌─────────────────────────────────────┐ │
│ │ Price: [250000]  Duration: [60▼]   │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ [Cancel] [✅ Promote to Professional]  │
└─────────────────────────────────────────┘
```

---

## Support

Jika ada pertanyaan atau issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify user role in database
3. Check professional profile table
4. Review validation errors

---

*Last Updated: January 16, 2026*
*Feature: Admin User Management v1.0*
