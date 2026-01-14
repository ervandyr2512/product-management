# Teman Bicara - Documentation

## Overview
Teman Bicara is a comprehensive mental health consultation platform built with Laravel 12. This documentation covers recent development updates, implementations, and technical guides.

## Documentation Index

### 1. [Changelog](./CHANGELOG.md)
Complete list of recent updates and improvements made to the application.

**Contents:**
- Navigation & UI improvements
- Chat system enhancements
- Multilingual support updates
- Contact information updates
- Dark mode implementation
- Translation keys reference

### 2. [Navigation Improvements](./NAVIGATION-IMPROVEMENTS.md)
Detailed documentation on navigation bar consistency and icon additions.

**Contents:**
- Problem statement and solutions
- Desktop and mobile navigation structure
- Icon implementation with badges
- Laravel Breeze pattern adoption
- Dark mode support
- Testing guide
- Visual diagrams

### 3. [Chat System](./CHAT-SYSTEM.md)
Complete guide to the chat functionality and recent improvements.

**Contents:**
- Problem and solution overview
- Core logic implementation
- Database queries
- Access control system
- User interface components
- Performance considerations
- Security measures
- Future enhancements

### 4. [Multilingual Support](./MULTILINGUAL-SUPPORT.md)
Comprehensive guide to translation implementation and best practices.

**Contents:**
- Supported languages (Indonesian, English)
- Translation helper functions
- Implementation guide
- Special cases (line breaks, HTML)
- Best practices
- Testing procedures
- Adding new translations

### 5. [Contact Updates](./CONTACT-UPDATES.md)
Documentation for contact information updates and formatting improvements.

**Contents:**
- Email domain migration (.com → .id)
- Address formatting solution
- Translation keys
- Dark mode support
- Best practices
- Troubleshooting guide

---

## Quick Start Guide

### For Developers

#### Setting Up Development Environment
```bash
# Clone repository
git clone <repository-url>
cd teman-bicara

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Start development server
php artisan serve
npm run dev
```

#### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### For Content Managers

#### Updating Translations
1. Navigate to `lang/en/messages.php` or `lang/id/messages.php`
2. Find the translation key
3. Update the value
4. Clear cache: `php artisan config:clear`

#### Updating Contact Information
See [Contact Updates Documentation](./CONTACT-UPDATES.md#migration-guide)

---

## Project Structure

```
teman-bicara/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ChatController.php
│   │   │   ├── ContactController.php
│   │   │   └── ...
│   │   └── Middleware/
│   │       └── SetLocale.php
│   └── Models/
│       ├── Article.php
│       ├── User.php
│       └── ...
├── resources/
│   ├── views/
│   │   ├── articles/
│   │   │   └── index.blade.php
│   │   ├── chat/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   ├── pages/
│   │   │   └── contact.blade.php
│   │   ├── layouts/
│   │   │   └── navigation.blade.php
│   │   └── welcome.blade.php
│   └── lang/
│       ├── en/
│       │   └── messages.php
│       └── id/
│           └── messages.php
├── docs/                          # 👈 Documentation files
│   ├── README.md
│   ├── CHANGELOG.md
│   ├── NAVIGATION-IMPROVEMENTS.md
│   ├── CHAT-SYSTEM.md
│   ├── MULTILINGUAL-SUPPORT.md
│   └── CONTACT-UPDATES.md
└── ...
```

---

## Key Features

### 1. Mental Health Consultation Platform
- Browse verified professionals (Psychiatrists, Psychologists, Conversationalists)
- Book appointments with flexible scheduling
- Secure payment processing
- Video consultation support

### 2. Chat System
- Direct messaging with booked professionals
- Real-time unread message indicators
- Secure, payment-verified access
- Mobile-responsive interface

### 3. Multilingual Support
- Indonesian (default) and English
- Dynamic language switching
- Comprehensive translation coverage
- SEO-optimized content

### 4. Dark Mode
- System preference detection
- Manual toggle option
- Smooth transitions
- Complete UI coverage

### 5. Responsive Design
- Mobile-first approach
- Tablet optimization
- Desktop-enhanced features
- Touch-friendly interfaces

---

## Technology Stack

### Backend
- **Framework:** Laravel 12
- **Database:** MySQL
- **Authentication:** Laravel Breeze
- **Payment:** Midtrans (Indonesian payment gateway)

### Frontend
- **CSS Framework:** Tailwind CSS 3
- **JavaScript:** Alpine.js
- **Icons:** Heroicons
- **Build Tool:** Vite

### Additional Tools
- **Version Control:** Git
- **Package Manager:** Composer, NPM
- **Testing:** PHPUnit, Pest
- **Code Quality:** PHP CS Fixer, Laravel Pint

---

## Recent Updates Summary

### January 2026

#### Navigation Enhancements ✅
- Added Favorites and Messages icons to home page
- Implemented badge counters for all icons
- Unified navigation design across all pages
- Added complete dark mode support

#### Chat System Improvements ✅
- Show all booked professionals in chat list
- Display "No messages yet" for new conversations
- Fixed collection merge error
- Improved sorting logic

#### Multilingual Implementation ✅
- Complete translation of Articles page
- Dynamic category labels in Article model
- Added 10+ new translation keys
- Full dark mode support

#### Contact Updates ✅
- Migrated email domain to .id
- Fixed address line break formatting
- Updated footer contact information
- Improved accessibility

---

## Common Tasks

### Adding a New Translation Key

1. **Add to English file** (`lang/en/messages.php`):
```php
'your_new_key' => 'Your English Text',
```

2. **Add to Indonesian file** (`lang/id/messages.php`):
```php
'your_new_key' => 'Teks Bahasa Indonesia Anda',
```

3. **Use in view**:
```php
{{ __('messages.your_new_key') }}
```

4. **Clear cache**:
```bash
php artisan config:clear
```

### Creating a New Navigation Link

1. **Add to navigation.blade.php**:
```php
<x-nav-link :href="route('your.route')" :active="request()->routeIs('your.route')">
    {{ __('messages.your_link') }}
</x-nav-link>
```

2. **Add translation**:
```php
'your_link' => 'Your Link Text',
```

### Implementing Dark Mode

1. **Add dark mode classes**:
```html
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
    <!-- Content -->
</div>
```

2. **Add transition**:
```html
<div class="transition-colors duration-200">
    <!-- Smooth color transition -->
</div>
```

---

## Testing Guidelines

### Manual Testing Checklist
- [ ] Test all features in light mode
- [ ] Test all features in dark mode
- [ ] Verify translations (Indonesian & English)
- [ ] Check responsive design (mobile, tablet, desktop)
- [ ] Test navigation on all pages
- [ ] Verify badge counters update correctly
- [ ] Check chat functionality
- [ ] Test contact form submission

### Automated Testing
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=ChatControllerTest

# Generate coverage report
php artisan test --coverage-html coverage
```

---

## Troubleshooting

### Common Issues

#### Translations Not Showing
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### Dark Mode Not Working
1. Check if JavaScript is enabled
2. Verify Alpine.js is loaded
3. Clear browser cache
4. Check system preferences

#### Badge Counters Not Updating
1. Check database connections
2. Verify Eloquent relationships
3. Clear application cache
4. Refresh the page

---

## Contributing

### Before Submitting Changes
1. Run tests: `php artisan test`
2. Check code style: `./vendor/bin/pint`
3. Update documentation if needed
4. Test in both light and dark mode
5. Test in both languages (ID & EN)

### Code Style
- Follow PSR-12 coding standards
- Use Laravel best practices
- Write descriptive commit messages
- Add comments for complex logic

---

## Support & Contact

### For Technical Issues
- Check documentation first
- Review [Troubleshooting](#troubleshooting) section
- Search existing issues
- Create detailed bug report

### For Feature Requests
- Check roadmap in [CHANGELOG.md](./CHANGELOG.md)
- Review existing requests
- Provide clear use case
- Include mockups if possible

---

## License
This project is proprietary software. All rights reserved.

---

## Version History

### v1.0.0 (January 2026)
- Initial documentation release
- Complete navigation overhaul
- Chat system improvements
- Full multilingual support
- Contact information updates

---

## Roadmap

### Short Term (Q1 2026)
- [ ] Real-time chat with WebSockets
- [ ] Push notifications
- [ ] Advanced search filters
- [ ] User review system enhancements

### Medium Term (Q2-Q3 2026)
- [ ] Mobile app (iOS & Android)
- [ ] Video consultation feature
- [ ] AI chatbot assistant
- [ ] Analytics dashboard

### Long Term (Q4 2026+)
- [ ] Telemedicine integration
- [ ] Prescription management
- [ ] Insurance integration
- [ ] Multi-language support expansion

---

## Acknowledgments
- Laravel Team for the amazing framework
- Tailwind CSS for the utility-first approach
- Alpine.js for reactive components
- The open-source community

---

*Last Updated: January 14, 2026*
