<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventCalendar extends Component
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

        $query = Event::with(['creator', 'students'])
            ->whereBetween('date', [$monday->format('Y-m-d'), $sunday->format('Y-m-d')])
            ->when($this->teacherId, fn ($q) => $q->where('created_by', $this->teacherId))
            ->orderBy('date')
            ->orderBy('start_time');

        $events = $query->get();

        $grouped = [];
        foreach ($events as $event) {
            $dateKey = $event->date->format('Y-m-d');
            $grouped[$dateKey][] = $event;
        }

        $weekLabel = $monday->format('j') . ' ' . $monday->translatedFormat('F') . ' - '
            . $sunday->format('j') . ' ' . $sunday->translatedFormat('F Y');

        return view('livewire.event-calendar', compact('days', 'grouped', 'weekLabel'));
    }
}
