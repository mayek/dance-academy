<?php

namespace App\Services;

use App\Models\ClassSession;
use App\Models\DanceGroup;
use Carbon\Carbon;

class ScheduleService
{
    public function syncMonth(Carbon $month, ?int $groupId = null): int
    {
        $count = 0;
        $from = $month->copy()->startOfMonth()->startOfDay();
        $to = $month->copy()->endOfMonth()->endOfDay();

        $groups = DanceGroup::with('sessions')
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $to->toDateString()))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $from->toDateString()))
            ->when($groupId, fn ($q) => $q->where('id', $groupId))
            ->get();

        foreach ($groups as $group) {
            foreach ($this->plannedSlotsFor($group, $from, $to) as $date => $slot) {
                ClassSession::firstOrCreate(
                    ['dance_group_id' => $group->id, 'date' => $date],
                    [
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end'],
                        'room' => $group->room,
                        'status' => 'planned',
                    ]
                );
                $count++;
            }
        }

        return $count;
    }

    public function hoursForGroup(DanceGroup $group, Carbon $from, Carbon $to): float
    {
        $slots = $this->plannedSlotsFor($group, $from, $to);

        return round(array_sum(array_map(fn ($s) => $s['hours'], $slots)), 2);
    }

    public function hoursForGroupOnDate(DanceGroup $group, Carbon $date): ?float
    {
        $slot = $this->slotForDate($group, $date);

        return $slot !== null ? $slot['hours'] : null;
    }

    public function sessionForGroupOnDate(DanceGroup $group, Carbon $date): ?ClassSession
    {
        if ($date->lt(now()->subDays(30))) {
            return null;
        }

        $session = ClassSession::where('dance_group_id', $group->id)
            ->whereDate('date', $date)
            ->first();

        if ($session && !$session->isCancelled()) {
            return $session;
        }

        return null;
    }

    public function durationFor(ClassSession $session): float
    {
        return $session->durationHours();
    }

    private function plannedSlotsFor(DanceGroup $group, Carbon $from, Carbon $to): array
    {
        $slots = [];

        $classTimes = $group->class_times ?? [];

        foreach ($classTimes as $time) {
            $day = (int) ($time['day'] ?? 0);
            if ($day < 1 || $day > 7 || empty($time['start']) || empty($time['end'])) {
                continue;
            }

            $start = Carbon::parse($time['start']);
            $end = Carbon::parse($time['end']);

            for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
                $dow = $date->dayOfWeek === Carbon::SUNDAY ? 7 : $date->dayOfWeek;

                if ($dow !== $day) {
                    continue;
                }

                if ($group->start_date && $date->lt($group->start_date)) {
                    continue;
                }

                if ($group->end_date && $date->gt($group->end_date)) {
                    continue;
                }

                $hours = $end->gt($start) ? round($end->diffInMinutes($start, true) / 60, 2) : 1.0;

                $slots[$date->format('Y-m-d')] = [
                    'start' => $time['start'],
                    'end' => $time['end'],
                    'hours' => $hours,
                ];
            }
        }

        ksort($slots);

        return $slots;
    }

    private function slotForDate(DanceGroup $group, Carbon $date): ?array
    {
        $day = $date->dayOfWeek === Carbon::SUNDAY ? 7 : $date->dayOfWeek;

        foreach ($group->class_times ?? [] as $time) {
            if ((int) ($time['day'] ?? 0) === $day && !empty($time['start']) && !empty($time['end'])) {
                $start = Carbon::parse($time['start']);
                $end = Carbon::parse($time['end']);

                return [
                    'start' => $time['start'],
                    'end' => $time['end'],
                    'hours' => $end->gt($start) ? round($end->diffInMinutes($start, true) / 60, 2) : 1.0,
                ];
            }
        }

        return null;
    }
}