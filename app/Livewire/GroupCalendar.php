<?php

namespace App\Livewire;

use App\Models\DanceGroup;
use Livewire\Component;

class GroupCalendar extends Component
{
    public int $weekOffset = 0;

    public ?int $teacherId = null;

    public ?int $selectedGroupId = null;

    public function mount(?int $teacherId = null)
    {
        $this->teacherId = $teacherId;
    }

    public function openGroup(int $groupId)
    {
        $this->selectedGroupId = $groupId;
    }

    public function closeModal()
    {
        $this->selectedGroupId = null;
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

        $groups = DanceGroup::with(['category', 'teacher'])
            ->whereNotNull('class_times')
            ->when($this->teacherId, fn ($q) => $q->where('teacher_id', $this->teacherId))
            ->activeForWeek($monday, $sunday)
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

        foreach ($schedule as &$daySlots) {
            usort($daySlots, fn ($a, $b) => strcmp($a['start'], $b['start']));
        }
        unset($daySlots);

        $weekLabel = $monday->format('j') . ' ' . $monday->translatedFormat('F') . ' - '
            . $monday->copy()->addDays(6)->format('j') . ' ' . $monday->copy()->addDays(6)->translatedFormat('F Y');

        $modalTitle = null;
        $modalSubtitle = null;
        $modalStudents = collect();
        $modalEditRoute = null;
        $modalAssignRoute = null;
        if ($this->selectedGroupId) {
            $group = DanceGroup::with(['category', 'teacher', 'students'])->find($this->selectedGroupId);
            $modalTitle = $group?->name;
            $modalSubtitle = $group?->category?->name;
            $modalStudents = $group?->students ?? collect();
            $modalEditRoute = $group ? route('admin.groups.edit', $group) : null;
            $modalAssignRoute = $group ? route('admin.groups.assign', $group) : null;
        }

        return view('livewire.group-calendar', compact('days', 'schedule', 'weekLabel', 'modalTitle', 'modalSubtitle', 'modalStudents', 'modalEditRoute', 'modalAssignRoute'));
    }
}
