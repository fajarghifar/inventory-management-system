<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Supplier') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-full mx-auto">
            <!-- Form Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-6">
                        @csrf

                        <!-- Grid: Name & Contact Person -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Name -->
                            <x-form-input
                                name="name"
                                label="{{ __('Company Name') }}"
                                placeholder="e.g. PT Example Supplier"
                                required
                                autofocus
                            />

                            <!-- Contact Person -->
                            <x-form-input
                                name="contact_person"
                                label="{{ __('Contact Person') }}"
                                placeholder="e.g. Budi Santoso"
                                required
                            />
                        </div>

                        <!-- Grid: Email & Phone -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Email -->
                            <x-form-input
                                type="email"
                                name="email"
                                label="{{ __('Email') }}"
                                placeholder="supplier@example.com"
                                required
                            />

                            <!-- Phone -->
                            <x-form-input
                                name="phone"
                                label="{{ __('Phone') }}"
                                placeholder="+62 812-3456-7890"
                            />
                        </div>

                        <!-- Address -->
                        <x-form-textarea
                            name="address"
                            label="{{ __('Address') }}"
                            placeholder="Full address"
                        />

                        <!-- Notes -->
                        <x-form-textarea
                            name="notes"
                            label="{{ __('Notes') }}"
                            placeholder="Any additional notes..."
                        />

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                            <x-button variant="secondary" href="{{ route('suppliers.index') }}">
                                {{ __('Cancel') }}
                            </x-button>
                            <x-button>
                                {{ __('Save Supplier') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
