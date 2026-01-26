@props([
    'name' => '',
    'label' => '',
    'options' => [],
    'value' => null,
    'placeholder' => 'Select option...',
    'required' => false,
    'disabled' => false,
    'url' => null,
    'inputClass' => 'h-10',
    'initialLabel' => '',
    'params' => [],
])

<div
    x-data="{
        open: false,
        query: @js($initialLabel),
        selected: @js($value),
        options: @js($options),
        isLoading: false,
        params: @js($params),
        init() {
            if (this.selected && this.options.length > 0) {
                const option = this.options.find(o => o.value == this.selected);
                if (option) this.query = option.label;
            }

            this.$watch('query', (value) => {
                if ('{{ $url }}' && value.length > 0) {
                    this.fetchOptions(value);
                }
            });

            // Watch for external param updates (if bound via Alpine)
        },
        async fetchOptions(search) {
            this.isLoading = true;
            try {
                const urlBase = '{{ $url }}';
                if (!urlBase) {
                    this.isLoading = false;
                    return;
                }

                // Merge props and legacy dataset params
                const type = this.$el.dataset.type || (this.params && this.params.type) || '';

                const queryParams = new URLSearchParams({
                    q: search,
                    type: type,
                    ...this.params
                });

                const res = await fetch(`${urlBase}?${queryParams.toString()}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (res.ok) {
                    this.options = await res.json();
                } else {
                    this.options = [];
                }
            } catch (e) {
                console.error('Fetch error:', e);
                this.options = [];
            }
            this.isLoading = false;
        },
        get filteredOptions() {
            if ('{{ $url }}') return this.options;
            if (this.query === '') return this.options;
            return this.options.filter(option =>
                option.label.toLowerCase().includes(this.query.toLowerCase())
            );
        },
        selectOption(option) {
            this.selected = option.value;
            this.query = option.label;
            this.open = false;
            this.$dispatch('option-selected', {
                name: '{{ $name }}',
                value: option.value,
                item: option
            });
        }
    }"
    x-modelable="selected"
    class="relative space-y-2"
    wire:ignore
    x-on:click.outside="open = false"
    {{ $attributes }}
>
    @if($label)
        <x-input-label :for="$name" :value="$label" :required="$required" />
    @endif

    <div class="relative">
        @if($name)
            <input type="hidden" name="{{ $name }}" :value="selected">
        @endif

        <input
            x-ref="input"
            type="text"
            x-model.debounce.400ms="query"
            x-on:focus="!{{ $disabled ? 'true' : 'false' }} && (open = true)"
            x-on:keydown.escape="open = false"
            placeholder="{{ $placeholder }}"
            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 {{ $inputClass }} {{ $name && $errors->has($name) ? 'border-red-500' : '' }}"
            {{ $disabled ? 'disabled' : '' }}
        />
        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <template x-if="isLoading">
                <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <template x-if="!isLoading">
                <x-heroicon-o-chevron-up-down class="w-5 h-5 text-muted-foreground" />
            </template>
        </div>
    </div>

    <!-- Dropdown (Absolute) -->
    <div
        wire:ignore
        x-show="open && (filteredOptions.length > 0 || isLoading)"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 w-full mt-1 bg-popover text-popover-foreground rounded-md shadow-lg max-h-60 overflow-auto border border-border"
        style="display: none;"
    >
        <ul class="py-1 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
            <template x-for="option in filteredOptions" :key="option.value">
                <li
                    x-on:click="selectOption(option)"
                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-accent hover:text-accent-foreground"
                >
                    <span x-text="option.label" class="block truncate" :class="{ 'font-semibold': selected == option.value, 'font-normal': selected != option.value }"></span>
                    <span x-show="selected == option.value" class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600">
                        <x-heroicon-o-check class="w-5 h-5" />
                    </span>
                </li>
            </template>
            <li x-show="!isLoading && filteredOptions.length === 0 && query.length > 0" class="relative cursor-default select-none py-2 pl-3 pr-9 text-gray-500">
                No results found.
            </li>
        </ul>
    </div>

    @if($name)
        <x-input-error :messages="$errors->get($name)" class="mt-2" />
    @endif
</div>
