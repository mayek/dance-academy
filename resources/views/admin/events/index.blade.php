@extends('layouts.app')
@section('title', __('Manage Events'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Events') }}</h1>
        <a href="{{ route('admin.events.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('+ Add Event') }}</a>
    </div>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Weekly Schedule') }}</h2>
        <livewire:event-calendar />
    </div>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Start Time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('End Time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Students') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Instructor') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($events as $event)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $event->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $event->date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $event->students->count() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $event->teacher?->name ?? $event->creator->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                        <button type="button" class="open-event-pass text-emerald-600 hover:text-emerald-800 inline-flex align-middle cursor-pointer"
                            data-event-id="{{ $event->id }}" data-event-name="{{ $event->name }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                        <a href="{{ route('admin.events.assign', $event) }}" class="text-indigo-600 hover:text-indigo-800 inline-flex align-middle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </a>
                        <a href="{{ route('admin.events.edit', $event) }}" class="text-emerald-600 hover:text-emerald-800 inline-flex align-middle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 inline-flex align-middle">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No events found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $events->links() }}</div>
</div>

<div id="event-pass-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-screen p-4 sm:p-6">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeEventPassModal()"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-gray-100 bg-gray-50">
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Buy Pass for Event') }}</h3>
                    <p id="event-pass-subtitle" class="text-xs text-gray-500 mt-0.5 truncate">{{ __('Find a student to sell a pass to.') }}</p>
                </div>
                <button type="button" onclick="closeEventPassModal()"
                    class="flex-shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                    aria-label="{{ __('Close') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.events.buy-pass') }}" class="px-5 py-4">
                @csrf
                <input type="hidden" name="event_id" id="event_pass_id" value="">
                <div>
                    <label for="student_search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Student') }}</label>
                    <div class="relative">
                        <input type="text" id="student_search" name="student_search" autocomplete="off"
                            placeholder="{{ __('Search by name, phone, email...') }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <input type="hidden" id="student_id" name="student_id" value="">
                        <button type="button" id="student_clear" class="hidden absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600" aria-label="{{ __('Clear') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <div id="student_dropdown" class="hidden absolute z-20 mt-1 left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-y-auto"></div>
                    </div>
                    @error('student_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Pass Type') }}</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($passTypes as $passType)
                        <label class="relative border rounded-lg p-4 cursor-pointer hover:border-emerald-500 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 transition">
                            <input type="radio" name="pass_type_id" value="{{ $passType->id }}" {{ $loop->first ? 'checked' : '' }} class="sr-only">
                            <div class="text-sm font-semibold text-gray-900">{{ $passType->display_name }}</div>
                            <div class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($passType->price, 2) }} zł</div>
                            <div class="text-xs text-gray-500 mt-1">{{ __('Valid for one class') }}</div>
                        </label>
                        @empty
                        <p class="col-span-2 text-sm text-gray-500">{{ __('No single passes available. Create one in Passes.') }}</p>
                        @endforelse
                    </div>
                    @error('pass_type_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label for="is_paid" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Payment') }}</label>
                    <select name="is_paid" id="is_paid"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="1" selected>{{ __('Paid') }}</option>
                        <option value="0">{{ __('Unpaid') }}</option>
                    </select>
                </div>
                <div class="flex space-x-3 mt-6">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg cursor-pointer">{{ __('Purchase Pass') }}</button>
                    <button type="button" onclick="closeEventPassModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg cursor-pointer">{{ __('Cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
$studentData = $students->map(fn($s) => (object) [
    'id' => $s->id,
    'full_name' => $s->full_name,
    'email' => $s->email,
    'phone' => $s->phone_number,
    'parent_phone' => $s->parent_phone_number,
    'group_ids' => $s->enrolledGroups->pluck('id')->all(),
])->values();
@endphp

@push('scripts')
<script>
    window.studentSearchData = @json($studentData);

    function openEventPassModal(eventId, eventName) {
        document.getElementById('event_pass_id').value = eventId;
        document.getElementById('event-pass-subtitle').textContent = eventName || '';
        document.getElementById('student_id').value = '';
        document.getElementById('student_search').value = '';
        document.getElementById('student_clear').classList.add('hidden');
        document.getElementById('event-pass-modal').classList.remove('hidden');
        document.getElementById('student_search').focus();
    }

    function closeEventPassModal() {
        document.getElementById('event-pass-modal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.open-event-pass').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openEventPassModal(btn.dataset.eventId, btn.dataset.eventName);
            });
        });
    });
</script>
@endpush
@endsection
