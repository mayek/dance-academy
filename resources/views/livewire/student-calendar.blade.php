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
    <div class="grid grid-cols-7 divide-x divide-gray-200 min-h-[250px]">
        @foreach($days as $i => $day)
            <div class="{{ $day['isToday'] ? 'bg-amber-50' : '' }}">
                <div class="text-center py-2 border-b border-gray-200 {{ $day['isToday'] ? 'bg-amber-100' : 'bg-gray-50' }}">
                    <div class="text-xs font-semibold text-gray-600 uppercase">{{ $day['label'] }}</div>
                    <div class="text-sm font-bold {{ $day['isToday'] ? 'text-amber-700' : 'text-gray-800' }}">{{ $day['dayNumber'] }} {{ $day['month'] }}</div>
                </div>
                <div class="p-1 space-y-1">
                    @forelse($combined[$i] as $slot)
                        <button type="button" wire:click="{{ $slot['type'] === 'group' ? 'openGroup' : 'openEvent' }}({{ $slot['id'] }})" class="block w-full text-left text-xs rounded px-1.5 py-1 {{ $slot['color'] }} hover:opacity-80 cursor-pointer">
                            <div class="font-medium truncate">{{ $slot['title'] }}</div>
                            <div class="opacity-75">{{ $slot['subtitle'] }}</div>
                        </button>
                    @empty
                        <div class="text-xs text-gray-300 text-center py-4">{{ __('—') }}</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
    @if($modal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end sm:items-center justify-center min-h-screen p-4 sm:p-6">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="closeModal"></div>
                <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-gray-900 truncate">{{ $modal['title'] }}</h3>
                            @isset($modal['subtitle'])
                                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $modal['subtitle'] }}</p>
                            @endisset
                        </div>
                        <button type="button" wire:click="closeModal"
                            class="flex-shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                            aria-label="{{ __('Close') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="px-5 py-4 max-h-80 overflow-y-auto space-y-2">
                        @forelse(array_filter($modal['lines']) as $line)
                            <p class="text-sm text-gray-600">{{ $line }}</p>
                        @empty
                            <p class="text-sm text-gray-500">{{ __('No additional details.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
