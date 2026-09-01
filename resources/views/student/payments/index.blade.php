@extends('layouts.app')
@section('title', __('My Passes'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('My Passes') }}</h1>
        <button disabled class="bg-gray-300 text-gray-500 text-sm font-medium py-2 px-4 rounded-lg cursor-not-allowed">{{ __('Kup karnet (wkrótce)') }}</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Active Passes') }}</div>
            <div class="mt-1 text-3xl font-bold text-green-600">{{ $payments->filter(fn ($p) => $p->isActive())->count() }}</div>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Total Spent') }}</div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($payments->sum('amount'), 2) }} zł</div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Group') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Pass Type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Valid From') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Valid Until') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Payment') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payments as $payment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        @if($payment->event)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 mr-1">{{ __('Event') }}</span>
                            <span class="text-gray-900 font-medium">{{ $payment->event->name }}</span>
                        @elseif($payment->danceGroup)
                            {{ $payment->danceGroup->name }}
                            <span class="text-xs text-gray-400 ml-1">({{ $payment->danceGroup->category->name }})</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($payment->isMonthly())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ __('Monthly') }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">{{ __('Single') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($payment->amount, 2) }} zł</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->valid_from->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->valid_until?->format('d.m.Y') ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($payment->isActive())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">{{ __('Active') }}</span>
                        @elseif($payment->status === 'cancelled')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ __('Cancelled') }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ __('Expired') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($payment->is_paid)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">{{ __('Paid') }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ __('Unpaid') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No passes yet. Buy your first pass!') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>
</div>
@endsection
