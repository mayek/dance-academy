@extends('layouts.app')
@section('title', __('Sessions'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Zajęcia') }} - {{ $month->translatedFormat('F Y') }}</h1>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.sessions.index', ['month' => $month->copy()->subMonth()->format('Y-m'), 'group_id' => $selectedGroupId], ) }}" class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 cursor-pointer">&lsaquo;</a>
            <a href="{{ route('admin.sessions.index', ['month' => $month->copy()->addMonth()->format('Y-m'), 'group_id' => $selectedGroupId]) }}" class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 cursor-pointer">&rsaquo;</a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.sessions.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Miesiąc') }}</label>
                <input type="month" id="month" name="month" value="{{ $month->format('Y-m') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="group_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Grupa') }}</label>
                <select id="group_id" name="group_id" class="rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <option value="">{{ __('Wszystkie grupy') }}</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" @selected((string) $group->id === (string) $selectedGroupId)>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 text-sm bg-orange-600 hover:bg-orange-700 text-white rounded-md cursor-pointer">{{ __('Filtruj') }}</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.sessions.sync') }}" class="flex flex-wrap items-end gap-3 mt-4 border-t border-gray-100 pt-4">
            @csrf
            <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
            <input type="hidden" name="group_id" value="{{ $selectedGroupId }}">
            <div class="text-sm text-gray-500 mt-1">
                {{ __('Wygeneruj zajęcia z tygodniowego planu grupy. Już istniejące terminy nie zostaną nadpisane.') }}
            </div>
            <div class="ml-auto">
                <button type="submit" class="px-4 py-2 text-sm bg-emerald-600 hover:bg-emerald-700 text-white rounded-md cursor-pointer">{{ __('Generuj zajęcia') }}</button>
            </div>
        </form>
    </div>

    @forelse($sessions as $date => $daySessions)
    @php $day = \Carbon\Carbon::parse($date); @endphp
    <div class="bg-white shadow rounded-lg mb-4">
        <div class="px-4 py-3 bg-gray-50 rounded-t-lg flex items-center justify-between">
            <div>
                <span class="font-semibold text-gray-900">{{ $day->translatedFormat('l') }}</span>
                <span class="text-gray-500 ml-2">{{ $day->format('d.m.Y') }}</span>
            </div>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <tbody class="divide-y divide-gray-100">
                @foreach($daySessions as $session)
                <tr class="{{ $session->isCancelled() ? 'opacity-50' : '' }}">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $session->start_time }} - {{ $session->end_time }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $session->danceGroup->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $session->room ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($session->isCancelled())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ __('Odwołane') }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('Odbywa się') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap relative">
                        <details class="inline-block align-middle relative z-40">
                            <summary class="cursor-pointer select-none text-sm text-orange-600 hover:text-orange-800">{{ __('Edytuj') }}</summary>
                            <div class="absolute right-0 mt-2 w-96 max-w-[calc(100vw-2rem)] bg-white border border-gray-300 rounded-lg shadow-xl p-5 z-[9999] text-left">
                                <form method="POST" action="{{ route('admin.sessions.update', $session) }}" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">{{ __('Data') }}</label>
                                            <input type="date" name="date" value="{{ $session->date->format('Y-m-d') }}" class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">{{ __('Sala') }}</label>
                                            <select name="room" class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm">
                                                <option value="">-</option>
                                                <option value="górna" @selected($session->room === 'górna')>{{ __('Górna') }}</option>
                                                <option value="dolna" @selected($session->room === 'dolna')>{{ __('Dolna') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">{{ __('Start') }}</label>
                                            <input type="time" name="start_time" value="{{ $session->start_time }}" class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">{{ __('Koniec') }}</label>
                                            <input type="time" name="end_time" value="{{ $session->end_time }}" class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">{{ __('Status') }}</label>
                                        <select name="status" class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm">
                                            <option value="planned" @selected(!$session->isCancelled())>{{ __('Odbywa się') }}</option>
                                            <option value="cancelled" @selected($session->isCancelled())>{{ __('Odwołane') }}</option>
                                        </select>
                                    </div>
                                    <div class="flex justify-end space-x-2 pt-1">
                                        <button type="submit" class="px-3 py-1.5 text-xs bg-orange-600 hover:bg-orange-700 text-white rounded-md cursor-pointer">{{ __('Zapisz') }}</button>
                                    </div>
                                </form>
                            </div>
                        </details>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <div class="bg-white shadow rounded-lg p-8 text-center text-gray-500">
        {{ __('Brak zajęć w tym miesiącu dla wybranej grupy.') }}
    </div>
    @endforelse
</div>
@endsection