<div>
    <x-modal name="supplier-detail-modal" title="Supplier Details">
        @if($supplier)
            <div class="space-y-6">
                <!-- Header Info -->
                <div class="flex items-start justify-between border-b border-gray-100 dark:border-gray-700 pb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $supplier->name }}</h3>
                        <p class="text-sm text-gray-500">Supplier Account Information</p>
                    </div>
                    <div class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium border border-slate-200 dark:border-slate-600">
                        ID: #{{ $supplier->id }}
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Contact Person -->
                    <x-detail-item label="Contact Person" :value="$supplier->contact_person" />

                    <!-- Email -->
                    <x-detail-item label="Email" :value="$supplier->email">
                        <x-heroicon-o-envelope class="w-4 h-4 text-gray-400" />
                    </x-detail-item>

                    <!-- Phone -->
                    <x-detail-item label="Phone" :value="$supplier->phone ?? '-'">
                        <x-heroicon-o-phone class="w-4 h-4 text-gray-400" />
                    </x-detail-item>

                    <!-- Registered At -->
                    <x-detail-item label="Registered At" :value="$supplier->created_at->format('d M Y, H:i')" />
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-6 space-y-6">
                    <!-- Address -->
                    <x-detail-item label="Address" :value="$supplier->address ?? '-'" />

                    <!-- Notes -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-gray-500">
                            Notes
                        </label>
                        <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-md border border-gray-100 dark:border-gray-700">
                            <p class="text-sm text-slate-700 dark:text-slate-300 italic leading-relaxed">{{ $supplier->notes ?? 'No additional notes.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', { name: 'supplier-detail-modal' })">
                        Close
                    </x-button>
                    <x-button type="button" wire:click="edit">
                        <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                        Edit Supplier
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
