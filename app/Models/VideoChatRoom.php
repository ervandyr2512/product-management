<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VideoChatRoom extends Model
{
    protected $fillable = [
        'appointment_id',
        'room_id',
        'status',
        'started_at',
        'ended_at',
        'duration_minutes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            if (!$room->room_id) {
                $room->room_id = Str::uuid()->toString();
            }
        });
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function canJoin(): bool
    {
        // NOTE: Time restrictions removed for testing purposes
        // In production, you should re-enable the time-based validation

        // TESTING MODE: Allow joining anytime as long as session hasn't ended
        return $this->status !== 'ended';

        /* PRODUCTION CODE - Uncomment this block when deploying:
        $now = now();
        $appointmentTime = $this->appointment->appointment_date . ' ' . $this->appointment->schedule->start_time;
        $appointmentDateTime = \Carbon\Carbon::parse($appointmentTime);

        // Allow joining 10 minutes before scheduled time
        $canJoinFrom = $appointmentDateTime->copy()->subMinutes(10);

        // Can join until 30 minutes after scheduled time
        $canJoinUntil = $appointmentDateTime->copy()->addMinutes(30);

        return $now->between($canJoinFrom, $canJoinUntil) && $this->status !== 'ended';
        */
    }

    public function start(): void
    {
        $this->update([
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function end(): void
    {
        $duration = null;
        if ($this->started_at) {
            $duration = now()->diffInMinutes($this->started_at);
        }

        $this->update([
            'status' => 'ended',
            'ended_at' => now(),
            'duration_minutes' => $duration,
        ]);
    }
}
