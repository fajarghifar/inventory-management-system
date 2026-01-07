<div>
    <x-modal name="unit-detail-modal" title="Unit Details" maxWidth="sm">
        @if($unit)
            <div class="space-y-6">
                <!-- Header Info -->
                <div class="flex items-start justify-between border-b border-gray-100 dark:border-gray-700 pb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $unit->name }}</h3>
                        <p class="text-sm text-gray-500">Unit Information</p>
                    </div>
                    <div class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium border border-slate-200 dark:border-slate-600">
                        ID: #{{ $unit->id }}
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 gap-6">
                    <x-detail-item label="Name" :value="$unit->name" />
                    <x-detail-item label="Symbol" :value="$unit->symbol" />
                    <x-detail-item label="Created At" :value="$unit->created_at->format('d M Y, H:i')" />
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', { name: 'unit-detail-modal' })">
                        Close
                    </x-button>
                    <x-button type="button" wire:click="edit">
                        <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                        Edit Unit
                    </x-button>
                </div>
            </div>
        @else
            <div class="p-4 text-center text-gray-500">
                Loading details...
            </div>
        @endif
    </x-modal>
</div>
