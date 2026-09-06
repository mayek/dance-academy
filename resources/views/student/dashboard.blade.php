@extends('layouts.app')
@section('title', __('Student Dashboard'))

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('My Schedule') }}</h1>
    <livewire:student-calendar />

    <h1 class="text-2xl font-bold text-gray-900 mb-6 mt-8">{{ __('Mój karnet miesięczny') }}</h1>

    <div class="bg-white shadow rounded-lg p-6 mb-4 border-2 {{ $currentMonthPass && $currentMonthPass->is_paid ? 'border-green-300' : 'border-red-300' }}">
        @if($currentMonthPass)
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-lg font-semibold text-gray-900 mr-2">{{ __('Karnet') }} {{ $currentMonthPass->valid_from->translatedFormat('F Y') }}</h2>
                @if($currentMonthPass->is_paid)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ __('Opłacony') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ __('Nieopłacony') }}
                    </span>
                @endif
                @if($currentMonthPass->total_hours !== null)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $currentMonthPass->remainingHours() > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ __('Godziny') }}: {{ $currentMonthPass->remainingHours() }} / {{ $currentMonthPass->total_hours }}h
                    </span>
                @endif
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                    {{ __('Kwota') }}: {{ number_format((float) $currentMonthPass->amount, 2, ',', ' ') }} zł
                </span>
            </div>
            @if(!$currentMonthPass->is_paid)
                <a href="{{ route('student.payments.index') }}" class="mt-3 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-md">
                    {{ __('Opłać karnet') }}
                </a>
            @endif
        @else
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Brak aktywnego karnetu miesięcznego') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Zapisz się do grupy, aby aktywować karnet.') }}</p>
        @endif
    </div>

    <h1 class="text-2xl font-bold text-gray-900 mb-6 mt-8">{{ __('My Dance Groups') }}</h1>

    @forelse($groups as $group)
    <div class="bg-white shadow rounded-lg p-6 mb-4">
        <h2 class="text-lg font-semibold text-gray-900">{{ $group->name }}</h2>
        <p class="text-sm text-gray-500">Category: {{ $group->category->name }}</p>
        <p class="text-sm text-gray-500">Teacher: {{ $group->teacher->name }}</p>
        @if($group->schedule)
            <p class="text-sm text-gray-500">Schedule: {{ $group->schedule }}</p>
        @endif
    </div>
    @empty
    <div class="bg-white shadow rounded-lg p-6">
        <p class="text-gray-500 text-center">{{ __('You are not enrolled in any dance groups yet.') }}</p>
    </div>
    @endforelse

    <h1 class="text-2xl font-bold text-gray-900 mb-6 mt-8">{{ __('My Events') }}</h1>

    @forelse($events as $event)
    <div class="bg-white shadow rounded-lg p-6 mb-4">
        <h2 class="text-lg font-semibold text-gray-900">{{ $event->name }}</h2>
        <p class="text-sm text-gray-500">{{ $event->date->translatedFormat('d.m.Y') }}, {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</p>
        @if($event->teacher)
            <p class="text-sm text-gray-500">{{ __('Instructor') }}: {{ $event->teacher->name }}</p>
        @endif
    </div>
    @empty
    <div class="bg-white shadow rounded-lg p-6">
        <p class="text-gray-500 text-center">{{ __('You are not enrolled in any events yet.') }}</p>
    </div>
    @endforelse
</div>
@endsection
