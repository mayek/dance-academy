<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\DanceGroup;
use App\Models\PassType;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyPassService
{
    public const MAKEUP_WINDOW_DAYS = 14;

    public function fee(): float
    {
        return (float) (
            config('app.monthly_pass_fee')
            ?? PassType::where('type', 'monthly')->orderBy('price')->value('price')
            ?? 0
        );
    }

    public function computeTotalHours(User $student, Carbon $month): float
    {
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $total = 0.0;

        foreach ($student->enrolledGroups()->with('sessions')->get() as $group) {
            $joinedAt = $group->pivot?->created_at ? Carbon::parse($group->pivot->created_at) : $monthStart;
            $from = $joinedAt->lt($monthStart) ? $monthStart->copy() : $joinedAt->copy()->startOfDay();

            $sessions = $group->sessions
                ->where('status', '!=', 'cancelled')
                ->filter(fn (ClassSession $s) => $s->date->between($from, $monthEnd));

            if ($sessions->isNotEmpty()) {
                foreach ($sessions as $session) {
                    $total += (float) $session->durationHours();
                }
            } else {
                $total += app(ScheduleService::class)->hoursForGroup($group, $from, $monthEnd);
            }
        }

        return round($total, 2);
    }

    public function activePassFor(User $student, Carbon $date): ?Payment
    {
        return Payment::query()
            ->where('student_id', $student->id)
            ->where('pass_type', 'monthly')
            ->whereNull('dance_group_id')
            ->where('status', 'active')
            ->whereDate('valid_from', '<=', $date->toDateString())
            ->whereDate('valid_until', '>=', $date->toDateString())
            ->orderByDesc('valid_from')
            ->orderByDesc('id')
            ->first();
    }

    public function obligationFor(User $student, Carbon $month): ?Payment
    {
        return Payment::query()
            ->where('student_id', $student->id)
            ->where('pass_type', 'monthly')
            ->whereNull('dance_group_id')
            ->where('status', '!=', 'cancelled')
            ->whereDate('valid_from', $month->copy()->startOfMonth()->toDateString())
            ->first();
    }

    public function ensureObligation(User $student, Carbon $month): ?Payment
    {
        $existing = $this->obligationFor($student, $month);
        if ($existing !== null) {
            return $existing;
        }

        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $hasEnrollment = $student->enrolledGroups()
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $monthStart->toDateString()))
            ->exists();

        if (!$hasEnrollment) {
            return null;
        }

        return Payment::create([
            'student_id' => $student->id,
            'dance_group_id' => null,
            'pass_type' => 'monthly',
            'pass_type_id' => PassType::where('type', 'monthly')->orderBy('price')->value('id'),
            'amount' => $this->fee(),
            'total_hours' => $this->computeTotalHours($student, $month),
            'used_hours' => 0,
            'valid_from' => $monthStart,
            'valid_until' => $monthEnd->endOfDay(),
            'status' => 'active',
            'is_paid' => false,
            'source' => 'auto',
            'recorded_by' => null,
        ]);
    }

    public function applyForAttendance(Attendance $attendance): array
    {
        $pass = $this->activePassFor($attendance->student, $attendance->date);

        if ($pass === null) {
            return [__(':name nie ma aktywnego karnetu miesięcznego na :date', [
                'name' => $attendance->student?->full_name ?? $attendance->student_id,
                'date' => $attendance->date->format('d.m.Y'),
            ])];
        }

        $duration = $this->sessionDurationFor($attendance->danceGroup, $attendance->date);

        $attendance->payment_id = $pass->id;
        $attendance->hours_consumed = $attendance->status === 'excused' ? 0 : $duration;
        $attendance->save();

        $this->recomputeUsedHours($pass);

        return [];
    }

    public function markMadeUp(Attendance $absence, int $makeUpGroupId, Carbon $makeUpDate): array
    {
        $warnings = [];

        if ($absence->status !== 'absent') {
            return [__('Ta nieobecność nie kwalifikuje się do odrobienia.')];
        }

        if ($absence->made_up) {
            return [__('Ta nieobecność została już odrobiona.')];
        }

        if ($makeUpDate->lt($absence->date) || $makeUpDate->gt($absence->date->copy()->addDays(self::MAKEUP_WINDOW_DAYS))) {
            return [__('Zajęcia odrabiane muszą odbyć się do :days dni od nieobecności.', [
                'days' => self::MAKEUP_WINDOW_DAYS,
            ])];
        }

        $makeUpSession = ClassSession::where('dance_group_id', $makeUpGroupId)
            ->whereDate('date', $makeUpDate->toDateString())
            ->first();

        if ($makeUpSession && $makeUpSession->isCancelled()) {
            return [__('Wybrane zajęcia są odwołane.')];
        }

        $pass = $this->activePassFor($absence->student, $absence->date);

        if ($pass === null) {
            return [__('Uczeń nie miał aktywnego karnetu w dniu nieobecności.')];
        }

        $alreadyEnrolled = $absence->student->enrolledGroups()
            ->where('dance_groups.id', $makeUpGroupId)
            ->exists();

        DB::transaction(function () use ($absence, $makeUpGroupId, $makeUpDate, $pass, $alreadyEnrolled) {
            if (!$alreadyEnrolled) {
                $makeUpGroup = DanceGroup::find($makeUpGroupId);
                $duration = $this->sessionDurationFor($makeUpGroup, $makeUpDate);

                Attendance::updateOrCreate(
                    [
                        'student_id' => $absence->student_id,
                        'dance_group_id' => $makeUpGroupId,
                        'date' => $makeUpDate->toDateString(),
                    ],
                    [
                        'status' => 'present',
                        'payment_id' => $pass->id,
                        'hours_consumed' => $duration,
                        'made_up_for_attendance_id' => $absence->id,
                        'recorded_by' => $absence->recorded_by,
                    ]
                );
            }

            $absence->update([
                'status' => 'excused',
                'made_up' => true,
                'date_of_made_up' => $makeUpDate->toDateString(),
                'hours_consumed' => 0,
            ]);
        });

        $this->recomputeUsedHours($pass);

        return $warnings;
    }

    public function recomputeUsedHours(Payment $payment): void
    {
        $used = (float) Attendance::where('payment_id', $payment->id)->sum('hours_consumed');
        $total = $payment->total_hours !== null ? (float) $payment->total_hours : null;

        $payment->used_hours = $total !== null ? min($used, $total) : $used;
        $payment->save();
    }

    public function refreshTotalHours(Payment $payment): void
    {
        $student = $payment->student;
        $month = $payment->valid_from;

        $payment->total_hours = $this->computeTotalHours($student, $month);
        $payment->save();

        $this->recomputeUsedHours($payment);
    }

    private function sessionDurationFor($group, Carbon $date): float
    {
        $schedule = app(ScheduleService::class);

        $session = $schedule->sessionForGroupOnDate($group, $date);
        if ($session !== null) {
            return (float) $session->durationHours();
        }

        return $schedule->hoursForGroupOnDate($group, $date) ?? 1.0;
    }
}