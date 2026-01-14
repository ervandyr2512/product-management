<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'professional_id',
        'schedule_id',
        'appointment_date',
        'start_time',
        'end_time',
        'duration',
        'price',
        'status',
        'video_chat_link',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function videoChatRoom()
    {
        return $this->hasOne(VideoChatRoom::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function canStartVideoChat(): bool
    {
        // NOTE: Time restrictions removed for testing purposes
        // In production, you should re-enable the time-based validation

        if ($this->status !== 'confirmed' || !$this->payment || $this->payment->status !== 'success') {
            return false;
        }

        // TESTING MODE: Allow access anytime if appointment is confirmed and paid
        return true;

        /* PRODUCTION CODE - Uncomment this block when deploying:
        $now = now();
        $appointmentTime = $this->appointment_date->format('Y-m-d') . ' ' . $this->schedule->start_time;
        $appointmentDateTime = \Carbon\Carbon::parse($appointmentTime);

        // Allow joining 10 minutes before scheduled time
        $canJoinFrom = $appointmentDateTime->copy()->subMinutes(10);

        // Can join until 30 minutes after scheduled time
        $canJoinUntil = $appointmentDateTime->copy()->addMinutes(30);

        return $now->between($canJoinFrom, $canJoinUntil);
        */
    }
}
