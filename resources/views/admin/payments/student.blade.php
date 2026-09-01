@extends('layouts.app')
@section('title', $student->full_name . ' - ' . __('Payments'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.students.index') }}" class="text-gray-400 hover:text-gray-600">&larr; {{ __('Back') }}</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $student->full_name }} &mdash; {{ __('Payments') }}</h1>
            <p class="text-sm text-gray-500">{{ __('Total payments:') }} <span class="font-semibold text-gray-900">{{ $payments->total() }}</span></p>
        </div>
        <div class="ml-auto">
            <button type="button" id="open_buy_pass_modal"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 px-4 rounded-lg cursor-pointer">
                {{ __('+ Record Payment') }}
            </button>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Group') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Pass Type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Hours') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Recorded By') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Valid From') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Valid Until') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payments as $payment)
                <tr class="{{ $payment->is_paid ? 'bg-green-50' : 'bg-red-50' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->recordedBy?->full_name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->valid_from->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->valid_until?->format('d.m.Y') ?? '-' }}</td>
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
                        <a href="{{ route('admin.payments.edit', $payment) }}" class="text-emerald-600 hover:text-emerald-800 inline-flex align-middle" title="{{ __('Edit') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No payments found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>
</div>

<div id="buy_pass_modal" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-screen p-4 sm:p-6">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" data-close-buy-pass-modal></div>
        <div class="relative w-full max-w-2xl max-h-[85vh] flex flex-col bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-base font-semibold text-gray-900">{{ __('Record Payment') }} &mdash; {{ $student->full_name }}</h3>
                <button type="button" data-close-buy-pass-modal
                        class="flex-shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition cursor-pointer"
                        aria-label="{{ __('Close') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4 flex-1 overflow-y-auto">
                <form method="POST" action="{{ route('admin.payments.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <div>
                        <label for="buy_pass_group_id" class="block text-sm font-medium text-gray-700">{{ __('Dance Group') }}</label>
                        <select name="dance_group_id" id="buy_pass_group_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ \Illuminate\Support\Str::limit($group->name, 60) }} ({{ $group->category->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="buy_pass_pass_type_id" class="block text-sm font-medium text-gray-700">{{ __('Pass Type') }}</label>
                        <select name="pass_type_id" id="buy_pass_pass_type_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                            @foreach($passTypes as $passType)
                                <option value="{{ $passType->id }}" data-hours="{{ $passType->hours ?? '' }}">{{ $passType->display_name }} ({{ number_format($passType->price, 2) }} zł)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="buy_pass_hours" class="block text-sm font-medium text-gray-700">{{ __('Hours') }}</label>
                        <input type="number" name="total_hours" id="buy_pass_hours" step="0.5" min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    </div>
                    <div>
                        <label for="buy_pass_valid_from" class="block text-sm font-medium text-gray-700">{{ __('Valid From') }}</label>
                        <input type="date" name="valid_from" id="buy_pass_valid_from"
                               value="{{ now()->format('Y-m-d') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    </div>
                    <div>
                        <label for="buy_pass_is_paid" class="block text-sm font-medium text-gray-700">{{ __('Payment') }}</label>
                        <select name="is_paid" id="buy_pass_is_paid"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                            <option value="1">{{ __('Paid') }}</option>
                            <option value="0">{{ __('Unpaid') }}</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="buy_pass_notes" class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
                        <input type="text" name="notes" id="buy_pass_notes"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    </div>
                    <div class="md:col-span-2 flex justify-end gap-2 mt-2">
                        <button type="button" data-close-buy-pass-modal
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg cursor-pointer">{{ __('Cancel') }}</button>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 px-4 rounded-lg cursor-pointer">{{ __('Record Payment') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('buy_pass_modal');
    const openBtn = document.getElementById('open_buy_pass_modal');
    const passType = document.getElementById('buy_pass_pass_type_id');
    const hoursField = document.getElementById('buy_pass_hours');

    if (!modal || !openBtn) return;

    function toggleModal(open) {
        modal.classList.toggle('hidden', !open);
        if (open && passType) {
            const opt = passType.selectedOptions[0];
            hoursField.value = opt && opt.dataset.hours ? opt.dataset.hours : '';
        }
    }

    openBtn.addEventListener('click', function () { toggleModal(true); });

    modal.querySelectorAll('[data-close-buy-pass-modal]').forEach(function (el) {
        el.addEventListener('click', function () { toggleModal(false); });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) toggleModal(false);
    });

    if (passType) {
        passType.addEventListener('change', function () {
            const opt = passType.selectedOptions[0];
            hoursField.value = opt && opt.dataset.hours ? opt.dataset.hours : '';
        });
    }
});
</script>
@endsection
