<?php

namespace App\Livewire;

use App\Models\DanceGroup;
use Livewire\Component;
use Livewire\WithPagination;

class GroupSearch extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $groups = DanceGroup::with(['category', 'teacher', 'students'])
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate(10);

        return view('livewire.group-search', compact('groups'));
    }
}
