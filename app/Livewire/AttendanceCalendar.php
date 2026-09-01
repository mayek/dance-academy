<?php

namespace App\Livewire;

use App\Models\Attendance;
use Livewire\Component;

class AttendanceCalendar extends Component
{
    public int $weekOffset = 0;

    public ?int $groupId = null;

    public function mount(?int $groupId = null)
    {
        $this->groupId = $groupId;
    }

    public function previousWeek()
    {
        $this->weekOffset--;
    }

    public function nextWeek()
    {
        $this->weekOffset++;
    }

    public function goToCurrent()
    {
        $this->weekOffset = 0;
    }

    public function render()
    {
        $monday = now()->startOfWeek()->addWeeks($this->weekOffset);
        $sunday = $monday->copy()->addDays(6);

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $monday->copy()->addDays($i);
            $days[] = [
                'label' => $date->isoFormat('dddd'),
                'date' => $date->format('Y-m-d'),
                'dayNumber' => $date->format('j'),
                'month' => $date->format('F'),
                'isToday' => $date->isToday(),
            ];
        }

        $absences = collect();
        if ($this->groupId) {
            $absences = Attendance::with(['student'])
                ->where('dance_group_id', $this->groupId)
                ->where('status', 'absent')
                ->whereBetween('date', [$monday->format('Y-m-d'), $sunday->format('Y-m-d')])
                ->orderBy('date')
                ->get()
                ->groupBy(fn ($attendance) => $attendance->date->format('Y-m-d'));
        }

        $weekLabel = $monday->format('j') . ' ' . $monday->translatedFormat('F') . ' - '
            . $sunday->format('j') . ' ' . $sunday->translatedFormat('F Y');

        return view('livewire.attendance-calendar', compact('days', 'absences', 'weekLabel'));
    }
}
