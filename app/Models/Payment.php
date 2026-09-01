<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'dance_group_id', 'event_id', 'pass_type', 'pass_type_id',
        'amount', 'total_hours', 'used_hours', 'valid_from', 'valid_until', 'status', 'is_paid', 'notes', 'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'total_hours' => 'decimal:2',
            'used_hours' => 'decimal:2',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_paid' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function danceGroup(): BelongsTo
    {
        return $this->belongsTo(DanceGroup::class, 'dance_group_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function passType(): BelongsTo
    {
        return $this->belongsTo(PassType::class, 'pass_type_id');
    }

    public function isMonthly(): bool
    {
        return $this->pass_type === 'monthly';
    }

    public function isSingle(): bool
    {
        return $this->pass_type === 'single';
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->valid_until !== null
            && $this->valid_until->gte(now()->startOfDay());
    }

    public function remainingHours(): ?float
    {
        if ($this->total_hours === null) {
            return null;
        }

        return round((float) $this->total_hours - (float) $this->used_hours, 2);
    }

    public function hasHoursRemaining(): bool
    {
        return $this->total_hours === null || $this->remainingHours() > 0;
    }

    public function hoursLabel(): ?string
    {
        $remaining = $this->remainingHours();

        if ($remaining === null) {
            return null;
        }

        return (string) $remaining;
    }

    public function daysLeft(): int
    {
        if ($this->valid_until === null) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->valid_until->copy()->startOfDay());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('valid_until', '>=', now()->startOfDay());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'active')->where('valid_until', '<', now()->startOfDay());
            });
    }
}
