@extends('layouts.app')
@section('title', __('Admin Dashboard'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Admin Dashboard') }}</h1>
        <button type="button" onclick="openBuyPassModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 px-4 rounded-lg cursor-pointer">{{ __('Buy Pass') }}</button>
    </div>
    <div class="mt-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Weekly Schedule') }}</h2>
        <livewire:dashboard-calendar />
    </div>

    @if($expiringPasses->isNotEmpty())
    <div class="mt-8">
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
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
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
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <button type="button"
                                class="open-buy-pass inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white transition cursor-pointer"
                                data-student-id="{{ $payment->student_id }}"
                                data-student-name="{{ $payment->student->full_name }}"
                                data-group-id="{{ $payment->dance_group_id }}">
                                {{ __('Buy Pass') }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div id="buy-pass-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end sm:items-center justify-center min-h-screen p-4 sm:p-6">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeBuyPassModal()"></div>
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Buy Pass') }}</h3>
                        <p id="buy-pass-subtitle" class="text-xs text-gray-500 mt-0.5 truncate">{{ __('Find a student to sell a pass to.') }}</p>
                    </div>
                    <button type="button" onclick="closeBuyPassModal()"
                        class="flex-shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                        aria-label="{{ __('Close') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.payments.buy-pass') }}" class="px-5 py-4">
                    @csrf
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
                        <label for="buy-pass-group" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Group') }}</label>
                        <select id="buy-pass-group" name="dance_group_id" data-group-filter
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">{{ __('Select group...') }}</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        @error('dance_group_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Pass Type') }}</label>
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
                        <button type="button" onclick="closeBuyPassModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg cursor-pointer">{{ __('Cancel') }}</button>
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

        function openBuyPassModal(studentId = null, studentName = '', groupId = null) {
            const modal = document.getElementById('buy-pass-modal');
            const hidden = document.getElementById('student_id');
            const search = document.getElementById('student_search');
            const clear = document.getElementById('student_clear');
            const group = document.getElementById('buy-pass-group');
            const subtitle = document.getElementById('buy-pass-subtitle');

            hidden.value = studentId || '';
            search.value = studentName;
            group.value = groupId || '';
            clear.classList.toggle('hidden', !studentId);
            if (window.filterStudentGroups) window.filterStudentGroups(studentId || '');

            const groupName = group.selectedOptions.length && group.value
                ? group.selectedOptions[0].textContent.trim() : '';
            subtitle.textContent = studentName ? (studentName + (groupName ? ' \u2014 ' + groupName : '')) : '';

            modal.classList.remove('hidden');
            if (!studentId) search.focus();
        }

        function closeBuyPassModal() {
            document.getElementById('buy-pass-modal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.open-buy-pass').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    openBuyPassModal(btn.dataset.studentId, btn.dataset.studentName, btn.dataset.groupId);
                });
            });
            document.getElementById('buy-pass-group').addEventListener('change', function () {
                const search = document.getElementById('student_search');
                const groupName = this.selectedOptions.length && this.value
                    ? this.selectedOptions[0].textContent.trim() : '';
                document.getElementById('buy-pass-subtitle').textContent =
                    search.value ? (search.value + (groupName ? ' \u2014 ' + groupName : '')) : '';
            });
        });
    </script>
    @endpush

    <div class="mt-8 bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Revenue') }}</h2>
        <div class="relative" style="height: 280px;">
            <canvas id="revenueChart"
                    data-labels='{{ json_encode($labels) }}'
                    data-revenue='{{ json_encode($revenue) }}'
                    data-label="{{ __('Revenue') }}"></canvas>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Staff') }}</div>
            <div class="mt-1 text-3xl font-bold text-purple-600">{{ $stats['teachers'] + $stats['reception'] }}</div>
            <a href="{{ route('admin.staff.index') }}" class="mt-3 inline-block text-sm text-purple-600 hover:text-purple-800">{{ __('Manage') }} &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Students') }}</div>
            <div class="mt-1 text-3xl font-bold text-blue-600">{{ $stats['students'] }}</div>
            <a href="{{ route('admin.students.index') }}" class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-800">{{ __('Manage') }} &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Categories') }}</div>
            <div class="mt-1 text-3xl font-bold text-green-600">{{ $stats['categories'] }}</div>
            <a href="{{ route('admin.categories.index') }}" class="mt-3 inline-block text-sm text-green-600 hover:text-green-800">{{ __('Manage') }} &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Dance Groups') }}</div>
            <div class="mt-1 text-3xl font-bold text-orange-600">{{ $stats['groups'] }}</div>
            <a href="{{ route('admin.groups.index') }}" class="mt-3 inline-block text-sm text-orange-600 hover:text-orange-800">{{ __('Manage') }} &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Active Passes') }}</div>
            <div class="mt-1 text-3xl font-bold text-emerald-600">{{ $stats['active_passes'] }}</div>
            <a href="{{ route('admin.payments.index') }}" class="mt-3 inline-block text-sm text-emerald-600 hover:text-emerald-800">{{ __('Manage') }} &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('Monthly Revenue') }}</div>
            <div class="mt-1 text-3xl font-bold text-indigo-600">{{ number_format($stats['monthly_revenue'], 2) }} zł</div>
            <a href="{{ route('admin.payments.index') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800">{{ __('Manage') }} &rarr;</a>
        </div>
    </div>
</div>
@endsection
