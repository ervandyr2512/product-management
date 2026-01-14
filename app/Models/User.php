<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function professional()
    {
        return $this->hasOne(Professional::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProfessionals()
    {
        return $this->belongsToMany(Professional::class, 'favorites')
            ->withTimestamps();
    }

    public function isProfessional()
    {
        return $this->role === 'professional';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getRecommendedProfessionals($limit = 6)
    {
        // Get user's booking history to analyze preferences
        $userAppointments = $this->appointments()
            ->with('professional')
            ->get();

        $preferredTypes = $userAppointments->pluck('professional.type')->filter()->unique();
        $preferredSpecializations = $userAppointments->pluck('professional.specialization')->filter()->unique();

        // Build recommendation query
        $query = Professional::where('is_active', true)
            ->with(['user', 'reviews'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // If user has booking history, prioritize similar professionals
        if ($preferredTypes->isNotEmpty() || $preferredSpecializations->isNotEmpty()) {
            $query->where(function ($q) use ($preferredTypes, $preferredSpecializations) {
                if ($preferredTypes->isNotEmpty()) {
                    $q->orWhereIn('type', $preferredTypes->toArray());
                }
                if ($preferredSpecializations->isNotEmpty()) {
                    $q->orWhereIn('specialization', $preferredSpecializations->toArray());
                }
            });
        }

        // Exclude professionals user already booked recently
        $recentlyBookedIds = $userAppointments
            ->where('appointment_date', '>=', now()->subMonths(3))
            ->pluck('professional_id')
            ->unique();

        if ($recentlyBookedIds->isNotEmpty()) {
            $query->whereNotIn('id', $recentlyBookedIds->toArray());
        }

        // Order by rating and review count
        $query->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count');

        $recommendations = $query->take($limit)->get();

        // If not enough recommendations, fill with top-rated professionals
        if ($recommendations->count() < $limit) {
            $remainingLimit = $limit - $recommendations->count();
            $excludeIds = $recommendations->pluck('id')->merge($recentlyBookedIds)->unique();

            $topRated = Professional::where('is_active', true)
                ->with(['user', 'reviews'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->whereNotIn('id', $excludeIds->toArray())
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('reviews_count')
                ->take($remainingLimit)
                ->get();

            $recommendations = $recommendations->merge($topRated);
        }

        return $recommendations;
    }
}
