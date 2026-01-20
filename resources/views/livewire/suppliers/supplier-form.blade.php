<div>
    <x-modal name="supplier-modal" :title="$isEditing ? 'Edit Supplier' : 'Add New Supplier'">
        <form wire:submit="save" class="space-y-6">
            <!-- Grid: Name & Contact Person -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Name -->
                <x-form-input
                    name="name"
                    label="Company Name"
                    placeholder="e.g. PT Example Supplier"
                    required
                    wire:model="name"
                />

                <!-- Contact Person -->
                <x-form-input
                    name="contact_person"
                    label="Contact Person"
                    placeholder="e.g. Budi Santoso"
                    required
                    wire:model="contact_person"
                />
            </div>

            <!-- Grid: Email & Phone -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Email -->
                <x-form-input
                    type="email"
                    name="email"
                    label="Email"
                    placeholder="supplier@example.com"
                    wire:model="email"
                />

                <!-- Phone -->
                <x-form-input
                    name="phone"
                    label="Phone"
                    placeholder="+62 812-3456-7890"
                    wire:model="phone"
                />
            </div>

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
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', { name: 'supplier-modal' })">
                    Cancel
                </x-button>
                <x-button type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditing ? 'Update Supplier' : 'Save Supplier' }}
                    </span>
                    <span wire:loading wire:target="save">
                        Saving...
                    </span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
