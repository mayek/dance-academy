<div>
    <label for="group_picker_search" class="block text-sm font-medium text-gray-700">{{ __('Dance Groups') }}</label>
    <input type="text" id="group_picker_search" wire:model.live.debounce.300ms="search"
           placeholder="{{ __('Search by name...') }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
    <div class="mt-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md divide-y divide-gray-100">
        @forelse($groups as $group)
        <label wire:key="group-check-{{ $group->id }}" class="flex items-center gap-2 px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm">
            <input type="checkbox" name="group_ids[]" value="{{ $group->id }}"
                   {{ in_array($group->id, old('group_ids', $this->selected)) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
            <span class="font-medium text-gray-900">{{ $group->name }}</span>
            <span class="text-gray-400">({{ $group->category->name }})</span>
        </label>
        @empty
        <p class="px-3 py-4 text-sm text-gray-500 text-center">{{ __('No groups found.') }}</p>
        @endforelse
    </div>
    @error('group_ids.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
