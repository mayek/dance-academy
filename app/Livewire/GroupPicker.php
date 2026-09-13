<?php

namespace App\Livewire;

use App\Models\DanceGroup;
use Livewire\Component;

class GroupPicker extends Component
{
    public $search = '';

    public array $selected = [];

    public function mount(array $selected = [])
    {
        $this->selected = $selected;
    }

    public function render()
    {
        $selected = $this->selected;

        $groups = DanceGroup::with('category')
            ->where(function ($query) use ($selected) {
                $query->whereIn('id', $selected)->orWhere(function ($other) use ($selected) {
                    $other->whereNotIn('id', $selected)
                        ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderBy('name')
            ->limit(30)
            ->get();

        return view('livewire.group-picker', compact('groups'));
    }
}
