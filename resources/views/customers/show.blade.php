<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customer Details') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-full mx-auto">
            <!-- Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 md:p-8 bg-white/50 space-y-8">

                    <header class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $customer->name }}</h3>
                                <p class="text-sm text-gray-500">Customer Profile</p>
                            </div>
                            <div class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200">
                                ID: #{{ $customer->id }}
                            </div>
                    </header>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Email -->
                        <x-detail-item label="{{ __('Email') }}" value="{{ $customer->email }}">
                            <x-heroicon-o-envelope class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Phone -->
                        <x-detail-item label="{{ __('Phone') }}" value="{{ $customer->phone ?? '-' }}">
                            <x-heroicon-o-phone class="w-4 h-4 text-gray-400" />
                        </x-detail-item>
                    </div>

                    <div class="border-t border-gray-100 pt-6 space-y-6">
                        <!-- Address -->
                        <x-detail-item label="{{ __('Address') }}" value="{{ $customer->address ?? '-' }}" />

                        <!-- Notes -->
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-gray-500">
                                {{ __('Notes') }}
                            </label>
                            <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                <p class="text-sm text-slate-700 italic leading-relaxed">{{ $customer->notes ?? 'No additional notes.' }}</p>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 pt-2">
                            <x-detail-item label="{{ __('Registered At') }}" value="{{ $customer->created_at->format('d M Y H:i') }}" />
                            <x-detail-item label="{{ __('Last Updated') }}" value="{{ $customer->updated_at->format('d M Y H:i') }}" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                        <x-button variant="secondary" href="{{ route('customers.index') }}">
                            {{ __('Back to List') }}
                        </x-button>
                        <x-button href="{{ route('customers.edit', $customer) }}">
                            <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                            {{ __('Edit Customer') }}
                        </x-button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
