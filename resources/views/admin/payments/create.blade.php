@extends('layouts.app')
@section('title', __('Record Payment'))

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Record Payment') }}</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.payments.store') }}" id="paymentForm">
            @csrf
            <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id', $selectedStudent) }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Student') }}</label>
                    <input type="text" id="student_search" placeholder="{{ __('Search by name, phone, email...') }}"
                           autocomplete="off"
                           value="{{ old('student_id') ? $students->firstWhere('id', old('student_id'))?->full_name : '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    <div id="student_clear" class="absolute right-2 top-8 hidden cursor-pointer text-gray-400 hover:text-gray-600 text-sm">{{ __('clear') }}</div>
                    <div id="student_dropdown" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
                    @error('student_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="dance_group_id" class="block text-sm font-medium text-gray-700">{{ __('Dance Group') }}</label>
                    <select name="dance_group_id" id="dance_group_id" required data-group-filter
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                        <option value="">{{ __('Select group') }}</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('dance_group_id', $selectedGroup) == $group->id ? 'selected' : '' }}>{{ $group->name }} ({{ $group->category->name }})</option>
                        @endforeach
                    </select>
                    @error('dance_group_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="pass_type_id" class="block text-sm font-medium text-gray-700">{{ __('Pass Type') }}</label>
                    <select name="pass_type_id" id="pass_type_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                        @foreach($passTypes as $passType)
                            <option value="{{ $passType->id }}" data-price="{{ $passType->price }}" {{ old('pass_type_id') == $passType->id ? 'selected' : '' }}>{{ $passType->display_name }} ({{ number_format($passType->price, 2) }} zł)</option>
                        @endforeach
                    </select>
                    @error('pass_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">{{ __('Amount (PLN)') }}</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" readonly
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2 bg-gray-50">
                </div>
            </div>
            <div class="mb-4">
                <label for="valid_from" class="block text-sm font-medium text-gray-700">{{ __('Valid From') }}</label>
                <input type="date" name="valid_from" id="valid_from" value="{{ old('valid_from', date('Y-m-d')) }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                @error('valid_from') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="is_paid" class="block text-sm font-medium text-gray-700">{{ __('Payment') }}</label>
                <select name="is_paid" id="is_paid"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    <option value="1" {{ old('is_paid', 1) == 1 ? 'selected' : '' }}>{{ __('Paid') }}</option>
                    <option value="0" {{ old('is_paid') === '0' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                </select>
            </div>
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
                <textarea name="notes" id="notes" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">{{ old('notes') }}</textarea>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Record Payment') }}</button>
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">{{ __('Cancel') }}</a>
            </div>
        </form>
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

<script>window.studentSearchData = @json($studentData);</script>

<script>
    const passTypeSelect = document.getElementById('pass_type_id');
    const amountField = document.getElementById('amount');
    const updateAmount = () => {
        const opt = passTypeSelect.selectedOptions[0];
        amountField.value = opt ? Number(opt.dataset.price).toFixed(2) : '';
    };
    passTypeSelect.addEventListener('change', updateAmount);
    updateAmount();
</script>
@endsection
