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
    </div>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Group') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Pass Type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Amount') }}</th>
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
                <tr><td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No payments found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>
</div>
@endsection
