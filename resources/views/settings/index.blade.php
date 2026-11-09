<x-app-layout title="Settings">
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Settings') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <x-secondary-button type="button" x-data x-on:click="$dispatch('load-default-settings')">
                    <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                    {{ __('Load Defaults') }}
                </x-secondary-button>
                <x-primary-button type="button" x-data x-on:click="$dispatch('create-setting')">
                    <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                    {{ __('Create Setting') }}
                </x-primary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <livewire:settings.setting-table />
        </div>
    </div>

    <livewire:settings.setting-form />
</x-app-layout>
