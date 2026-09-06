<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\PassHoursService;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'dance_group_id', 'date', 'status', 'notes', 'recorded_by', 'made_up', 'date_of_made_up',
        'payment_id', 'hours_consumed', 'made_up_for_attendance_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'made_up' => 'boolean',
            'date_of_made_up' => 'date',
            'hours_consumed' => 'decimal:2',
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

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function madeUpFor(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'made_up_for_attendance_id');
    }

    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    public function lessonHours(): float
    {
        return app(PassHoursService::class)->lessonHours($this);
    }
}
