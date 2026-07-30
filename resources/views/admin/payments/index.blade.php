@extends('layouts.app')
@section('title', __('Manage Payments'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Payments') }}</h1>
        <a href="{{ route('admin.payments.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('+ Record Payment') }}</a>
    </div>

    <livewire:payment-search />
</div>
@endsection
