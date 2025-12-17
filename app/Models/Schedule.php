<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'professional_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('date', '>=', now()->toDateString());
    }
}
