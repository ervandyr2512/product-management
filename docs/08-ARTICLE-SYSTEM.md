# Article System - Teman Bicara

## Overview

Article system di Teman Bicara adalah blog/content management untuk artikel-artikel terkait kesehatan mental, tips, dan informasi edukatif.

## Features

- Browse artikel berdasarkan kategori
- Search artikel
- View artikel detail
- SEO-friendly URLs (slugs)
- Published/draft status
- Featured images
- Categories
- Author attribution
- Published date

## Database Schema

```sql
CREATE TABLE articles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255) NULL,
    author VARCHAR(255) DEFAULT 'Admin',
    category ENUM(
        'mental_health',
        'anxiety',
        'depression',
        'stress',
        'self_care',
        'therapy',
        'other'
    ) NOT NULL,
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_published (is_published, published_at),
    INDEX idx_slug (slug)
);
```

## Model

**File**: `app/Models/Article.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author',
        'category',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Scope for published articles only
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope for filtering by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('excerpt', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'mental_health' => 'Kesehatan Mental',
            'anxiety' => 'Kecemasan',
            'depression' => 'Depresi',
            'stress' => 'Stres',
            'self_care' => 'Perawatan Diri',
            'therapy' => 'Terapi',
            'other' => 'Lainnya',
            default => ucfirst($this->category),
        };
    }

    /**
     * Get reading time estimate
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200); // Average reading speed: 200 words/minute
        return $minutes;
    }

    /**
     * Auto-generate slug from title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }
}
```

## Categories

### Available Categories

```php
const CATEGORIES = [
    'mental_health' => 'Kesehatan Mental',
    'anxiety' => 'Kecemasan',
    'depression' => 'Depresi',
    'stress' => 'Stres',
    'self_care' => 'Perawatan Diri',
    'therapy' => 'Terapi',
    'other' => 'Lainnya',
];
```

### Category Colors (UI)

```php
const CATEGORY_COLORS = [
    'mental_health' => 'bg-purple-100 text-purple-800',
    'anxiety' => 'bg-yellow-100 text-yellow-800',
    'depression' => 'bg-blue-100 text-blue-800',
    'stress' => 'bg-red-100 text-red-800',
    'self_care' => 'bg-green-100 text-green-800',
    'therapy' => 'bg-indigo-100 text-indigo-800',
    'other' => 'bg-gray-100 text-gray-800',
];
```

## Routes

### Public Routes

```php
// routes/web.php

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
```

### Admin Routes (Future)

```php
// For admin article management
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resource('admin/articles', AdminArticleController::class);
});
```

## Controller

**File**: `app/Http/Controllers/ArticleController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display list of articles
     */
    public function index(Request $request)
    {
        $query = Article::published()
            ->orderBy('published_at', 'desc');

        // Filter by category
        if ($request->filled('category')) {
            $query->category($request->category);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $articles = $query->paginate(9);

        // Get all categories for filter
        $categories = [
            'mental_health' => 'Kesehatan Mental',
            'anxiety' => 'Kecemasan',
            'depression' => 'Depresi',
            'stress' => 'Stres',
            'self_care' => 'Perawatan Diri',
            'therapy' => 'Terapi',
            'other' => 'Lainnya',
        ];

        return view('articles.index', compact('articles', 'categories'));
    }

    /**
     * Display article detail
     */
    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Get related articles (same category, exclude current)
        $relatedArticles = Article::published()
            ->where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->limit(3)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }
}
```

## Views

### Index Page

**File**: `resources/views/articles/index.blade.php`

```php
<x-app-layout>
    <div class="gradient-bg py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">Artikel Kesehatan Mental</h1>
            <p class="text-xl text-white/90">Tips, informasi, dan panduan untuk kesehatan mental Anda</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Search & Filter -->
        <div class="mb-8">
            <form method="GET" action="{{ route('articles.index') }}" class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari artikel..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Category Filter -->
                <div class="md:w-64">
                    <select name="category"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                            onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Button -->
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Cari
                </button>
            </form>
        </div>

        <!-- Articles Grid -->
        @if($articles->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($articles as $article)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <!-- Featured Image -->
                        @if($article->featured_image)
                            <img src="{{ asset('storage/' . $article->featured_image) }}"
                                 alt="{{ $article->title }}"
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-r from-purple-400 to-indigo-500"></div>
                        @endif

                        <!-- Content -->
                        <div class="p-6">
                            <!-- Category Badge -->
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-3
                                {{ match($article->category) {
                                    'mental_health' => 'bg-purple-100 text-purple-800',
                                    'anxiety' => 'bg-yellow-100 text-yellow-800',
                                    'depression' => 'bg-blue-100 text-blue-800',
                                    'stress' => 'bg-red-100 text-red-800',
                                    'self_care' => 'bg-green-100 text-green-800',
                                    'therapy' => 'bg-indigo-100 text-indigo-800',
                                    default => 'bg-gray-100 text-gray-800',
                                } }}">
                                {{ $article->category_label }}
                            </span>

                            <!-- Title -->
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                <a href="{{ route('articles.show', $article->slug) }}" class="hover:text-purple-600">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            <!-- Excerpt -->
                            <p class="text-gray-600 mb-4 line-clamp-3">
                                {{ $article->excerpt }}
                            </p>

                            <!-- Meta -->
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>{{ $article->published_at->format('d M Y') }}</span>
                                <span>{{ $article->reading_time }} min read</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">Tidak ada artikel ditemukan.</p>
            </div>
        @endif
    </div>
</x-app-layout>
```

### Show Page (Article Detail)

**File**: `resources/views/articles/show.blade.php`

```php
<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('articles.index') }}" class="text-purple-600 hover:text-purple-800">
                ← Kembali ke Artikel
            </a>
        </div>

        <!-- Article Header -->
        <article class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Featured Image -->
            @if($article->featured_image)
                <img src="{{ asset('storage/' . $article->featured_image) }}"
                     alt="{{ $article->title }}"
                     class="w-full h-96 object-cover">
            @endif

            <!-- Content -->
            <div class="p-8">
                <!-- Category Badge -->
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-4
                    {{ match($article->category) {
                        'mental_health' => 'bg-purple-100 text-purple-800',
                        'anxiety' => 'bg-yellow-100 text-yellow-800',
                        'depression' => 'bg-blue-100 text-blue-800',
                        'stress' => 'bg-red-100 text-red-800',
                        'self_care' => 'bg-green-100 text-green-800',
                        'therapy' => 'bg-indigo-100 text-indigo-800',
                        default => 'bg-gray-100 text-gray-800',
                    } }}">
                    {{ $article->category_label }}
                </span>

                <!-- Title -->
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $article->title }}</h1>

                <!-- Meta -->
                <div class="flex items-center text-gray-500 mb-6 space-x-4">
                    <span>Oleh {{ $article->author }}</span>
                    <span>•</span>
                    <span>{{ $article->published_at->format('d M Y') }}</span>
                    <span>•</span>
                    <span>{{ $article->reading_time }} min read</span>
                </div>

                <!-- Excerpt -->
                <div class="text-xl text-gray-700 mb-8 italic border-l-4 border-purple-500 pl-4">
                    {{ $article->excerpt }}
                </div>

                <!-- Article Content -->
                <div class="prose prose-lg max-w-none">
                    {!! nl2br(e($article->content)) !!}
                </div>
            </div>
        </article>

        <!-- Related Articles -->
        @if($relatedArticles->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Artikel Terkait</h2>

                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($relatedArticles as $related)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            @if($related->featured_image)
                                <img src="{{ asset('storage/' . $related->featured_image) }}"
                                     alt="{{ $related->title }}"
                                     class="w-full h-32 object-cover">
                            @else
                                <div class="w-full h-32 bg-gradient-to-r from-purple-400 to-indigo-500"></div>
                            @endif

                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 mb-2">
                                    <a href="{{ route('articles.show', $related->slug) }}" class="hover:text-purple-600">
                                        {{ $related->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ $related->published_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
```

## Factory

**File**: `database/factories/ArticleFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        $categories = ['mental_health', 'anxiety', 'depression', 'stress', 'self_care', 'therapy', 'other'];
        $category = $this->faker->randomElement($categories);

        // Category-specific content
        $content = $this->generateContentByCategory($category);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->paragraph(2),
            'content' => $content,
            'featured_image' => null, // Can add image path if needed
            'author' => $this->faker->randomElement(['Dr. Sarah Johnson', 'Prof. Michael Chen', 'Dr. Emily Rodriguez', 'Admin']),
            'category' => $category,
            'is_published' => true,
            'published_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    private function generateContentByCategory($category)
    {
        $contents = [
            'mental_health' => "Kesehatan mental adalah aspek penting dalam kehidupan sehari-hari. Artikel ini membahas berbagai cara untuk menjaga kesehatan mental Anda.\n\nPenting untuk memahami bahwa kesehatan mental sama pentingnya dengan kesehatan fisik. Berikut beberapa tips yang dapat membantu:\n\n1. Rutin berolahraga\n2. Tidur yang cukup\n3. Makan makanan bergizi\n4. Berbicara dengan orang yang dipercaya\n5. Mencari bantuan profesional jika diperlukan",

            'anxiety' => "Kecemasan adalah respons alami tubuh terhadap stres. Namun, ketika kecemasan menjadi berlebihan, dapat mengganggu kehidupan sehari-hari.\n\nBeberapa teknik untuk mengelola kecemasan:\n\n1. Teknik pernapasan dalam\n2. Meditasi dan mindfulness\n3. Olahraga teratur\n4. Menghindari kafein berlebihan\n5. Tidur yang cukup\n\nJika kecemasan terus berlanjut, konsultasikan dengan profesional kesehatan mental.",

            'depression' => "Depresi adalah kondisi kesehatan mental yang memerlukan perhatian serius. Artikel ini membahas tanda-tanda depresi dan cara mengatasinya.\n\nTanda-tanda depresi meliputi:\n- Perasaan sedih yang berkepanjangan\n- Kehilangan minat pada aktivitas yang biasa dinikmati\n- Perubahan pola tidur dan makan\n- Kesulitan berkonsentrasi\n- Pikiran negatif yang berulang\n\nPenting untuk mencari bantuan profesional jika Anda mengalami gejala-gejala ini.",

            'stress' => "Stres adalah bagian dari kehidupan, tetapi penting untuk mengelolanya dengan baik agar tidak berdampak negatif pada kesehatan.\n\nCara mengelola stres:\n\n1. Identifikasi sumber stres\n2. Kelola waktu dengan baik\n3. Tetapkan prioritas\n4. Belajar mengatakan tidak\n5. Luangkan waktu untuk relaksasi\n6. Olahraga teratur\n7. Tidur cukup",

            'self_care' => "Self-care atau perawatan diri adalah praktik yang penting untuk menjaga kesehatan mental dan fisik.\n\nBeberapa praktik self-care yang dapat Anda lakukan:\n\n1. Meluangkan waktu untuk hobi\n2. Menjaga hubungan sosial yang sehat\n3. Menetapkan batasan yang jelas\n4. Makan makanan bergizi\n5. Berolahraga secara teratur\n6. Tidur yang cukup\n7. Melakukan aktivitas yang Anda nikmati",

            'therapy' => "Terapi adalah alat yang efektif untuk mengatasi berbagai masalah kesehatan mental. Artikel ini membahas berbagai jenis terapi dan manfaatnya.\n\nJenis-jenis terapi:\n\n1. Cognitive Behavioral Therapy (CBT)\n2. Psikoanalisis\n3. Terapi Humanistik\n4. Terapi Kelompok\n5. Terapi Keluarga\n\nSetiap jenis terapi memiliki pendekatan yang berbeda, dan penting untuk menemukan yang paling sesuai dengan kebutuhan Anda.",

            'other' => $this->faker->paragraphs(5, true),
        ];

        return $contents[$category] ?? $this->faker->paragraphs(5, true);
    }

    /**
     * Article that is draft (not published)
     */
    public function draft()
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
```

## Seeder

**File**: `database/seeders/ArticleSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // Create 30 published articles
        Article::factory()->count(30)->create();

        // Create 5 draft articles
        Article::factory()->count(5)->draft()->create();
    }
}
```

**Add to DatabaseSeeder:**

```php
// database/seeders/DatabaseSeeder.php

public function run(): void
{
    $this->call([
        ArticleSeeder::class,
    ]);
}
```

## SEO Optimization

### Meta Tags in Article Show Page

```php
<!-- resources/views/articles/show.blade.php -->

<x-app-layout>
    <x-slot name="meta">
        <meta name="description" content="{{ $article->excerpt }}">
        <meta name="keywords" content="kesehatan mental, {{ $article->category_label }}, teman bicara">
        <meta name="author" content="{{ $article->author }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="article">
        <meta property="og:title" content="{{ $article->title }}">
        <meta property="og:description" content="{{ $article->excerpt }}">
        @if($article->featured_image)
            <meta property="og:image" content="{{ asset('storage/' . $article->featured_image) }}">
        @endif

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $article->title }}">
        <meta name="twitter:description" content="{{ $article->excerpt }}">
        @if($article->featured_image)
            <meta name="twitter:image" content="{{ asset('storage/' . $article->featured_image) }}">
        @endif
    </x-slot>

    <!-- Article content -->
</x-app-layout>
```

## Future Enhancements

### Admin Panel for Article Management

```php
// app/Http/Controllers/Admin/ArticleController.php

class ArticleController extends Controller
{
    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'excerpt' => 'required',
            'content' => 'required',
            'category' => 'required|in:mental_health,anxiety,depression,stress,self_care,therapy,other',
            'featured_image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        Article::create($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dibuat');
    }
}
```

### Comments System

```php
// Future: Add comments table
Schema::create('article_comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('article_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->text('comment');
    $table->boolean('is_approved')->default(false);
    $table->timestamps();
});
```

### Like/Bookmark System

```php
// Future: Add likes table
Schema::create('article_likes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('article_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
    $table->unique(['article_id', 'user_id']);
});
```

## Testing

### Feature Test Example

```php
// tests/Feature/ArticleTest.php

public function test_user_can_view_article_index()
{
    $response = $this->get('/articles');
    $response->assertStatus(200);
    $response->assertViewIs('articles.index');
}

public function test_user_can_view_article_detail()
{
    $article = Article::factory()->create([
        'is_published' => true,
        'published_at' => now(),
    ]);

    $response = $this->get("/articles/{$article->slug}");
    $response->assertStatus(200);
    $response->assertSee($article->title);
}

public function test_unpublished_article_is_not_accessible()
{
    $article = Article::factory()->create([
        'is_published' => false,
        'published_at' => null,
    ]);

    $response = $this->get("/articles/{$article->slug}");
    $response->assertStatus(404);
}

public function test_can_filter_articles_by_category()
{
    Article::factory()->create(['category' => 'anxiety', 'is_published' => true, 'published_at' => now()]);
    Article::factory()->create(['category' => 'depression', 'is_published' => true, 'published_at' => now()]);

    $response = $this->get('/articles?category=anxiety');
    $response->assertStatus(200);
}
```

## Best Practices

1. **SEO-Friendly URLs**: Always use slugs instead of IDs
2. **Published Status**: Only show published articles to public
3. **Image Optimization**: Compress featured images before upload
4. **Content Security**: Escape HTML in content to prevent XSS
5. **Pagination**: Don't load all articles at once
6. **Caching**: Cache article list for better performance
7. **Related Articles**: Show related content to increase engagement
8. **Reading Time**: Calculate and display estimated reading time
9. **Categories**: Use consistent category naming
10. **Search**: Implement full-text search for better UX

## Next Documentation

- [09-API-ENDPOINTS.md](09-API-ENDPOINTS.md) - Complete routes and endpoints reference
- [10-TESTING.md](10-TESTING.md) - Testing guide and examples
