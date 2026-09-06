@extends('layouts.app')
@section('title', __('Manage Passes'))

@push('styles')
<style>
    .pass-name {
        display: inline-block;
        max-width: 320px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
        cursor: help;
    }
    .pass-name-tooltip {
        position: fixed;
        z-index: 50;
        max-width: 360px;
        padding: 6px 10px;
        background: #1f2937;
        color: #ffffff;
        font-size: 12px;
        line-height: 1.4;
        border-radius: 6px;
        pointer-events: none;
        white-space: normal;
        word-wrap: break-word;
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Passes') }}</h1>
        <a href="{{ route('admin.passes.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('+ Add Pass') }}</a>
    </div>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Pass Type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Duration') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Hours') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Price') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Payments') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase sticky right-0 bg-gray-50" style="box-shadow: -4px 0 6px rgba(0,0,0,0.05);">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($passTypes as $pass)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        <span class="pass-name" data-full="{{ $pass->display_name }}">{{ mb_strimwidth($pass->display_name, 0, 50, '…') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pass->duration_label ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pass->hours !== null ? $pass->hours . 'h' : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($pass->price, 2) }} zł</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pass->payments_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2 sticky right-0 bg-white" style="box-shadow: -4px 0 6px rgba(0,0,0,0.05);">
                        <a href="{{ route('admin.passes.edit', $pass) }}" class="text-green-600 hover:text-green-800 inline-flex align-middle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.passes.destroy', $pass) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 inline-flex align-middle">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No passes found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $passTypes->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const names = document.querySelectorAll('.pass-name');
        let tip = document.createElement('div');
        tip.className = 'pass-name-tooltip';
        tip.style.display = 'none';
        document.body.appendChild(tip);

        names.forEach(function (el) {
            el.addEventListener('mouseenter', function () {
                tip.textContent = el.getAttribute('data-full') || el.textContent;
                tip.style.display = 'block';
                positionTooltip(el, tip);
            });
            el.addEventListener('mousemove', function (e) {
                positionTooltip(e.clientX, e.clientY, tip);
            });
            el.addEventListener('mouseleave', function () {
                tip.style.display = 'none';
            });
        });

        function positionTooltip(x, y, tip) {
            let left = typeof x === 'number' ? x + 12 : x.getBoundingClientRect().left;
            let top = typeof y === 'number' ? y + 12 : x.getBoundingClientRect().bottom + 6;
            const rect = tip.getBoundingClientRect();
            if (left + rect.width > window.innerWidth - 8) {
                left = window.innerWidth - rect.width - 8;
            }
            if (top + rect.height > window.innerHeight - 8) {
                top = window.innerHeight - rect.height - 8;
            }
            tip.style.left = left + 'px';
            tip.style.top = top + 'px';
        }
    });
</script>
@endpush
