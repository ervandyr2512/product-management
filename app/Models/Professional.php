<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'license_number',
        'bio',
        'specialization',
        'experience_years',
        'rate_30min',
        'rate_60min',
        'profile_photo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rate_30min' => 'decimal:2',
        'rate_60min' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function availableSchedules()
    {
        return $this->schedules()->where('is_available', true)->where('date', '>=', now()->toDateString());
    }
}
