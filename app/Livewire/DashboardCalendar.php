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

    public ?int $selectedGroupId = null;

    public ?int $selectedEventId = null;

    public function mount(?int $teacherId = null)
    {
        $this->teacherId = $teacherId;
    }

    public function openGroup(int $groupId)
    {
        $this->selectedGroupId = $groupId;
        $this->selectedEventId = null;
    }

    public function openEvent(int $eventId)
    {
        $this->selectedEventId = $eventId;
        $this->selectedGroupId = null;
    }

    public function closeModal()
    {
        $this->selectedGroupId = null;
        $this->selectedEventId = null;
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
                ->activeForWeek($monday, $sunday)
                ->orderBy('name')
                ->get();

            foreach ($groups as $group) {
                $classTimes = $group->class_times ?? [];
                foreach ($classTimes as $time) {
                    $dayNum = (int) ($time['day'] ?? 0);
                    if ($dayNum < 1 || $dayNum > 7) continue;
                    $combined[$dayNum - 1][] = [
                        'type' => 'group',
                        'id' => $group->id,
                        'title' => $group->name,
                        'subtitle' => ($time['start'] ?? '') . (isset($time['end']) && $time['end'] ? ' - ' . $time['end'] : ''),
                        'start' => $time['start'] ?? '00:00',
                        'color' => 'bg-blue-100 text-blue-800',
                        'route' => route('admin.groups.edit', $group),
                    ];
                }
            }
        }

        if ($this->showEvents) {
            $events = Event::with(['creator', 'teacher'])
                ->whereBetween('date', [$monday->format('Y-m-d'), $sunday->format('Y-m-d')])
                ->when($this->teacherId, fn ($q) => $q->where('teacher_id', $this->teacherId))
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();

            foreach ($events as $event) {
                $dateKey = $event->date->format('Y-m-d');
                $dayIndex = (int) $monday->diffInDays($event->date);
                if ($dayIndex < 0 || $dayIndex > 6) continue;
                $combined[$dayIndex][] = [
                    'type' => 'event',
                    'id' => $event->id,
                    'title' => $event->name,
                    'subtitle' => \Carbon\Carbon::parse($event->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($event->end_time)->format('H:i'),
                    'start' => \Carbon\Carbon::parse($event->start_time)->format('H:i'),
                    'color' => 'bg-emerald-100 text-emerald-800',
                    'route' => route('admin.events.edit', $event),
                ];
            }
        }

        foreach ($combined as &$daySlots) {
            usort($daySlots, fn ($a, $b) => strcmp($a['start'] ?? '', $b['start'] ?? ''));
        }
        unset($daySlots);

        $weekLabel = $monday->format('j') . ' ' . $monday->translatedFormat('F') . ' - '
            . $sunday->format('j') . ' ' . $sunday->translatedFormat('F Y');

        $modalTitle = null;
        $modalSubtitle = null;
        $modalStudents = collect();
        $modalEditRoute = null;
        $modalAssignRoute = null;
        $modalPassStatus = [];
        $modalEventId = null;
        $modalBuyPassRoute = null;
        $modalPassTypes = collect();
        if ($this->selectedGroupId) {
            $group = DanceGroup::with(['category', 'teacher', 'students'])->find($this->selectedGroupId);
            $modalTitle = $group?->name;
            $modalSubtitle = $group?->category?->name;
            $modalStudents = $group?->students ?? collect();
            $modalEditRoute = $group ? route('admin.groups.edit', $group) : null;
            $modalAssignRoute = $group
                ? ($this->teacherId ? route('teacher.assign', $group) : route('admin.groups.assign', $group))
                : null;
        } elseif ($this->selectedEventId) {
            $event = Event::with(['creator', 'students'])->find($this->selectedEventId);
            $modalTitle = $event?->name;
            $modalSubtitle = $event ? \Carbon\Carbon::parse($event->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($event->end_time)->format('H:i') : null;
            $modalStudents = $event?->students ?? collect();
            $modalEditRoute = $event ? route('admin.events.edit', $event) : null;
            $modalAssignRoute = $event
                ? ($this->teacherId ? route('teacher.events.assign', $event) : route('admin.events.assign', $event))
                : null;
            $modalEventId = $event?->id;
            $modalPassStatus = $event?->passStatusByStudent() ?? [];
            $modalBuyPassRoute = $event
                ? ($this->teacherId ? route('teacher.events.buy-pass') : route('admin.events.buy-pass'))
                : null;
            $modalPassTypes = $event ? \App\Models\PassType::where('type', 'single')->orderBy('price')->get() : collect();
        }

        return view('livewire.dashboard-calendar', compact('days', 'combined', 'weekLabel', 'modalTitle', 'modalSubtitle', 'modalStudents', 'modalEditRoute', 'modalAssignRoute', 'modalPassStatus', 'modalEventId', 'modalBuyPassRoute', 'modalPassTypes'));
    }
}
