<div>
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label for="student_search" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Student') }}</label>
                <input type="text" id="student_search" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or email...') }}"
                       class="w-full rounded-md border-gray-300 text-sm border p-2">
            </div>
            <div>
                <label for="dance_group_id" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Group') }}</label>
                <select name="dance_group_id" id="dance_group_id" wire:model.live="dance_group_id" class="w-full rounded-md border-gray-300 text-sm border p-2">
                    <option value="">{{ __('All groups') }}</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="pass_type" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Pass Type') }}</label>
                <select name="pass_type" id="pass_type" wire:model.live="pass_type" class="w-full rounded-md border-gray-300 text-sm border p-2">
                    <option value="">{{ __('All types') }}</option>
                    <option value="monthly">{{ __('Monthly') }}</option>
                    <option value="single">{{ __('Single Class') }}</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Status') }}</label>
                <select name="status" id="status" wire:model.live="status" class="w-full rounded-md border-gray-300 text-sm border p-2">
                    <option value="">{{ __('All statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="expired">{{ __('Expired') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                </select>
            </div>
            <div>
                <label for="is_paid" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Payment') }}</label>
                <select name="is_paid" id="is_paid" wire:model.live="is_paid" class="w-full rounded-md border-gray-300 text-sm border p-2">
                    <option value="">{{ __('All') }}</option>
                    <option value="1">{{ __('Paid') }}</option>
                    <option value="0">{{ __('Unpaid') }}</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="button" wire:click="$set('search', ''); $set('dance_group_id', ''); $set('pass_type', ''); $set('status', ''); $set('is_paid', '')" class="w-full bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('Clear') }}</button>
            </div>
        </div>
    </div>

    @if($flashMessage)
        <div class="mb-4 bg-emerald-50 text-emerald-800 text-sm p-3 rounded-lg">{{ $flashMessage }}</div>
    @endif

    @if($selectedCount > 0)
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3 bg-white border border-blue-200 rounded-lg p-4 shadow-sm">
            <span class="text-sm font-medium text-gray-800">{{ __(':count payment(s) selected.', ['count' => $selectedCount]) }}</span>
            <button type="button" wire:click="deleteSelected"
                    wire:confirm="{{ __('Delete the selected payments?') }}"
                    class="inline-flex items-center gap-2 shrink-0 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 px-5 rounded-md shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                {{ __('Delete selected') }}
            </button>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-px">
                        <input type="checkbox" wire:key="select-all-{{ $allOnPageSelected ? 'checked' : 'unchecked' }}"
                       wire:click="toggleSelectAllOnPage" @checked($allOnPageSelected)
                       title="{{ __('Select all') }}"
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Student') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Made by') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Group') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Pass Type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Hours') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Valid From') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payments as $payment)
                <tr class="{{ $payment->is_paid ? 'bg-green-50' : 'bg-red-50' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" value="{{ $payment->id }}" wire:model.live="selected"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->student->full_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->recordedBy?->full_name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($payment->event)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 mr-1">{{ __('Event') }}</span>
                            <span class="text-gray-900 font-medium">{{ $payment->event->name }}</span>
                        @elseif($payment->danceGroup)
                            {{ $payment->danceGroup->name }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($payment->passType)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ $payment->passType->display_name }}</span>
                        @elseif($payment->isMonthly())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ __('Monthly') }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">{{ __('Single') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($payment->amount, 2) }} zł</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($payment->total_hours !== null)
                            @php $remainingHours = $payment->remainingHours(); @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $remainingHours > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $remainingHours }} / {{ $payment->total_hours }}h
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->valid_from->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center space-x-3">
                            <span title="@if($payment->status === 'active'){{ __('Aktywny') }}@elseif($payment->status === 'expired'){{ __('Wygasły') }}@else{{ __('Anulowany') }}@endif"
                                class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                @if($payment->status === 'active') bg-green-100 text-green-700
                                @elseif($payment->status === 'expired') bg-gray-100 text-gray-500
                                @else bg-red-100 text-red-700 @endif">
                                @if($payment->status === 'active')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @elseif($payment->status === 'expired')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                @endif
                            </span>
                            @if($payment->is_paid)
                                <span title="{{ __('Opłacony') }}" class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                            @else
                                <span title="{{ __('Nieopłacony') }}" class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                        <a href="{{ route('admin.payments.edit', $payment) }}" class="text-emerald-600 hover:text-emerald-800 inline-flex align-middle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 inline-flex align-middle">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No payments found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>
</div>
