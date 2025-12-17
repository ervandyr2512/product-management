<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'mental_health' => 'Kesehatan Mental',
            'anxiety' => 'Kecemasan',
            'depression' => 'Depresi',
            'stress' => 'Stress',
            'self_care' => 'Perawatan Diri',
            'therapy' => 'Terapi',
            'other' => 'Lainnya',
            default => 'Lainnya',
        };
    }
}
