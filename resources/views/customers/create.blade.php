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
                        <div class="space-y-2">
                            <label for="name" class="text-sm font-medium leading-none text-gray-700">
                                {{ __('Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                class="flex w-full h-10 px-3 py-2 text-sm bg-white border border-gray-300 rounded-md ring-offset-white file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="e.g. Acme Corp">
                            @error('name') <p class="text-sm font-medium text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Grid: Email & Phone -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Email -->
                            <div class="space-y-2">
                                <label for="email" class="text-sm font-medium leading-none text-gray-700">
                                    {{ __('Email') }} <span class="text-red-500">*</span>
                                </label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    class="flex w-full h-10 px-3 py-2 text-sm bg-white border border-gray-300 rounded-md ring-offset-white file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="contact@acme.com">
                                @error('email') <p class="text-sm font-medium text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Phone -->
                            <div class="space-y-2">
                                <label for="phone" class="text-sm font-medium leading-none text-gray-700">
                                    {{ __('Phone') }}
                                </label>
                                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                                    class="flex w-full h-10 px-3 py-2 text-sm bg-white border border-gray-300 rounded-md ring-offset-white file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="+1 (555) 000-0000">
                                @error('phone') <p class="text-sm font-medium text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="space-y-2">
                            <label for="address" class="text-sm font-medium leading-none text-gray-700">
                                {{ __('Address') }}
                            </label>
                            <textarea id="address" name="address" rows="3"
                                class="flex w-full min-h-[80px] px-3 py-2 text-sm bg-white border border-gray-300 rounded-md ring-offset-white placeholder:text-gray-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="Business full address">{{ old('address') }}</textarea>
                            @error('address') <p class="text-sm font-medium text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Notes -->
                         <div class="space-y-2">
                            <label for="notes" class="text-sm font-medium leading-none text-gray-700">
                                {{ __('Notes') }}
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                class="flex w-full min-h-[80px] px-3 py-2 text-sm bg-white border border-gray-300 rounded-md ring-offset-white placeholder:text-gray-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="Internal notes or memo...">{{ old('notes') }}</textarea>
                            @error('notes') <p class="text-sm font-medium text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                             <a href="{{ route('customers.index') }}" class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors bg-white border border-gray-200 rounded-md text-slate-900 hover:bg-gray-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium text-white transition-colors bg-slate-900 rounded-md hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                                {{ __('Save Customer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
