@extends('layouts.app')
@section('title', __('Admin Dashboard'))

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Admin Dashboard') }}</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Teachers') }}</div>
            <div class="mt-1 text-3xl font-bold text-purple-600">{{ $stats['teachers'] }}</div>
            <a href="{{ route('admin.teachers.index') }}" class="mt-3 inline-block text-sm text-purple-600 hover:text-purple-800">{{ __('Manage') }} &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Students') }}</div>
            <div class="mt-1 text-3xl font-bold text-blue-600">{{ $stats['students'] }}</div>
            <a href="{{ route('admin.students.index') }}" class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-800">{{ __('Manage') }} &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Categories') }}</div>
            <div class="mt-1 text-3xl font-bold text-green-600">{{ $stats['categories'] }}</div>
            <a href="{{ route('admin.categories.index') }}" class="mt-3 inline-block text-sm text-green-600 hover:text-green-800">{{ __('Manage') }} &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Dance Groups') }}</div>
            <div class="mt-1 text-3xl font-bold text-orange-600">{{ $stats['groups'] }}</div>
            <a href="{{ route('admin.groups.index') }}" class="mt-3 inline-block text-sm text-orange-600 hover:text-orange-800">{{ __('Manage') }} &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Active Passes') }}</div>
            <div class="mt-1 text-3xl font-bold text-emerald-600">{{ $stats['active_passes'] }}</div>
            <a href="{{ route('admin.payments.index') }}" class="mt-3 inline-block text-sm text-emerald-600 hover:text-emerald-800">{{ __('Manage') }} &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Monthly Revenue') }}</div>
            <div class="mt-1 text-3xl font-bold text-indigo-600">{{ number_format($stats['monthly_revenue'], 2) }} &zloty;</div>
            <a href="{{ route('admin.payments.index') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800">{{ __('Manage') }} &rarr;</a>
        </div>
    </div>

    @if($expiringPasses->isNotEmpty())
    <div class="mt-8">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse"></div>
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Expiring Passes (next 7 days)') }}</h2>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ $expiringPasses->count() }}</span>
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-amber-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Student') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Group') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Pass Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Expires') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Days Left') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($expiringPasses as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->student->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->danceGroup->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($payment->isMonthly())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ __('Monthly') }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">{{ __('Single') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->valid_until->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php $days = (int) now()->diffInDays($payment->valid_until, false); @endphp
                            @if($days <= 1)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ $days }} {{ __('day') }}</span>
                            @elseif($days <= 3)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">{{ $days }} {{ __('days') }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">{{ $days }} {{ __('days') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
