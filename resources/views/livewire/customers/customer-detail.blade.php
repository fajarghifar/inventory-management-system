<div>
    <x-modal name="customer-detail-modal" title="Customer Details">
        @if($customer)
            <div class="space-y-6">
                <!-- Header Info -->
                <div class="flex items-start justify-between border-b border-gray-200 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 tracking-tight">{{ $customer->name }}</h3>
                        <p class="text-sm text-gray-500">Customer Account Information</p>
                    </div>
                    <div class="inline-flex items-center rounded-full border border-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-700 bg-gray-50">
                        ID: #{{ $customer->id }}
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Email -->
                    <x-detail-item label="Email" :value="$customer->email ?: '-'">
                        <x-heroicon-o-envelope class="w-4 h-4 text-gray-400" />
                    </x-detail-item>

                    <!-- Phone -->
                    <x-detail-item label="Phone" :value="$customer->phone ?: '-'">
                        <x-heroicon-o-phone class="w-4 h-4 text-gray-400" />
                    </x-detail-item>

                    <!-- Registered At -->
                    <x-detail-item label="Registered At" :value="$customer->created_at->format('d M Y, H:i')" />
                </div>

                <div class="border-t border-gray-200 pt-6 space-y-6">
                    <!-- Address -->
                    <x-detail-item label="Address" :value="$customer->address ?: '-'" />

                    <!-- Notes -->
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium leading-none text-gray-500">
                            Notes
                        </label>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="text-sm text-gray-700 italic leading-relaxed">{{ $customer->notes ?: 'No additional notes.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', { name: 'customer-detail-modal' })">
                        Close
                    </x-button>
                    <x-button type="button" wire:click="edit">
                        <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                        Edit Customer
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
