@extends('layouts.app')
@section('title', 'Record Payment')

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Record Payment</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.payments.store') }}" id="paymentForm">
            @csrf
            <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id', $selectedStudent) }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700">Student</label>
                    <input type="text" id="student_search" placeholder="Search by name, PESEL, or phone..."
                           autocomplete="off"
                           value="{{ old('student_id') ? $students->firstWhere('id', old('student_id'))?->full_name : '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    <div id="student_clear" class="absolute right-2 top-8 hidden cursor-pointer text-gray-400 hover:text-gray-600 text-sm">clear</div>
                    <div id="student_dropdown" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
                    @error('student_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="dance_group_id" class="block text-sm font-medium text-gray-700">Dance Group</label>
                    <select name="dance_group_id" id="dance_group_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                        <option value="">Select group</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('dance_group_id', $selectedGroup) == $group->id ? 'selected' : '' }}>{{ $group->name }} ({{ $group->category->name }})</option>
                        @endforeach
                    </select>
                    @error('dance_group_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="pass_type" class="block text-sm font-medium text-gray-700">Pass Type</label>
                    <select name="pass_type" id="pass_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                        <option value="monthly" {{ old('pass_type') == 'monthly' ? 'selected' : '' }}>Monthly Pass (150 &zloty;)</option>
                        <option value="single" {{ old('pass_type') == 'single' ? 'selected' : '' }}>Single Class Pass (25 &zloty;)</option>
                    </select>
                    @error('pass_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount (&zloty;)</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" value="{{ old('amount', '150.00') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mb-4">
                <label for="valid_from" class="block text-sm font-medium text-gray-700">Valid From</label>
                <input type="date" name="valid_from" id="valid_from" value="{{ old('valid_from', date('Y-m-d')) }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                @error('valid_from') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">{{ old('notes') }}</textarea>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg">Record Payment</button>
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

@php
$studentData = $students->map(fn($s) => (object) [
    'id' => $s->id,
    'full_name' => $s->full_name,
    'email' => $s->email,
    'pesel' => $s->pesel,
    'phone' => $s->phone_number,
    'parent_phone' => $s->parent_phone_number,
])->values();
@endphp

@push('scripts')
<script>
    //console.log('Student data:', @json($studentData));
const students = @json($studentData);

const searchInput = document.getElementById('student_search');
const hiddenInput = document.getElementById('student_id');
const dropdown = document.getElementById('student_dropdown');
const clearBtn = document.getElementById('student_clear');

function renderDropdown(query) {
    const q = query.toLowerCase().trim();
    if (!q) {
        dropdown.classList.add('hidden');
        return;
    }

    const matches = students.filter(s =>
        (s.full_name && s.full_name.toLowerCase().includes(q)) ||
        (s.email && s.email.toLowerCase().includes(q)) ||
        (s.pesel && s.pesel.includes(q)) ||
        (s.phone && s.phone.replace(/\s/g, '').includes(q.replace(/\s/g, ''))) ||
        (s.parent_phone && s.parent_phone.replace(/\s/g, '').includes(q.replace(/\s/g, '')))
    );

    if (matches.length === 0) {
        dropdown.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">No students found</div>';
        dropdown.classList.remove('hidden');
        return;
    }

    dropdown.innerHTML = matches.map(s => {
        const parts = [];
        if (s.pesel) parts.push('PESEL: ' + s.pesel);
        if (s.phone) parts.push('Tel: ' + s.phone);
        if (s.email) parts.push(s.email);
        const meta = parts.join(' &middot; ');
        return `<div class="px-4 py-2 cursor-pointer hover:bg-emerald-50 border-b border-gray-100 last:border-0" data-id="${s.id}" data-name="${s.full_name}">
            <div class="text-sm font-medium text-gray-900">${s.full_name}</div>
            <div class="text-xs text-gray-500">${meta}</div>
        </div>`;
    }).join('');

    dropdown.classList.remove('hidden');

    dropdown.querySelectorAll('[data-id]').forEach(el => {
        el.addEventListener('mousedown', function(e) {
            e.preventDefault();
            hiddenInput.value = this.dataset.id;
            searchInput.value = this.dataset.name;
            dropdown.classList.add('hidden');
            clearBtn.classList.remove('hidden');
        });
    });
};

searchInput.addEventListener('input', function() {
    hiddenInput.value = '';
    clearBtn.classList.add('hidden');
    renderDropdown(this.value);
});

searchInput.addEventListener('focus', function() {
    if (this.value) renderDropdown(this.value);
});

clearBtn.addEventListener('click', function() {
    searchInput.value = '';
    hiddenInput.value = '';
    dropdown.classList.add('hidden');
    clearBtn.classList.add('hidden');
});

document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

document.getElementById('pass_type').addEventListener('change', function() {
    const amountField = document.getElementById('amount');
    amountField.value = this.value === 'monthly' ? '150.00' : '25.00';
});
</script>
@endpush
@endsection
