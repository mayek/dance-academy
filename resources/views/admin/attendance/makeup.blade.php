@extends('layouts.app')
@section('title', __('Make-up'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.attendance.index') }}" class="text-gray-400 hover:text-gray-600">&larr; {{ __('Back') }}</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Odrabianie zajęć') }}</h1>
            <p class="text-sm text-gray-500">{{ __('Nieobecności można odrabiać w dowolnej grupie w ciągu 14 dni od zajęć.') }}</p>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Student') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nieobecność') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Grupa') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Odrabianie') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($absences as $absence)
                @php $maxDate = $absence->date->copy()->addDays(13); $canMakeUp = $maxDate->gte(now()); @endphp
                <tr class="{{ !$canMakeUp ? 'opacity-40' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $absence->student->full_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $absence->date->format('d.m.Y') }}<br>
                        <span class="text-xs">{{ $absence->danceGroup->name }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->danceGroup->category->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($canMakeUp)
                        <details class="inline-block align-middle relative">
                            <summary class="cursor-pointer select-none text-green-600 hover:text-green-800 font-medium">{{ __('Odnotuj odrobienie') }}</summary>
                            <div class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-lg shadow-lg p-3 z-10 text-left space-y-2">
                                <form method="POST" action="{{ route('admin.attendance.made-up', $absence) }}">
                                    @csrf @method('PATCH')
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Grupa zajęć odrabianych') }}</label>
                                        <select name="group_id" required class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm">
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mt-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Data zajęć odrabianych') }}</label>
                                        <input type="date" name="date" required min="{{ $absence->date->format('Y-m-d') }}" max="{{ $maxDate->format('Y-m-d') }}" class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm">
                                    </div>
                                    <div class="flex justify-end pt-1">
                                        <button type="submit" class="px-3 py-1.5 text-xs bg-green-600 hover:bg-green-700 text-white rounded-md cursor-pointer">{{ __('Odnotuj odrobienie') }}</button>
                                    </div>
                                </form>
                            </div>
                        </details>
                        @else
                        <span class="text-gray-400 text-xs">{{ __('Mija 14 dni') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('Brak nieobecności do odrobienia.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $absences->links() }}</div>
</div>
@endsection