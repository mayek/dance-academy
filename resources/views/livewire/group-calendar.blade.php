<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
        <button wire:click="previousWeek" class="text-gray-500 hover:text-gray-700 text-sm font-medium flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Previous') }}
        </button>
        <div class="text-sm font-semibold text-gray-900">{{ $weekLabel }}</div>
        <div class="flex items-center gap-2">
            @if($weekOffset !== 0)
                <button wire:click="goToCurrent" class="text-xs text-gray-500 hover:text-gray-700 underline">{{ __('Today') }}</button>
            @endif
            <button wire:click="nextWeek" class="text-gray-500 hover:text-gray-700 text-sm font-medium flex items-center gap-1">
                {{ __('Next') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
    <div class="grid grid-cols-7 divide-x divide-gray-200 min-h-[200px]">
        @foreach($days as $day)
            <div class="{{ $day['isToday'] ? 'bg-amber-50' : '' }}">
                <div class="text-center py-2 border-b border-gray-200 {{ $day['isToday'] ? 'bg-amber-100' : 'bg-gray-50' }}">
                    <div class="text-xs font-semibold text-gray-600 uppercase">{{ $day['label'] }}</div>
                    <div class="text-sm font-bold {{ $day['isToday'] ? 'text-amber-700' : 'text-gray-800' }}">{{ $day['dayNumber'] }} {{ $day['month'] }}</div>
                </div>
                <div class="p-1 space-y-1">
                    @php $dayNum = $loop->iteration; @endphp
                    @forelse(($schedule[$dayNum] ?? []) as $slot)
                        <button type="button" wire:click="openGroup({{ $slot['group']->id }})" class="w-full text-left text-xs rounded px-1.5 py-1 bg-blue-100 text-blue-800 hover:opacity-80 cursor-pointer">
                            <div class="font-medium truncate">{{ $slot['group']->name }}</div>
                            <div class="opacity-75">{{ $slot['start'] }}{{ $slot['end'] ? ' - ' . $slot['end'] : '' }}</div>
                        </button>
                    @empty
                        <div class="text-xs text-gray-300 text-center py-4">{{ __('—') }}</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
    @include('livewire.partials.calendar-students-modal')
</div>
