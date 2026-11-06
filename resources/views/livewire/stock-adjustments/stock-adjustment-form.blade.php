<x-modal name="stock-adjustment-modal" :title="''" maxWidth="2xl">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                Record Stock Adjustment
            </h3>
            <p class="text-sm text-muted-foreground">
                Document inventory changes due to damage, loss, shrinkage, expiration, or physical count corrections.
            </p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <!-- Product Selector -->
            <div>
                <x-input-label for="product_id" :value="__('Product')" />
                <select
                    id="product_id"
                    wire:model.live="product_id"
                    class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    required
                >
                    <option value="">-- Select Product --</option>
                    @foreach(\App\Models\Product::orderBy('name')->get() as $prod)
                        <option value="{{ $prod->id }}">
                            {{ $prod->name }} (SKU: {{ $prod->sku }}) — Current Stock: {{ $prod->quantity }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('product_id')" />
            </div>

            @if($product_id)
                <div class="p-3 bg-muted/50 rounded-md text-sm border flex items-center justify-between">
                    <div>
                        <span class="text-muted-foreground">Selected Item:</span>
                        <strong class="text-foreground ml-1">{{ $productName }}</strong>
                    </div>
                    <div>
                        <span class="text-muted-foreground">On Hand:</span>
                        <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $currentStock > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                            {{ $currentStock }} units
                        </span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Type -->
                <div class="space-y-2">
                    <x-input-label for="type" :value="__('Adjustment Type')" />
                    <select
                        id="type"
                        wire:model="type"
                        class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        <option value="reduction">Reduction (-) — Decrease Stock</option>
                        <option value="addition">Addition (+) — Increase Stock</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" />
                </div>

                <!-- Reason -->
                <div class="space-y-2">
                    <x-input-label for="reason" :value="__('Reason')" />
                    <select
                        id="reason"
                        wire:model="reason"
                        class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        @foreach(\App\Enums\StockAdjustmentReason::cases() as $r)
                            <option value="{{ $r->value }}">{{ $r->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('reason')" />
                </div>
            </div>

            <!-- Quantity -->
            <div class="space-y-2">
                <x-input-label for="quantity" :value="__('Quantity to Adjust')" />
                <x-text-input
                    id="quantity"
                    type="number"
                    min="1"
                    wire:model="quantity"
                    class="w-full"
                    required
                />
                <x-input-error :messages="$errors->get('quantity')" />
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <x-input-label for="notes" :value="__('Notes / Remarks (Optional)')" />
                <textarea
                    id="notes"
                    wire:model="notes"
                    rows="3"
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    placeholder="Provide details about the damage, count discrepancy, or approval reference..."
                ></textarea>
                <x-input-error :messages="$errors->get('notes')" />
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'stock-adjustment-modal' })">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button type="submit" wire:loading.attr="disabled">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="w-4 h-4 mr-2" />
                    {{ __('Confirm Adjustment') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

