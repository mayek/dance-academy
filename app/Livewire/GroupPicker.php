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
        $groups = DanceGroup::with('category')
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->limit(30)
            ->get();

        return view('livewire.group-picker', compact('groups'));
    }
}
