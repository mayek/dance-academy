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
            $joinedAt = $group->pivot?->joined_at ?? $group->pivot?->created_at;
            $leftAt = $group->pivot?->left_at;

            $from = $monthStart->copy();
            if ($joinedAt) {
                $joined = Carbon::parse($joinedAt)->startOfDay();
                if ($joined->gt($from)) {
                    $from = $joined;
                }
            }

            $to = $monthEnd->copy();
            if ($leftAt) {
                $left = Carbon::parse($leftAt)->startOfDay();
                if ($left->lte($monthStart)) {
                    continue;
                }
                if ($left->lt($to)) {
                    $to = $left->copy()->subDay()->endOfDay();
                }
            }

            if ($from->gt($to)) {
                continue;
            }

            $sessions = $group->sessions
                ->where('status', '!=', 'cancelled')
                ->filter(fn (ClassSession $s) => $s->date->between($from, $to));

            if ($sessions->isNotEmpty()) {
                foreach ($sessions as $session) {
                    $total += (float) $session->durationHours();
                }
            } else {
                $total += app(ScheduleService::class)->hoursForGroup($group, $from, $to);
            }
        }

        return round($total, 2);
    }

    public function computeHoursBetween(User $student, Carbon $from, Carbon $to): float
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();

        if ($from->isSameDay($to)) {
            return $this->computeHoursOnDate($student, $from);
        }

        $total = 0.0;
        $month = $from->copy()->startOfMonth();
        $lastMonth = $to->copy()->startOfMonth();

        while ($month->lte($lastMonth)) {
            $total += $this->computeTotalHours($student, $month);
            $month->addMonth();
        }

        return round($total, 2);
    }

    public function computeHoursOnDate(User $student, Carbon $date): float
    {
        $date = $date->copy()->startOfDay();
        $schedule = app(ScheduleService::class);
        $total = 0.0;

        foreach ($student->enrolledGroups()->with('sessions')->get() as $group) {
            $joinedAt = $group->pivot?->joined_at ?? $group->pivot?->created_at;
            $leftAt = $group->pivot?->left_at;

            if ($joinedAt && Carbon::parse($joinedAt)->startOfDay()->gt($date)) {
                continue;
            }

            if ($leftAt && Carbon::parse($leftAt)->startOfDay()->lte($date)) {
                continue;
            }

            if ($group->start_date && $date->lt(Carbon::parse($group->start_date)->startOfDay())) {
                continue;
            }

            if ($group->end_date && $date->gt(Carbon::parse($group->end_date)->endOfDay())) {
                continue;
            }

            $session = $schedule->sessionForGroupOnDate($group, $date);

            if ($session !== null) {
                $total += (float) $session->durationHours();
                continue;
            }

            $total += $schedule->hoursForGroupOnDate($group, $date) ?? 0.0;
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

        if (!$this->isEnrolledInMonth($student, $month)) {
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

    public function refreshTotalsForMonth(Carbon $month, ?int $groupId = null): int
    {
        $monthStart = $month->copy()->startOfMonth()->toDateString();

        $query = Payment::query()
            ->where('pass_type', 'monthly')
            ->whereNull('dance_group_id')
            ->whereDate('valid_from', $monthStart)
            ->where('status', '!=', 'cancelled')
            ->with('student.enrolledGroups');

        if ($groupId) {
            $query->whereHas('student.enrolledGroups', fn ($q) => $q->where('dance_groups.id', $groupId));
        }

        $count = 0;

        foreach ($query->get() as $payment) {
            $this->refreshTotalHours($payment);
            $count++;
        }

        return $count;
    }

    public function isEnrolledInMonth(User $student, Carbon $month): bool
    {
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        return $student->enrolledGroups()
            ->get()
            ->contains(function ($group) use ($monthStart, $monthEnd) {
                $joinedAt = $group->pivot?->joined_at ?? $group->pivot?->created_at;
                $leftAt = $group->pivot?->left_at;

                if ($joinedAt && Carbon::parse($joinedAt)->gt($monthEnd)) {
                    return false;
                }

                if ($leftAt && Carbon::parse($leftAt)->lte($monthStart)) {
                    return false;
                }

                return true;
            });
    }

    public function purgeFutureObligations(User $student): int
    {
        $count = 0;
        $nowMonth = now()->startOfMonth();

        $obligations = Payment::query()
            ->where('student_id', $student->id)
            ->where('pass_type', 'monthly')
            ->whereNull('dance_group_id')
            ->where('status', 'active')
            ->where('is_paid', false)
            ->whereDate('valid_from', '>', $nowMonth->toDateString())
            ->get();

        foreach ($obligations as $obligation) {
            $month = $obligation->valid_from->copy()->startOfMonth();

            if ($this->isEnrolledInMonth($student, $month)) {
                continue;
            }

            $obligation->update(['status' => 'cancelled']);
            $count++;
        }

        return $count;
    }

    public function refreshAllForStudent(User $student): int
    {
        $obligations = Payment::query()
            ->where('student_id', $student->id)
            ->where('pass_type', 'monthly')
            ->whereNull('dance_group_id')
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($obligations as $obligation) {
            $this->refreshTotalHours($obligation);
        }

        return $obligations->count();
    }

    private function sessionDurationFor($group, Carbon $date): float
    {
        $schedule = app(ScheduleService::class);

        $session = $schedule->sessionForGroupOnDate($group, $date);
        if ($session !== null) {
            return (float) $session->durationHours();
        }

        if ($schedule->sessionRow($group, $date)?->isCancelled()) {
            return 0.0;
        }

        return $schedule->hoursForGroupOnDate($group, $date) ?? 1.0;
    }
}