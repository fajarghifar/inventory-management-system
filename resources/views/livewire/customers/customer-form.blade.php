<div>
    <x-modal name="customer-modal" :title="$isEditing ? 'Edit Customer' : 'Add New Customer'">
        <form wire:submit="save" class="space-y-6">
            <!-- Grid: Name & Email -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Name -->
                <x-form-input
                    name="name"
                    label="Full Name"
                    placeholder="e.g. John Doe"
                    required
                    wire:model="name"
                />

                <!-- Email -->
                <x-form-input
                    type="email"
                    name="email"
                    label="Email"
                    placeholder="customer@example.com"
                    wire:model="email"
                />
            </div>

            <!-- Phone -->
            <x-form-input
                name="phone"
                label="Phone"
                placeholder="+62 812-3456-7890"
                wire:model="phone"
            />

            <!-- Address -->
            <x-form-textarea
                name="address"
                label="Address"
                placeholder="Full address"
                wire:model="address"
            />

            <!-- Notes -->
            <x-form-textarea
                name="notes"
                label="Notes"
                placeholder="Any additional notes..."
                wire:model="notes"
            />

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', { name: 'customer-modal' })">
                    Cancel
                </x-button>
                <x-button type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditing ? 'Update Customer' : 'Save Customer' }}
                    </span>
                    <span wire:loading wire:target="save">
                        Saving...
                    </span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
