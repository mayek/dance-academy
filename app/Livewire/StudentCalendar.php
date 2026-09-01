<?php

namespace App\Livewire;

use App\Models\DanceGroup;
use App\Models\Event;
use Livewire\Component;

class StudentCalendar extends Component
{
    public int $weekOffset = 0;

    public ?int $selectedGroupId = null;

    public ?int $selectedEventId = null;

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
        $user = auth()->user();
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

        $groups = $user->enrolledGroups()
            ->with(['category', 'teacher'])
            ->whereNotNull('class_times')
            ->activeForWeek($monday, $sunday)
            ->get();

        foreach ($groups as $group) {
            foreach ($group->class_times ?? [] as $time) {
                $dayNum = (int) ($time['day'] ?? 0);
                if ($dayNum < 1 || $dayNum > 7) continue;
                $combined[$dayNum - 1][] = [
                    'type' => 'group',
                    'id' => $group->id,
                    'title' => $group->name,
                    'subtitle' => ($time['start'] ?? '') . (!empty($time['end']) ? ' - ' . $time['end'] : ''),
                    'start' => $time['start'] ?? '00:00',
                    'color' => 'bg-blue-100 text-blue-800',
                ];
            }
        }

        $events = Event::with(['creator', 'teacher'])
            ->whereHas('students', fn ($q) => $q->where('users.id', $user->id))
            ->whereBetween('date', [$monday->format('Y-m-d'), $sunday->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        foreach ($events as $event) {
            $dayIndex = (int) $monday->diffInDays($event->date);
            if ($dayIndex < 0 || $dayIndex > 6) continue;
            $combined[$dayIndex][] = [
                'type' => 'event',
                'id' => $event->id,
                'title' => $event->name,
                'subtitle' => \Carbon\Carbon::parse($event->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($event->end_time)->format('H:i'),
                'start' => \Carbon\Carbon::parse($event->start_time)->format('H:i'),
                'color' => 'bg-emerald-100 text-emerald-800',
            ];
        }

        foreach ($combined as &$daySlots) {
            usort($daySlots, fn ($a, $b) => strcmp($a['start'] ?? '', $b['start'] ?? ''));
        }
        unset($daySlots);

        $weekLabel = $monday->format('j') . ' ' . $monday->translatedFormat('F') . ' - '
            . $sunday->format('j') . ' ' . $sunday->translatedFormat('F Y');

        $modal = null;
        if ($this->selectedGroupId) {
            $group = DanceGroup::with(['category', 'teacher'])->find($this->selectedGroupId);
            if ($group) {
                $modal = [
                    'title' => $group->name,
                    'subtitle' => $group->category?->name,
                    'lines' => [
                        __('Teacher') . ': ' . ($group->teacher?->name ?? '-'),
                        $group->schedule ? __('Schedule') . ': ' . $group->schedule : null,
                    ],
                ];
            }
        } elseif ($this->selectedEventId) {
            $event = Event::with(['creator', 'teacher'])->find($this->selectedEventId);
            if ($event) {
                $modal = [
                    'title' => $event->name,
                    'subtitle' => $event->date->format('Y-m-d') . ' '
                        . \Carbon\Carbon::parse($event->start_time)->format('H:i') . ' - '
                        . \Carbon\Carbon::parse($event->end_time)->format('H:i'),
                    'lines' => [
                        $event->teacher ? __('Instructor') . ': ' . $event->teacher->name : null,
                    ],
                ];
            }
        }

        return view('livewire.student-calendar', compact('days', 'combined', 'weekLabel', 'modal'));
    }
}
