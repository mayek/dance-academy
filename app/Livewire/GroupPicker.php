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
        $selected = array_values(array_filter($this->selected, fn ($id) => is_numeric($id)));

        $groups = collect();

        if (!empty($selected)) {
            $groups = DanceGroup::with('category')
                ->whereIn('id', $selected)
                ->orderBy('name')
                ->get();
        }

        $excluded = $groups->pluck('id')->all();

        $others = DanceGroup::with('category')
            ->whereNotIn('id', $excluded)
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->limit(max(0, 30 - $groups->count()))
            ->get();

        $groups = $groups->concat($others);

        return view('livewire.group-picker', compact('groups'));
    }
}
