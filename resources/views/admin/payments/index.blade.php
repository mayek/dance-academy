@extends('layouts.app')
@section('title', __('Manage Payments'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Payments') }}</h1>
        <a href="{{ route('admin.payments.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('+ Record Payment') }}</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Revenue') }}</h2>
        <div class="relative" style="height: 280px;">
            <canvas id="revenueChart"
                    data-labels='{{ json_encode($labels) }}'
                    data-revenue='{{ json_encode($revenue) }}'
                    data-label="{{ __('Revenue') }}"></canvas>
        </div>
    </div>

    <livewire:payment-search />
</div>
@endsection
