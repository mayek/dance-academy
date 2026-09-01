<?php

namespace App\Livewire;

use App\Models\DanceGroup;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $dance_group_id = '';
    public $pass_type = '';
    public $status = '';
    public $is_paid = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDanceGroupId()
    {
        $this->resetPage();
    }

    public function updatingPassType()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingIsPaid()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Payment::with(['student', 'danceGroup.category', 'recordedBy', 'passType', 'event']);

        if ($this->search) {
            $search = $this->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($this->dance_group_id) {
            $query->where('dance_group_id', $this->dance_group_id);
        }

        if ($this->pass_type) {
            $query->where('pass_type', $this->pass_type);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->is_paid !== '') {
            $query->where('is_paid', (bool) $this->is_paid);
        }

        $payments = $query->latest()->paginate(15);
        $groups = DanceGroup::with('category')->orderBy('name')->get();

        return view('livewire.payment-search', compact('payments', 'groups'));
    }
}
