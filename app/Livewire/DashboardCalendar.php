<?php

namespace App\Livewire;

use App\Models\DanceGroup;
use App\Models\Event;
use Livewire\Component;

class DashboardCalendar extends Component
{
    public int $weekOffset = 0;
    public ?int $teacherId = null;
    public bool $showGroups = true;
    public bool $showEvents = true;

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

        $combined = array_fill(0, 7, []);

        if ($this->showGroups) {
            $groups = DanceGroup::with(['category', 'teacher'])
                ->whereNotNull('class_times')
                ->when($this->teacherId, fn ($q) => $q->where('teacher_id', $this->teacherId))
                ->orderBy('name')
                ->get();

            foreach ($groups as $group) {
                $classTimes = $group->class_times ?? [];
                foreach ($classTimes as $time) {
                    $dayNum = (int) ($time['day'] ?? 0);
                    if ($dayNum < 1 || $dayNum > 7) continue;
                    $combined[$dayNum - 1][] = [
                        'type' => 'group',
                        'title' => $group->name,
                        'subtitle' => ($time['start'] ?? '') . (isset($time['end']) && $time['end'] ? ' - ' . $time['end'] : ''),
                        'color' => 'bg-blue-100 text-blue-800',
                        'route' => route('admin.groups.edit', $group),
                    ];
                }
            }
        }

        if ($this->showEvents) {
            $events = Event::with(['creator'])
                ->whereBetween('date', [$monday->format('Y-m-d'), $sunday->format('Y-m-d')])
                ->when($this->teacherId, fn ($q) => $q->where('created_by', $this->teacherId))
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();

            foreach ($events as $event) {
                $dateKey = $event->date->format('Y-m-d');
                $dayIndex = (int) $monday->diffInDays($event->date);
                if ($dayIndex < 0 || $dayIndex > 6) continue;
                $combined[$dayIndex][] = [
                    'type' => 'event',
                    'title' => $event->name,
                    'subtitle' => \Carbon\Carbon::parse($event->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($event->end_time)->format('H:i'),
                    'color' => 'bg-emerald-100 text-emerald-800',
                    'route' => route('admin.events.edit', $event),
                ];
            }
        }

        $weekLabel = $monday->format('j') . ' ' . $monday->translatedFormat('F') . ' - '
            . $sunday->format('j') . ' ' . $sunday->translatedFormat('F Y');

        return view('livewire.dashboard-calendar', compact('days', 'combined', 'weekLabel'));
    }
}
