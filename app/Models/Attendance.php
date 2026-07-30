<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'dance_group_id', 'date', 'status', 'notes', 'recorded_by', 'made_up', 'date_of_made_up',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'made_up' => 'boolean',
            'date_of_made_up' => 'date',
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

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }
}
