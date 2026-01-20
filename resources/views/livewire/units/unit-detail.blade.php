<div>
    <x-modal name="unit-detail-modal" title="Unit Details" maxWidth="sm">
        @if($unit)
            <div class="space-y-6">
                <!-- Header Info -->
                <div class="flex items-start justify-between border-b border-gray-200 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 tracking-tight">{{ $unit->name }}</h3>
                        <p class="text-sm text-gray-500">Unit Information</p>
                    </div>
                    <div class="inline-flex items-center rounded-full border border-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-700 bg-gray-50">
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
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
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
            <div class="p-8 text-center text-gray-500">
                Loading details...
            </div>
        @endif
    </x-modal>
</div>
