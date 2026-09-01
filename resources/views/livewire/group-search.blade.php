<div>
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="group_search" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Search') }}</label>
                <input type="text" id="group_search" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name...') }}"
                       class="w-full rounded-md border-gray-300 text-sm border p-2">
            </div>
            <div class="flex items-end">
                <button type="button" wire:click="$set('search', '')" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('Clear') }}</button>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Category') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Teacher') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Schedule') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Students') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($groups as $group)
                <tr wire:key="group-{{ $group->id }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $group->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $group->category->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $group->teacher->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-[240px] whitespace-normal break-words">{{ $group->schedule ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $group->students->count() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                        <a href="{{ route('admin.groups.assign', $group) }}" class="text-indigo-600 hover:text-indigo-800 inline-flex align-middle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </a>
                        <a href="{{ route('admin.groups.edit', $group) }}" class="text-orange-600 hover:text-orange-800 inline-flex align-middle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.groups.destroy', $group) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 inline-flex align-middle">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No groups found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $groups->links() }}</div>
</div>
