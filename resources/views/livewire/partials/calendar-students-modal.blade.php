@isset($modalTitle)
    <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="students-modal" role="dialog" aria-modal="true">
        <div class="flex items-end sm:items-center justify-center min-h-screen p-4 sm:p-6">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="relative w-full max-w-2xl max-h-[85vh] flex flex-col bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 truncate">{{ $modalTitle }}</h3>
                        @isset($modalSubtitle)
                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $modalSubtitle }}</p>
                        @endisset
                    </div>
                    <button type="button" wire:click="closeModal"
                        class="flex-shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                        aria-label="{{ __('Close') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-5 py-3 flex-1 overflow-y-auto">
                    @forelse($modalStudents as $student)
                        @php
                            $passStatus = isset($modalPassStatus) ? ($modalPassStatus[$student->id] ?? ['has_pass' => false, 'is_paid' => false]) : null;
                            $hasPaidPass = $passStatus && $passStatus['has_pass'] && $passStatus['is_paid'];
                        @endphp
                        <div class="py-2.5 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-9 h-9 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold flex-shrink-0">
                                    {{ collect(explode(' ', trim($student->full_name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->join('') }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $student->full_name }}</div>
                                    @if($student->phone_number)
                                        <div class="text-xs text-gray-500">{{ $student->phone_number }}</div>
                                    @endif
                                </div>
                                @if(isset($modalEventId) && $passStatus)
                                    <div class="flex-shrink-0">
                                        @if($hasPaidPass)
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full px-2 py-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                {{ __('Pass paid') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full px-2 py-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                @if($passStatus['has_pass'])
                                                    {{ __('Pass unpaid') }}
                                                @else
                                                    {{ __('No pass') }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            @if(isset($modalEventId) && $passStatus && !$hasPaidPass && isset($modalBuyPassRoute) && $modalPassTypes->isNotEmpty())
                                <form method="POST" action="{{ $modalBuyPassRoute }}" class="mt-2 ml-12 flex flex-wrap items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="event_id" value="{{ $modalEventId }}">
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <select name="pass_type_id" class="text-xs rounded-lg border border-gray-300 px-2 py-1.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        @foreach($modalPassTypes as $passType)
                                            <option value="{{ $passType->id }}">{{ $passType->display_name }} — {{ number_format($passType->price, 2) }} zł</option>
                                        @endforeach
                                    </select>
                                    <select name="is_paid" class="text-xs rounded-lg border border-gray-300 px-2 py-1.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        <option value="1">{{ __('Paid') }}</option>
                                        <option value="0">{{ __('Unpaid') }}</option>
                                    </select>
                                    <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg px-3 py-1.5 cursor-pointer">
                                        {{ __('Buy Pass') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <div class="text-3xl mb-2">👥</div>
                            <p class="text-sm text-gray-500">{{ __('No students assigned.') }}</p>
                        </div>
                    @endforelse
                </div>
                @isset($modalAssignRoute)
                    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-2">
                        <a href="{{ $modalAssignRoute }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-purple-700 hover:text-purple-900">
                            {{ __('Assign Students') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        @isset($modalEditRoute)
                            <a href="{{ $modalEditRoute }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-purple-700 hover:text-purple-900">
                                {{ __('Edit') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endisset
                    </div>
                @elseif(isset($modalEditRoute))
                    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex justify-end">
                        <a href="{{ $modalEditRoute }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-purple-700 hover:text-purple-900">
                            {{ __('Edit') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endisset
            </div>
        </div>
    </div>
@endisset
