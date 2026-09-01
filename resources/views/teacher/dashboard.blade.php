@extends('layouts.app')
@section('title', __('Teacher Dashboard'))

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('My Dance Groups') }}</h1>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Weekly Schedule') }}</h2>
        <livewire:dashboard-calendar :teacher-id="auth()->id()" :key="'teacher-dashboard-calendar'" />
    </div>

    @if($expiringPasses->isNotEmpty())
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse"></div>
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Expiring Passes (next 7 days)') }}</h2>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ $expiringPasses->count() }}</span>
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-amber-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Student') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Group') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Pass Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Expires') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Days Left') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($expiringPasses as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->student->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->danceGroup->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($payment->isMonthly())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ __('Monthly') }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">{{ __('Single') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->valid_until->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php $days = $payment->daysLeft(); @endphp
                            @if($days <= 1)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ $days }} {{ __('day') }}</span>
                            @elseif($days <= 3)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">{{ $days }} {{ __('days') }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">{{ $days }} {{ __('days') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @forelse($groups as $group)
    <div class="bg-white shadow rounded-lg p-6 mb-4">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-lg font-semibold text-gray-900" title="{{ $group->name }}">{{ \Illuminate\Support\Str::limit($group->name, 60) }}</h2>
                <p class="text-sm text-gray-500">Category: {{ $group->category->name }}</p>
                @if($group->schedule)
                    <p class="text-sm text-gray-500" title="{{ $group->schedule }}">Schedule: {{ \Illuminate\Support\Str::limit($group->schedule, 60) }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('teacher.attendance.create', ['group_id' => $group->id]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('Record Attendance') }}</a>
                <a href="{{ route('teacher.attendance.history', $group) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg">{{ __('History') }}</a>
                <a href="{{ route('teacher.assign', $group) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('Assign Students') }}</a>
            </div>
        </div>
        <div class="mt-4">
            <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('Enrolled Students') }} ({{ $group->students->count() }})</h3>
            @if($group->students->isEmpty())
                <p class="text-sm text-gray-400">{{ __('No students enrolled yet.') }}</p>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach($group->students as $student)
                    @php
                        $pass = $student->payments
                            ->where('dance_group_id', $group->id)
                            ->where('status', 'active')
                            ->filter(fn ($p) => $p->valid_until && $p->valid_until->gte(now()->startOfDay()))
                            ->sortByDesc('valid_until')
                            ->first();
                    @endphp
                    <li class="py-2 flex justify-between items-center gap-3">
                        <span class="text-sm text-gray-900">{{ $student->full_name }}</span>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500">{{ $student->email }}</span>
                            @if($pass)
                                @php $days = $pass->daysLeft(); @endphp
                                @if($days <= 1)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ $days }} {{ __('day') }}</span>
                                @elseif($days <= 3)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">{{ $days }} {{ __('days') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">{{ $days }} {{ __('days') }}</span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">{{ __('No active pass') }}</span>
                            @endif
                            @if(!$pass)
                            <button type="button"
                                onclick="document.getElementById('buy-pass-{{ $group->id }}-{{ $student->id }}').classList.remove('hidden')"
                                class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white transition cursor-pointer">
                                {{ __('Buy Pass') }}
                            </button>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>

                @foreach($group->students as $student)
                <div id="buy-pass-{{ $group->id }}-{{ $student->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                    <div class="flex items-end sm:items-center justify-center min-h-screen p-4 sm:p-6">
                        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                            onclick="document.getElementById('buy-pass-{{ $group->id }}-{{ $student->id }}').classList.add('hidden')"></div>
                        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
                            <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-gray-100 bg-gray-50">
                                <div class="min-w-0">
                                    <h3 class="text-base font-semibold text-gray-900">{{ __('Buy Pass') }}</h3>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $student->full_name }} &mdash; {{ $group->name }}</p>
                                </div>
                                <button type="button"
                                    onclick="document.getElementById('buy-pass-{{ $group->id }}-{{ $student->id }}').classList.add('hidden')"
                                    class="flex-shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                                    aria-label="{{ __('Close') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <form method="POST" action="{{ route('teacher.passes.store', [$group, $student]) }}" class="px-5 py-4">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <input type="hidden" name="dance_group_id" value="{{ $group->id }}">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($passTypes as $passType)
                                    <label class="relative border rounded-lg p-4 cursor-pointer hover:border-emerald-500 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 transition">
                                        <input type="radio" name="pass_type_id" value="{{ $passType->id }}" {{ $loop->first ? 'checked' : '' }} class="sr-only">
                                        <div class="text-sm font-semibold text-gray-900">{{ $passType->display_name }}</div>
                                        <div class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($passType->price, 2) }} zł</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            @if($passType->isSingle())
                                                {{ __('Valid for one class') }}
                                            @else
                                                {{ __('Valid for') }} {{ $passType->duration_label }}
                                            @endif
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                                @error('pass_type_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                <div class="mt-4">
                                    <label for="is_paid-{{ $group->id }}-{{ $student->id }}" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Payment') }}</label>
                                    <select name="is_paid" id="is_paid-{{ $group->id }}-{{ $student->id }}"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                        <option value="1" selected>{{ __('Paid') }}</option>
                                        <option value="0">{{ __('Unpaid') }}</option>
                                    </select>
                                </div>
                                <div class="flex space-x-3 mt-6">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg cursor-pointer">{{ __('Purchase Pass') }}</button>
                                    <button type="button"
                                        onclick="document.getElementById('buy-pass-{{ $group->id }}-{{ $student->id }}').classList.add('hidden')"
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg cursor-pointer">{{ __('Cancel') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white shadow rounded-lg p-6">
        <p class="text-gray-500 text-center">{{ __('No groups assigned to you yet.') }}</p>
    </div>
    @endforelse
</div>
@endsection
