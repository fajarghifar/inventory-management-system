<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customer Details') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-full mx-auto">
            <!-- Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">

                    <!-- Name -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-gray-500">
                            {{ __('Name') }}
                        </label>
                        <p class="text-base font-medium text-slate-900">{{ $customer->name }}</p>
                    </div>

                    <!-- Grid: Email & Phone -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Email -->
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-gray-500">
                                {{ __('Email') }}
                            </label>
                            <p class="text-sm text-slate-900">{{ $customer->email }}</p>
                        </div>

                        <!-- Phone -->
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-gray-500">
                                {{ __('Phone') }}
                            </label>
                            <p class="text-sm text-slate-900">{{ $customer->phone ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-gray-500">
                            {{ __('Address') }}
                        </label>
                        <p class="text-sm text-slate-900 whitespace-pre-line">{{ $customer->address ?? '-' }}</p>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-gray-500">
                            {{ __('Notes') }}
                        </label>
                        <p class="text-sm text-slate-900 whitespace-pre-line">{{ $customer->notes ?? '-' }}</p>
                    </div>

                    <!-- Timestamps -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 pt-4 border-t border-gray-100">
                        <div class="space-y-1">
                            <label class="text-xs font-medium leading-none text-gray-400">
                                {{ __('Registered At') }}
                            </label>
                            <p class="text-xs text-slate-500">{{ $customer->created_at->format('d M Y H:i') }}</p>
                        </div>
                         <div class="space-y-1">
                            <label class="text-xs font-medium leading-none text-gray-400">
                                {{ __('Last Updated') }}
                            </label>
                            <p class="text-xs text-slate-500">{{ $customer->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                         <a href="{{ route('customers.index') }}" class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors bg-white border border-gray-200 rounded-md text-slate-900 hover:bg-gray-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                            {{ __('Back to List') }}
                        </a>
                        <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium text-white transition-colors bg-slate-900 rounded-md hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            {{ __('Edit Customer') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
