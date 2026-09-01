@extends('layouts.app')
@section('title', __('Student Dashboard'))

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('My Schedule') }}</h1>
    <livewire:student-calendar />

    <h1 class="text-2xl font-bold text-gray-900 mb-6 mt-8">{{ __('My Dance Groups') }}</h1>

    @forelse($groups as $group)
    <div class="bg-white shadow rounded-lg p-6 mb-4">
        <h2 class="text-lg font-semibold text-gray-900">{{ $group->name }}</h2>
        <p class="text-sm text-gray-500">Category: {{ $group->category->name }}</p>
        <p class="text-sm text-gray-500">Teacher: {{ $group->teacher->name }}</p>
        @if($group->schedule)
            <p class="text-sm text-gray-500">Schedule: {{ $group->schedule }}</p>
        @endif
        @if(isset($activePassByGroup[$group->id]))
            @php $pass = $activePassByGroup[$group->id]; @endphp
            <div class="flex flex-wrap gap-2 mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ __('Aktywny karnet') }}: {{ $pass->daysLeft() }} {{ __('dni') }}
                </span>
                @if($pass->total_hours !== null)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pass->remainingHours() > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ __('Godziny') }}: {{ $pass->remainingHours() }} / {{ $pass->total_hours }}h
                    </span>
                @endif
                @if($pass->is_paid)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ __('Opłacony') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ __('Nieopłacony') }}
                    </span>
                @endif
            </div>
        @else
            <p class="text-sm mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ __('Brak aktywnego karnetu') }}
                </span>
            </p>
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
