@extends('layouts.app')
@section('title', __('Record Attendance'))

@section('content')
<div class="px-4 sm:px-0 max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Record Attendance') }}</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('admin.attendance.create') }}" class="mb-6 pb-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="group_id" class="block text-sm font-medium text-gray-700">{{ __('Dance Group') }}</label>
                    <select name="group_id" id="group_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                        <option value="">{{ __('Select group') }}</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ $selectedGroup == $group->id ? 'selected' : '' }}>{{ $group->name }} ({{ $group->category->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">{{ __('Date') }}</label>
                    <input type="date" name="date" id="date" value="{{ $selectedDate }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('Load Students') }}</button>
                </div>
            </div>
        </form>

        @if($selectedGroup)
            @php
                $group = $groups->firstWhere('id', $selectedGroup);
            @endphp
            @if($group && $roster->isNotEmpty())
            <form method="POST" action="{{ route('admin.attendance.store') }}">
                @csrf
                <input type="hidden" name="dance_group_id" value="{{ $selectedGroup }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $group->name }} &mdash; {{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}</h2>
                    <div class="flex gap-2 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-green-800">{{ __('Present') }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-800">{{ __('Absent') }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-yellow-100 text-yellow-800">{{ __('Excused') }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-orange-100 text-orange-800">Wejście jednorazowe</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800">Odrabianie</span>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach($roster as $student)
                    @php
                        $activePass = $activePassByStudent[$student->id] ?? null;
                        $remainingHours = $activePass?->remainingHours();
                        $type = $student->roster_type ?? 'regular';
                    @endphp
                    <div class="flex items-center gap-4 p-3 rounded-lg border border-gray-200 hover:bg-gray-50">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                @if($type === 'one_time')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Wejście jednorazowe</span>
                                @elseif($type === 'makeup')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Odrabianie</span>
                                @elseif($activePass && $remainingHours > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('Pozostało') }}: {{ $remainingHours }}h</span>
                                @elseif($activePass)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ __('Pozostało') }}: 0h</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ __('Brak godzin') }}</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">{{ $student->email }}</div>
                        </div>
                        <div class="w-40">
                            <select name="statuses[{{ $student->id }}]"
                                    class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-1.5">
                                <option value="present" {{ ($existingRecords[$student->id] ?? 'present') === 'present' ? 'selected' : '' }}>{{ __('Present') }}</option>
                                <option value="absent" {{ ($existingRecords[$student->id] ?? '') === 'absent' ? 'selected' : '' }}>{{ __('Absent') }}</option>
                                <option value="excused" {{ ($existingRecords[$student->id] ?? '') === 'excused' ? 'selected' : '' }}>{{ __('Excused') }}</option>
                            </select>
                        </div>
                        <div class="w-48">
                            <input type="text" name="notes[{{ $student->id }}]" placeholder="{{ __('Note (optional)') }}"
                                   class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-1.5">
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 flex space-x-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Save Attendance') }}</button>
                    <a href="{{ route('admin.attendance.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">{{ __('Cancel') }}</a>
                </div>
            </form>

            <hr class="my-8 border-gray-200">

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Dodaj osobę na ten termin</h3>
                <form method="POST" action="{{ route('admin.attendance.add-single') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    @csrf
                    <input type="hidden" name="dance_group_id" value="{{ $selectedGroup }}">
                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                    <div>
                        <label class="block text-xs text-gray-500">Uczeń</label>
                        <select name="student_id" class="add-student-select mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                            @foreach($groups->firstWhere('id', $selectedGroup)?->students ?? [] as $student)
                            @continue(in_array($student->id, $roster->pluck('id')->all()))
                            <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Typ</label>
                        <select name="type" id="add_type" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                            <option value="one_time">Wejście jednorazowe</option>
                            <option value="makeup">Odrabianie</option>
                        </select>
                    </div>
                    <div id="add_absence_wrap" class="hidden">
                        <label class="block text-xs text-gray-500">Nieobecność do odrobienia</label>
                        <select name="absence_id" id="add_absence" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                            <option value="">— wybierz —</option>
                            @foreach($makeupAbsences as $absence)
                            <option value="{{ $absence->id }}">{{ $absence->student->full_name }} ({{ $absence->danceGroup->name ?? '' }} {{ $absence->date->format('d.m.Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium py-2 px-4 rounded-lg">Dodaj</button>
                    </div>
                </form>
            </div>
            @elseif($group)
            <p class="text-gray-500 text-center">{{ __('No students enrolled in this group.') }}</p>
            @endif
        @else
            <p class="text-gray-400 text-center">{{ __('Select a group and date above to record attendance.') }}</p>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('add_type');
    const absenceWrap = document.getElementById('add_absence_wrap');
    const absence = document.getElementById('add_absence');
    if (type && absenceWrap && absence) {
        type.addEventListener('change', function () {
            const isMakeup = this.value === 'makeup';
            absenceWrap.classList.toggle('hidden', !isMakeup);
            absence.required = isMakeup;
        });
    }
});
</script>
@endsection