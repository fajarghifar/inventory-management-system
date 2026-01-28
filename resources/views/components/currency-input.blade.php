@props(['disabled' => false])

<div x-data="{
    model: @entangle($attributes->wire('model')),
    display: '',
    timeout: null,
    init() {
        this.display = this.format(this.model);
        this.$watch('model', value => {
            // Only update display if it's different (avoids loop when typing)
            if (this.unformat(this.display) != value) {
                this.display = this.format(value);
            }
        });
    },
    format(value) {
        if (value === null || value === '') return '';
        return new Intl.NumberFormat('id-ID').format(value);
    },
    unformat(value) {
        return value.replace(/[^0-9]/g, '');
    },
    update(event) {
        let value = event.target.value;
        // Keep only numbers
        let raw = this.unformat(value);

        // Debounce both Model and Format updates to prevent jumping cursor
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
            this.model = raw;
            // Only format after pause
            this.display = this.format(raw);
        }, 800);
    }
}"
class="w-full"
>
    <!-- We don't use x-model here to have better control via @input -->
    <input
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->whereDoesntStartWith('wire:model')->merge(['class' => 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50']) !!}
        type="text"
        :value="display"
        @input="update($event)"
        @blur="display = format(model)"
    />
</div>
