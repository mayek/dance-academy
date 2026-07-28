@extends('layouts.app')
@section('title', 'My Passes')

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Passes</h1>
        <a href="{{ route('student.payments.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 px-4 rounded-lg">+ Buy Pass</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Active Passes</div>
            <div class="mt-1 text-3xl font-bold text-green-600">{{ $payments->where('status', 'active')->where('valid_until', '>=', now())->count() }}</div>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Total Spent</div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($payments->sum('amount'), 2) }} &zloty;</div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Group</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pass Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valid From</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valid Until</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payments as $payment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $payment->danceGroup->name }}
                        <span class="text-xs text-gray-400 ml-1">({{ $payment->danceGroup->category->name }})</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($payment->isMonthly())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Monthly</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">Single</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($payment->amount, 2) }} &zloty;</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->valid_from->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->valid_until?->format('d.m.Y') ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($payment->status === 'active' && $payment->valid_until?->isFuture())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @elseif($payment->status === 'cancelled')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Expired</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No passes yet. Buy your first pass!</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>
</div>
@endsection
