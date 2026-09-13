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

    public array $selected = [];

    public $flashMessage = '';

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

    public function toggleSelectAllOnPage()
    {
        $ids = $this->pagePaymentIds();

        if ($ids === []) {
            return;
        }

        $allSelected = count(array_diff($ids, $this->selected)) === 0;

        $this->selected = $allSelected
            ? array_values(array_diff($this->selected, $ids))
            : array_values(array_unique(array_merge($this->selected, $ids)));
    }

    public function deleteSelected()
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $this->selected))));

        if ($ids === []) {
            return;
        }

        $deleted = Payment::whereIn('id', $ids)->delete();

        $this->selected = [];
        $this->flashMessage = __('Deleted :count payment(s).', ['count' => $deleted]);
    }

    private function paymentQuery()
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

        return $query;
    }

    private function pagePaymentIds(): array
    {
        $payments = $this->paymentQuery()->latest()->paginate(15);

        return $payments->items() ? collect($payments->items())->pluck('id')->all() : [];
    }

    public function render()
    {
        $payments = $this->paymentQuery()->latest()->paginate(15);
        $groups = DanceGroup::with('category')->orderBy('name')->get();

        $pageIds = $payments->items() ? collect($payments->items())->pluck('id')->all() : [];
        $selectedCount = count($this->selected);
        $allOnPageSelected = $pageIds !== [] && count(array_diff($pageIds, $this->selected)) === 0;

        return view('livewire.payment-search', compact('payments', 'groups', 'pageIds', 'selectedCount', 'allOnPageSelected'));
    }
}
