<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Payment;

class AttendanceAccounting
{
    public function __construct(
        private MonthlyPassService $monthly,
        private PassHoursService $legacy,
    ) {
    }

    public function apply(Attendance $attendance): array
    {
        $cancelled = app(ScheduleService::class)->sessionRow($attendance->danceGroup, $attendance->date);

        if ($cancelled !== null && $cancelled->isCancelled()) {
            $oldPaymentId = $attendance->payment_id;
            $previousConsumed = (float) $attendance->hours_consumed;

            if ($oldPaymentId !== null || $previousConsumed != 0) {
                $attendance->update(['payment_id' => null, 'hours_consumed' => 0]);

                if ($oldPaymentId) {
                    $payment = Payment::find($oldPaymentId);

                    if ($payment !== null) {
                        if ($payment->pass_type === 'monthly') {
                            $this->monthly->recomputeUsedHours($payment);
                        } else {
                            $payment->used_hours = round((float) Attendance::where('payment_id', $payment->id)->sum('hours_consumed'), 2);
                            $payment->save();
                        }
                    }
                }
            }

            return [__('Zajęcia w tym dniu są odwołane — obecność zapisana bez naliczania godzin.')];
        }

        if ($this->monthly->activePassFor($attendance->student, $attendance->date) !== null) {
            return $this->monthly->applyForAttendance($attendance);
        }

        return $this->legacy->apply($attendance);
    }
}