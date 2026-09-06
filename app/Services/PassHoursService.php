<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PassHoursService
{
    public function apply(Attendance $attendance): array
    {
        $warnings = [];

        DB::transaction(function () use ($attendance, &$warnings) {
            if ($attendance->status === 'present') {
                $warnings = $this->consume($attendance);
            } else {
                $this->refund($attendance);
            }
        });

        return $warnings;
    }

    private function consume(Attendance $attendance): array
    {
        if ($attendance->payment_id !== null) {
            return [];
        }

        $hours = $this->lessonHours($attendance);
        $payment = $this->findActivePayment($attendance);

        if ($payment === null) {
            return [$this->noPassMessage($attendance)];
        }

        $remaining = (float) $payment->remainingHours();
        $deduct = min($hours, max(0, $remaining));

        $payment->used_hours = round((float) $payment->used_hours + $deduct, 2);
        $payment->save();

        $attendance->payment_id = $payment->id;
        $attendance->hours_consumed = $deduct;
        $attendance->save();

        if ($deduct < $hours) {
            return [$this->insufficientHoursMessage($attendance, $remaining)];
        }

        return [];
    }

    private function refund(Attendance $attendance): void
    {
        if ($attendance->payment_id === null) {
            return;
        }

        $payment = Payment::find($attendance->payment_id);

        if ($payment) {
            $used = round((float) $payment->used_hours - (float) $attendance->hours_consumed, 2);
            $payment->used_hours = max(0, $used);
            $payment->save();
        }

        $attendance->payment_id = null;
        $attendance->hours_consumed = 0;
        $attendance->save();
    }

    private function findActivePayment(Attendance $attendance): ?Payment
    {
        return Payment::query()
            ->where('student_id', $attendance->student_id)
            ->where('status', 'active')
            ->where('valid_until', '>=', now()->startOfDay())
            ->whereNotNull('total_hours')
            ->whereColumn('used_hours', '<', 'total_hours')
            ->orderBy('valid_from')
            ->orderBy('id')
            ->first();
    }

    public function lessonHours(Attendance $attendance): float
    {
        $group = $attendance->danceGroup;
        $dayOfWeek = $attendance->date->dayOfWeek == Carbon::SUNDAY ? 7 : $attendance->date->dayOfWeek;

        foreach ($group?->class_times ?? [] as $slot) {
            if ((int) $slot['day'] !== $dayOfWeek || empty($slot['start']) || empty($slot['end'])) {
                continue;
            }

            $start = Carbon::parse($slot['start']);
            $end = Carbon::parse($slot['end']);

            if ($end > $start) {
                return round($end->diffInMinutes($start, true) / 60, 2);
            }
        }

        return 1.0;
    }

    private function noPassMessage(Attendance $attendance): string
    {
        return __(':name nie ma aktywnego karnetu z godzinami', [
            'name' => $attendance->student?->full_name ?? $attendance->student_id,
        ]);
    }

    private function insufficientHoursMessage(Attendance $attendance, float $remaining): string
    {
        return __(':name ma tylko :hours h — odliczono pozostałe godziny', [
            'name' => $attendance->student?->full_name ?? $attendance->student_id,
            'hours' => $remaining,
        ]);
    }
}