@props(['name', 'label', 'options', 'placeholder' => 'Select an option', 'optionValue' => 'id', 'optionLabel' => 'name', 'required' => false])

<div x-data="{
    open: false,
    search: '',
    selected: @entangle($attributes->wire('model')),
    options: {{ json_encode($options) }},
    get filteredOptions() {
        if (this.search === '') {
            return this.options;
        }
        return this.options.filter(option => {
            return String(option['{{ $optionLabel }}']).toLowerCase().includes(this.search.toLowerCase());
        });
    },
    get selectedLabel() {
        if (!this.selected) return null;
        const found = this.options.find(o => o['{{ $optionValue }}'] == this.selected);
        return found ? found['{{ $optionLabel }}'] : null;
    },
    selectOption(value) {
        this.selected = value;
        this.open = false;
        this.search = '';
    },
    closeDropdown() {
        this.open = false;
    }
}"
class="relative w-full"
@click.outside="closeDropdown()"
>
    <!-- Label -->
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
    </label>

    <!-- Trigger Button -->
    <button type="button"
        @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
        class="relative w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
    >
        <span class="block truncate" x-text="selectedLabel ? selectedLabel : '{{ $placeholder }}'" :class="{'text-gray-900 dark:text-gray-300': selectedLabel, 'text-gray-500': !selectedLabel}"></span>
        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="open"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-900 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        style="display: none;"
    >
        <!-- Search Input -->
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 px-3 py-2 border-b border-gray-200 dark:border-gray-700">
            <input
                x-ref="searchInput"
                type="text"
                x-model="search"
                class="block w-full border-gray-300 dark:border-gray-600 rounded-md sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-300"
                placeholder="Search..."
            >
        </div>

        <!-- Options List -->
        <ul class="pt-1">
            <template x-for="option in filteredOptions" :key="option['{{ $optionValue }}']">
                <li
                    @click="selectOption(option['{{ $optionValue }}'])"
                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600"
                    :class="{'text-white bg-indigo-600': selected == option['{{ $optionValue }}'], 'text-gray-900 dark:text-gray-300': selected != option['{{ $optionValue }}']}"
                >
                    <span class="block truncate" x-text="option['{{ $optionLabel }}']" :class="{'font-semibold': selected == option['{{ $optionValue }}'], 'font-normal': selected != option['{{ $optionValue }}']}"></span>

                    <span x-show="selected == option['{{ $optionValue }}']" class="absolute inset-y-0 right-0 flex items-center pr-4" :class="{'text-white': selected == option['{{ $optionValue }}'], 'text-indigo-600': selected != option['{{ $optionValue }}']}">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            </template>
            <li x-show="filteredOptions.length === 0" class="py-2 pl-3 pr-9 text-gray-500 dark:text-gray-400">
                No results found.
            </li>
        </ul>
    </div>
    @error($name) <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
</div>
