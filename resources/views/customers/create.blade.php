<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Customer') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-full mx-auto">
            <!-- Form Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('customers.store') }}" class="space-y-6">
                        @csrf

                        <!-- Name -->
                        <x-form-input
                            name="name"
                            label="{{ __('Name') }}"
                            placeholder="e.g. Acme Corp"
                            required
                            autofocus
                        />

                        <!-- Grid: Email & Phone -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Email -->
                            <x-form-input
                                type="email"
                                name="email"
                                label="{{ __('Email') }}"
                                placeholder="contact@acme.com"
                                required
                            />

                            <!-- Phone -->
                            <x-form-input
                                name="phone"
                                label="{{ __('Phone') }}"
                                placeholder="+1 (555) 000-0000"
                            />
                        </div>

                        <!-- Address -->
                        <x-form-textarea
                            name="address"
                            label="{{ __('Address') }}"
                            placeholder="Business full address"
                        />

                        <!-- Notes -->
                        <x-form-textarea
                            name="notes"
                            label="{{ __('Notes') }}"
                            placeholder="Internal notes or memo..."
                        />

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                            <x-button variant="secondary" href="{{ route('customers.index') }}">
                                {{ __('Cancel') }}
                            </x-button>
                            <x-button>
                                {{ __('Save Customer') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
