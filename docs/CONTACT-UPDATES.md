# Contact Information Updates Documentation

## Overview
This document covers all updates made to contact information across the Teman Bicara application, including email domain changes and address formatting improvements.

## Updates Summary

### 1. Email Domain Migration
**Change:** `@temanbicara.com` → `@temanbicara.id`

**Affected Locations:**
- Contact page
- Home page footer

### 2. Address Formatting
**Issue:** HTML `<br>` tags displaying as literal text
**Solution:** Proper line break implementation

---

## Email Domain Updates

### Contact Page Emails

#### File Location
`resources/views/pages/contact.blade.php` (lines 102-103)

#### Changes Made
```php
// Before
<p class="text-gray-600">info@temanbicara.com</p>
<p class="text-gray-600">support@temanbicara.com</p>

// After
<p class="text-gray-600 dark:text-gray-400">info@temanbicara.id</p>
<p class="text-gray-600 dark:text-gray-400">support@temanbicara.id</p>
```

#### Visual Display
```
┌─────────────────────────────────────┐
│ Email                               │
│ info@temanbicara.id                 │
│ support@temanbicara.id              │
└─────────────────────────────────────┘
```

### Home Page Footer Email

#### File Location
`lang/en/messages.php` (line 282)
`lang/id/messages.php` (line 282)

#### Changes Made

**English Translation:**
```php
// Before
'footer_email' => 'Email: info@temanbicara.com',

// After
'footer_email' => 'Email: info@temanbicara.id',
```

**Indonesian Translation:**
```php
// Before
'footer_email' => 'Email: info@temanbicara.com',

// After
'footer_email' => 'Email: info@temanbicara.id',
```

#### Usage in View
```php
<p>{{ __('messages.footer_email') }}</p>
```

---

## Address Formatting Implementation

### The Problem

#### Initial Issue
```html
<!-- Translation file -->
'contact_address_value' => 'Jl. Sudirman No. 123<br>Jakarta Pusat 10110<br>Indonesia'

<!-- Blade template -->
{{ __('messages.contact_address_value') }}

<!-- Browser output -->
Jl. Sudirman No. 123<br>Jakarta Pusat 10110<br>Indonesia
```

The `<br>` tags were being escaped by Blade's `{{ }}` syntax, resulting in literal `<br>` text instead of line breaks.

### The Solution

#### Step 1: Update Translation Files

**English (lang/en/messages.php):**
```php
'contact_address_value' => 'Jl. Sudirman No. 123
Jakarta Pusat 10110
Indonesia',
```

**Indonesian (lang/id/messages.php):**
```php
'contact_address_value' => 'Jl. Sudirman No. 123
Jakarta Pusat 10110
Indonesia',
```

**Key Point:** Use actual newline characters instead of `<br>` tags.

#### Step 2: Update Blade Template

**File Location:** `resources/views/pages/contact.blade.php` (line 129)

```php
<!-- Before -->
<p class="text-gray-600">{{ __('messages.contact_address_value') }}</p>

<!-- After -->
<p class="text-gray-600 dark:text-gray-400">{!! nl2br(e(__('messages.contact_address_value'))) !!}</p>
```

### Understanding the Solution

#### Function Breakdown

1. **`__('messages.contact_address_value')`**
   - Gets translation with newline characters

2. **`e()`**
   - Escapes HTML to prevent XSS attacks
   - Converts `<` to `&lt;`, `>` to `&gt;`, etc.
   - SECURITY: Always escape user input!

3. **`nl2br()`**
   - Converts newline characters to `<br>` HTML tags
   - `\n` → `<br>`
   - `\r\n` → `<br>`

4. **`{!! !!}`**
   - Outputs unescaped HTML
   - Allows `<br>` tags to render as HTML
   - Safe because content was already escaped by `e()`

#### Security Flow
```
Translation → Escape HTML → Convert newlines to <br> → Output HTML
   (safe)   →    (safe)    →        (safe)         →   (safe)
```

### Visual Result

#### Before Fix
```
Jl. Sudirman No. 123<br>Jakarta Pusat 10110<br>Indonesia
```

#### After Fix
```
Jl. Sudirman No. 123
Jakarta Pusat 10110
Indonesia
```

---

## Complete Contact Section

### HTML Structure
```html
<div class="flex items-start">
    <!-- Icon -->
    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-4">
        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400">
            <!-- Location icon -->
        </svg>
    </div>

    <!-- Content -->
    <div>
        <h3 class="font-semibold mb-1 text-gray-900 dark:text-gray-100">
            {{ __('messages.contact_address') }}
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            {!! nl2br(e(__('messages.contact_address_value'))) !!}
        </p>
    </div>
</div>
```

### Full Contact Information Display

```
┌────────────────────────────────────────────┐
│ 📧 Email                                   │
│    info@temanbicara.id                     │
│    support@temanbicara.id                  │
├────────────────────────────────────────────┤
│ 📞 Phone                                   │
│    +62 21 1234 5678                        │
│    +62 812 3456 7890                       │
├────────────────────────────────────────────┤
│ 📍 Address                                 │
│    Jl. Sudirman No. 123                    │
│    Jakarta Pusat 10110                     │
│    Indonesia                               │
├────────────────────────────────────────────┤
│ 🕐 Operating Hours                         │
│    Monday - Friday: 08:00 - 20:00          │
│    Saturday - Sunday: 09:00 - 17:00        │
└────────────────────────────────────────────┘
```

---

## Dark Mode Support

### Color Scheme
All contact information elements support dark mode:

```php
// Light mode
text-gray-900    // Headings
text-gray-600    // Content

// Dark mode
dark:text-gray-100    // Headings
dark:text-gray-400    // Content
```

### Icons
```php
// Icon background
bg-purple-100 dark:bg-purple-900

// Icon color
text-purple-600 dark:text-purple-400
```

---

## Translation Keys Reference

### Contact Page Keys
```php
// Section titles
'contact_page_title' => 'Contact Us' / 'Hubungi Kami',
'contact_form_title' => 'Send us a Message' / 'Kirim Pesan',
'contact_info_title' => 'Contact Information' / 'Informasi Kontak',

// Contact details
'contact_email_title' => 'Email' / 'Email',
'contact_phone_title' => 'Phone' / 'Telepon',
'contact_address' => 'Address' / 'Alamat',
'contact_hours_title' => 'Operating Hours' / 'Jam Operasional',

// Address value (multiline)
'contact_address_value' => 'Jl. Sudirman No. 123
Jakarta Pusat 10110
Indonesia',

// Operating hours
'contact_hours_weekday' => 'Monday - Friday: 08:00 - 20:00' / 'Senin - Jumat: 08:00 - 20:00',
'contact_hours_weekend' => 'Saturday - Sunday: 09:00 - 17:00' / 'Sabtu - Minggu: 09:00 - 17:00',
```

### Footer Keys
```php
'footer_email' => 'Email: info@temanbicara.id',
'footer_phone' => 'Phone: +62 21 1234 5678' / 'Telp: +62 21 1234 5678',
'footer_contact_us' => 'Contact Us' / 'Hubungi Kami',
```

---

## Best Practices

### 1. Multiline Text in Translations

#### ✅ Good Practice
```php
// Use actual newlines
'text' => 'Line 1
Line 2
Line 3',

// In view
{!! nl2br(e(__('messages.text'))) !!}
```

#### ❌ Bad Practice
```php
// Don't use HTML tags in translation
'text' => 'Line 1<br>Line 2<br>Line 3',

// Don't output unescaped
{!! __('messages.text') !!}  // Security risk!
```

### 2. Email Addresses

#### ✅ Good Practice
```php
// Use mailto links
<a href="mailto:info@temanbicara.id" class="text-purple-600 hover:underline">
    info@temanbicara.id
</a>
```

#### ✅ Also Good
```php
// Plain text with proper styling
<p class="text-gray-600 dark:text-gray-400">info@temanbicara.id</p>
```

### 3. Phone Numbers

#### ✅ Good Practice
```php
// Use tel: protocol
<a href="tel:+622112345678" class="text-purple-600 hover:underline">
    +62 21 1234 5678
</a>
```

### 4. Addresses

#### ✅ Good Practice
```php
// Use schema.org markup for SEO
<div itemscope itemtype="http://schema.org/PostalAddress">
    <p itemprop="streetAddress">Jl. Sudirman No. 123</p>
    <p itemprop="addressLocality">Jakarta Pusat</p>
    <p itemprop="postalCode">10110</p>
    <p itemprop="addressCountry">Indonesia</p>
</div>
```

---

## Testing Checklist

### Visual Testing
- [ ] Email addresses display correctly on contact page
- [ ] Footer email displays correctly on home page
- [ ] Address shows proper line breaks (not `<br>` text)
- [ ] All text readable in light mode
- [ ] All text readable in dark mode
- [ ] Icons aligned with text properly

### Functional Testing
- [ ] Clicking email opens mail client
- [ ] Clicking phone number initiates call (mobile)
- [ ] Clicking address opens maps (if linked)
- [ ] Translation switches work (EN ↔ ID)

### Responsive Testing
- [ ] Contact info displays correctly on mobile
- [ ] Icons scale appropriately
- [ ] Text wraps properly on small screens
- [ ] Line breaks work on all screen sizes

### Security Testing
- [ ] HTML in translations properly escaped
- [ ] XSS attacks prevented
- [ ] No JavaScript injection possible
- [ ] Safe use of `{!! !!}` syntax

---

## Migration Guide

### If You Need to Update Contact Info

#### 1. Email Addresses
```php
// Update in view (if hardcoded)
resources/views/pages/contact.blade.php

// Or update in translation files
lang/en/messages.php
lang/id/messages.php
```

#### 2. Phone Numbers
```php
// Update in translation files
'contact_phone_value' => '+62 21 XXXX XXXX',
```

#### 3. Address
```php
// Update with newlines (not <br>)
'contact_address_value' => 'Street Address
City, Postal Code
Country',
```

#### 4. Operating Hours
```php
'contact_hours_weekday' => 'Monday - Friday: HH:MM - HH:MM',
'contact_hours_weekend' => 'Saturday - Sunday: HH:MM - HH:MM',
```

---

## Future Enhancements

### Planned Features
1. **Google Maps Integration**
   - Embed interactive map
   - Click address to open in maps app
   - Show office location pin

2. **Contact Form**
   - Already implemented
   - Future: Add file attachments
   - Future: Email notifications

3. **Live Chat**
   - WhatsApp integration
   - Live chat widget
   - Business hours automation

4. **Social Media Links**
   - Instagram
   - Facebook
   - LinkedIn
   - Twitter/X

5. **Multiple Offices**
   - Support multiple locations
   - Office selection dropdown
   - Location-based contact info

---

## Related Files

### Views
- `resources/views/pages/contact.blade.php` - Contact page
- `resources/views/welcome.blade.php` - Home page with footer

### Translation Files
- `lang/en/messages.php` - English translations
- `lang/id/messages.php` - Indonesian translations

### Controllers
- `app/Http/Controllers/ContactController.php` - Contact form handling

---

## Troubleshooting

### Issue: Line breaks not showing
**Solution:** Make sure you're using `{!! nl2br(e()) !!}` not `{{ }}`

### Issue: Email showing wrong domain
**Solution:** Check both translation files (en and id) and clear config cache

### Issue: Dark mode text not visible
**Solution:** Add `dark:text-gray-400` class

### Issue: XSS warning in security scan
**Solution:** Ensure you're using `e()` before `nl2br()`: `{!! nl2br(e($text)) !!}`

---

## Related Documentation
- [Multilingual Support](./MULTILINGUAL-SUPPORT.md)
- [Navigation Improvements](./NAVIGATION-IMPROVEMENTS.md)
- [Changelog](./CHANGELOG.md)
