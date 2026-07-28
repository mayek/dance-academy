<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'dance_group_id', 'pass_type', 'amount',
        'valid_from', 'valid_until', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'valid_from' => 'date',
            'valid_until' => 'date',
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
        return $this->status === 'active' && $this->valid_until?->isFuture();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('valid_until', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'active')->where('valid_until', '<', now());
            });
    }
}
