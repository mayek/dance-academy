<?php

namespace App\Services;

use App\Models\Attendance;

class AttendanceAccounting
{
    public function __construct(
        private MonthlyPassService $monthly,
        private PassHoursService $legacy,
    ) {
    }

    public function apply(Attendance $attendance): array
    {
        if ($this->monthly->activePassFor($attendance->student, $attendance->date) !== null) {
            return $this->monthly->applyForAttendance($attendance);
        }

        return $this->legacy->apply($attendance);
    }
}