<?php

namespace App\Livewire;

use App\Models\DanceGroup;
use Livewire\Component;

class GroupCalendar extends Component
{
    public int $weekOffset = 0;

    public ?int $teacherId = null;

    public function mount(?int $teacherId = null)
    {
        $this->teacherId = $teacherId;
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

        $groups = DanceGroup::with(['category', 'teacher'])
            ->whereNotNull('class_times')
            ->when($this->teacherId, fn ($q) => $q->where('teacher_id', $this->teacherId))
            ->orderBy('name')
            ->get();

        $schedule = [];
        foreach ($groups as $group) {
            $classTimes = $group->class_times ?? [];
            foreach ($classTimes as $time) {
                $dayNum = (int) ($time['day'] ?? 0);
                $start = $time['start'] ?? '';
                $end = $time['end'] ?? '';
                $schedule[$dayNum][] = [
                    'group' => $group,
                    'start' => $start,
                    'end' => $end,
                ];
            }
        }

        $weekLabel = $monday->format('j') . ' ' . $monday->translatedFormat('F') . ' - '
            . $monday->copy()->addDays(6)->format('j') . ' ' . $monday->copy()->addDays(6)->translatedFormat('F Y');

        return view('livewire.group-calendar', compact('days', 'schedule', 'weekLabel'));
    }
}
