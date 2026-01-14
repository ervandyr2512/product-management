# Multilingual Support Documentation

## Overview
This document covers the implementation of complete multilingual support for the Teman Bicara application, with a focus on the recent improvements to the Articles page.

## Supported Languages
- **Indonesian (id)** - Default
- **English (en)**

## Language Files Location
```
lang/
├── en/
│   └── messages.php
└── id/
    └── messages.php
```

## Recent Improvements

### Articles Page Translation

#### Problem
The Articles page had hardcoded Indonesian text that didn't change when users switched to English:
- Page title: "Artikel Kesehatan Mental"
- Search placeholder: "Cari artikel..."
- Category options: "Semua Kategori", "Kesehatan Mental", etc.
- Button: "Cari"
- Link: "Baca Selengkapnya"
- Empty state message

#### Solution
Complete migration to Laravel's translation system using `__()` helper function.

## Implementation Guide

### 1. View Translation

#### Before (Hardcoded)
```php
<h2 class="font-semibold text-xl">
    Artikel Kesehatan Mental
</h2>

<input type="text" placeholder="Cari artikel...">

<option value="all">Semua Kategori</option>
<option value="mental_health">Kesehatan Mental</option>
```

#### After (Translated)
```php
<h2 class="font-semibold text-xl">
    {{ __('messages.mental_health_articles') }}
</h2>

<input type="text" placeholder="{{ __('messages.search_articles') }}">

<option value="all">{{ __('messages.all_categories') }}</option>
<option value="mental_health">{{ __('messages.category_mental_health') }}</option>
```

### 2. Model Translation

#### Article Model Category Labels
Located in `app/Models/Article.php` (lines 36-48)

**Before (Hardcoded):**
```php
public function getCategoryLabelAttribute()
{
    return match($this->category) {
        'mental_health' => 'Kesehatan Mental',
        'anxiety' => 'Kecemasan',
        'depression' => 'Depresi',
        // ...
    };
}
```

**After (Translated):**
```php
public function getCategoryLabelAttribute()
{
    return match($this->category) {
        'mental_health' => __('messages.category_mental_health'),
        'anxiety' => __('messages.category_anxiety'),
        'depression' => __('messages.category_depression'),
        // ...
    };
}
```

### 3. Translation Keys Added

#### English (lang/en/messages.php)
```php
// Articles - Additional
'article_tags' => 'Tags',
'no_articles' => 'No articles found',
'search_articles' => 'Search articles...',
'all_categories' => 'All Categories',
'category_mental_health' => 'Mental Health',
'category_anxiety' => 'Anxiety',
'category_depression' => 'Depression',
'category_stress' => 'Stress',
'category_self_care' => 'Self Care',
'category_therapy' => 'Therapy',
'category_other' => 'Other',
'read_more' => 'Read More',
'try_different_search' => 'Try using different keywords or filters',
```

#### Indonesian (lang/id/messages.php)
```php
// Articles - Additional
'article_tags' => 'Tag',
'no_articles' => 'Tidak ada artikel ditemukan',
'search_articles' => 'Cari artikel...',
'all_categories' => 'Semua Kategori',
'category_mental_health' => 'Kesehatan Mental',
'category_anxiety' => 'Kecemasan',
'category_depression' => 'Depresi',
'category_stress' => 'Stress',
'category_self_care' => 'Perawatan Diri',
'category_therapy' => 'Terapi',
'category_other' => 'Lainnya',
'read_more' => 'Baca Selengkapnya',
'try_different_search' => 'Coba gunakan kata kunci atau filter yang berbeda',
```

## Translation Helper Functions

### 1. Basic Translation
```php
{{ __('messages.key_name') }}
```

### 2. Translation with Parameters
```php
// In translation file
'welcome_message' => 'Welcome, :name!'

// In view
{{ __('messages.welcome_message', ['name' => $user->name]) }}
```

### 3. Pluralization
```php
// In translation file
'professionals_count' => '{0} No professionals|{1} One professional|[2,*] :count professionals'

// In view
{{ trans_choice('messages.professionals_count', $count, ['count' => $count]) }}
```

### 4. Dynamic Translation
```php
// Get translation based on variable
$key = "category_{$category}";
{{ __("messages.{$key}") }}
```

## Language Switcher Component

### Component Location
`resources/views/components/language-switcher.blade.php`

### Implementation
```php
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="flex items-center">
        <svg><!-- Globe icon --></svg>
        <span>{{ strtoupper(app()->getLocale()) }}</span>
    </button>

    <div x-show="open" class="absolute right-0 mt-2">
        <a href="{{ route('language.switch', 'id') }}">
            Bahasa Indonesia
        </a>
        <a href="{{ route('language.switch', 'en') }}">
            English
        </a>
    </div>
</div>
```

### Language Switch Route
```php
// routes/web.php
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'id'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');
```

### Middleware
```php
// app/Http/Middleware/SetLocale.php
public function handle($request, Closure $next)
{
    if (session()->has('locale')) {
        app()->setLocale(session('locale'));
    }
    return $next($request);
}
```

## Complete Translation Coverage

### Navigation
```php
'home' => 'Home' / 'Beranda',
'professionals' => 'Professionals' / 'Profesional',
'articles' => 'Articles' / 'Artikel',
'about_us' => 'About Us' / 'Tentang Kami',
'contact' => 'Contact' / 'Kontak',
'my_schedule' => 'My Schedule' / 'Jadwal Saya',
```

### Authentication
```php
'login' => 'Login' / 'Masuk',
'register' => 'Register' / 'Daftar',
'logout' => 'Logout' / 'Keluar',
```

### Professionals
```php
'find_professional' => 'Find Your Professional' / 'Temukan Professional Anda',
'psychiatrist' => 'Psychiatrist' / 'Psikiater',
'psychologist' => 'Psychologist' / 'Psikolog',
'conversationalist' => 'Conversationalist' / 'Conversationalist',
```

### Articles
```php
'mental_health_articles' => 'Mental Health Articles' / 'Artikel Kesehatan Mental',
'search_articles' => 'Search articles...' / 'Cari artikel...',
'read_more' => 'Read More' / 'Baca Selengkapnya',
```

### Contact
```php
'contact_page_title' => 'Contact Us' / 'Hubungi Kami',
'contact_email_title' => 'Email' / 'Email',
'contact_phone_title' => 'Phone' / 'Telepon',
'contact_address' => 'Address' / 'Alamat',
```

### Shopping & Appointments
```php
'shopping_cart' => 'Shopping Cart' / 'Keranjang Belanja',
'my_appointments' => 'My Appointments' / 'Janji Temu Saya',
'checkout' => 'Checkout' / 'Pembayaran',
```

## Special Cases

### 1. Line Breaks in Translations

#### Problem
```php
// This displays <br> as literal text
'address' => 'Line 1<br>Line 2<br>Line 3'
```

#### Solution
```php
// Use actual newlines in translation
'contact_address_value' => 'Jl. Sudirman No. 123
Jakarta Pusat 10110
Indonesia',

// In view, use nl2br() with escaping
{!! nl2br(e(__('messages.contact_address_value'))) !!}
```

### 2. HTML in Translations

#### Safe HTML
```php
// Use {!! !!} for trusted HTML
{!! __('messages.terms_html') !!}

// Always escape user input first
{!! nl2br(e($userInput)) !!}
```

#### Unsafe - Never Do This
```php
// DON'T: This is vulnerable to XSS
{!! $userInput !!}
```

### 3. Date Formatting

#### Using Carbon with Locale
```php
// Automatic locale
{{ $article->published_at->diffForHumans() }}

// Manual locale
{{ $article->published_at->locale(app()->getLocale())->isoFormat('LL') }}
```

## Best Practices

### 1. Naming Conventions
```php
// Good
'user_profile_title' => 'User Profile'
'button_save' => 'Save'
'error_validation_required' => 'This field is required'

// Bad
'Title' => 'User Profile'  // Not descriptive
'btn1' => 'Save'          // Not clear
'err' => 'Required'       // Too short
```

### 2. Organization
```php
// Group related translations
// Navigation
'nav_home' => '...',
'nav_about' => '...',

// Forms
'form_name' => '...',
'form_email' => '...',

// Messages
'message_success' => '...',
'message_error' => '...',
```

### 3. Consistency
```php
// Use consistent terminology
'professionals' => 'Professionals'  // ✓
'experts' => 'Experts'             // ✗ (same concept, different word)

// Use consistent capitalization
'read_more' => 'Read More'         // ✓
'search' => 'Search'               // ✓
```

### 4. Avoid Hardcoded Text
```php
// Bad
<button>Save</button>

// Good
<button>{{ __('messages.button_save') }}</button>
```

## Testing Translations

### Manual Testing Checklist
- [ ] Switch to Indonesian - all text displays in Indonesian
- [ ] Switch to English - all text displays in English
- [ ] No untranslated strings visible
- [ ] Special characters display correctly
- [ ] Line breaks work as expected
- [ ] Date/time formats appropriate for locale

### Automated Testing
```php
// tests/Feature/TranslationTest.php
public function test_articles_page_translates_to_english()
{
    session(['locale' => 'en']);

    $response = $this->get(route('articles.index'));

    $response->assertSee('Mental Health Articles');
    $response->assertSee('Search articles...');
    $response->assertDontSee('Artikel Kesehatan Mental');
}
```

## Adding New Translations

### Step-by-Step Guide

1. **Identify Text to Translate**
   ```php
   // Find hardcoded text
   <h1>Welcome to Teman Bicara</h1>
   ```

2. **Choose Translation Key**
   ```php
   // Use descriptive, dot-notation key
   'home.welcome_title'
   ```

3. **Add to Both Language Files**
   ```php
   // lang/en/messages.php
   'home_welcome_title' => 'Welcome to Teman Bicara',

   // lang/id/messages.php
   'home_welcome_title' => 'Selamat Datang di Teman Bicara',
   ```

4. **Update View**
   ```php
   <h1>{{ __('messages.home_welcome_title') }}</h1>
   ```

5. **Test Both Languages**
   - Switch to Indonesian
   - Switch to English
   - Verify text changes

## Common Issues & Solutions

### Issue 1: Translation Not Showing
```php
// Problem
{{ __('messages.my_key') }}
// Output: messages.my_key

// Solution
// 1. Check key exists in both language files
// 2. Clear cache: php artisan config:clear
// 3. Verify correct file: lang/en/messages.php
```

### Issue 2: Line Breaks Not Working
```php
// Problem
'address' => 'Line 1\nLine 2'  // Shows \n literally

// Solution
{!! nl2br(e(__('messages.address'))) !!}
```

### Issue 3: Special Characters
```php
// Problem
'text' => 'It's broken'  // Syntax error

// Solution
'text' => 'It\'s working'  // Escape single quote
// Or
'text' => "It's working"   // Use double quotes
```

## File Structure Reference

```
resources/
└── lang/
    ├── en/
    │   └── messages.php        # English translations
    └── id/
        └── messages.php        # Indonesian translations

resources/views/
├── components/
│   └── language-switcher.blade.php
└── pages/
    └── [various pages using translations]

app/
└── Http/
    └── Middleware/
        └── SetLocale.php       # Language switching logic
```

## Performance Considerations

### Translation Caching
```php
// Cache translations in production
php artisan config:cache

// Clear cache during development
php artisan config:clear
```

### Lazy Loading
```php
// Load only needed translations
trans('messages.specific_key')

// Instead of loading entire file
__('messages.*')
```

## Future Enhancements

### Planned Features
1. **Additional Languages:**
   - Mandarin (zh)
   - Japanese (ja)
   - Spanish (es)

2. **User Preferences:**
   - Save language preference in database
   - Auto-detect browser language
   - Remember choice across sessions

3. **Translation Management:**
   - Admin panel for managing translations
   - Export/import translation files
   - Translation versioning

4. **Dynamic Content:**
   - Translate article titles/content
   - Professional bio translations
   - Dynamic email templates

## Related Documentation
- [Navigation Improvements](./NAVIGATION-IMPROVEMENTS.md)
- [Chat System](./CHAT-SYSTEM.md)
- [Contact Information Updates](./CONTACT-UPDATES.md)
