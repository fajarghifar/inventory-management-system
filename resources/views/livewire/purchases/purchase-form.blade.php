<div class="space-y-6">
    <!-- Header Input Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <!-- Supplier -->
        <div class="space-y-2">
            <x-input-label for="supplier_id" :value="__('Supplier')" required />
            <div wire:ignore>
                <x-tom-select
                    name="supplier_id"
                    wire:model="supplier_id"
                    :url="route('ajax.suppliers.search')"
                    placeholder="Select Supplier"
                    data-initial-label="{{ $this->supplierName }}"
                />
            </div>
            <x-input-error :messages="$errors->get('supplier_id')" />
        </div>

        <!-- Invoice (Optional) -->
        <div class="space-y-2">
            <x-input-label for="invoice_number" :value="__('Invoice Number (Optional)')" />
            <x-text-input
                id="invoice_number"
                type="text"
                wire:model="invoice_number"
                placeholder="Leave empty for drafts"
            />
            <x-input-error :messages="$errors->get('invoice_number')" />
        </div>

        <!-- Proof Image -->
        <div class="space-y-2">
            <x-input-label for="proof_image" :value="__('Proof of Receipt')" />
            <input
                id="proof_image"
                type="file"
                wire:model="proof_image"
                accept="image/*"
                class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100"
            />
            <x-input-error :messages="$errors->get('proof_image')" />

            <div class="mt-2">
                @if ($proof_image)
                    <img src="{{ $proof_image->temporaryUrl() }}" class="h-20 w-auto rounded border border-gray-200 object-cover">
                @elseif ($existing_proof_image)
                    <img src="{{ Storage::url($existing_proof_image) }}" class="h-20 w-auto rounded border border-gray-200 object-cover">
                @endif
            </div>
        </div>

        <!-- Dates -->
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
                <x-input-label for="purchase_date" :value="__('Purchase Date')" required />
                <x-text-input
                    id="purchase_date"
                    type="date"
                    wire:model="purchase_date"
                />
                <x-input-error :messages="$errors->get('purchase_date')" />
            </div>
            <div class="space-y-2">
                <x-input-label for="due_date" :value="__('Due Date')" />
                <x-text-input
                    id="due_date"
                    type="date"
                    wire:model="due_date"
                />
                <x-input-error :messages="$errors->get('due_date')" />
            </div>
        </div>

        <!-- Notes -->
        <div class="md:col-span-2 space-y-2">
            <x-input-label for="notes" :value="__('Notes')" />
            <textarea
                id="notes"
                wire:model="notes"
                rows="2"
                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                placeholder="Additional notes..."
            ></textarea>
            <x-input-error :messages="$errors->get('notes')" />
        </div>
    </div>

    <!-- Items Section -->
    <div class="space-y-4">
        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
                {{ __('Items') }}
            </h3>
            <span class="text-sm text-muted-foreground">{{ count($items) }} items</span>
        </div>

        <div class="rounded-md border border-gray-200 overflow-visible">
            <div class="overflow-x-auto md:overflow-visible">
                <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 min-w-[250px] font-medium">{{ __('Product') }}</th>
                        <th class="px-4 py-3 w-28 text-center font-medium">{{ __('Qty') }}</th>
                        <th class="px-4 py-3 w-40 text-right font-medium">{{ __('Buy Price') }}</th>
                        <th class="px-4 py-3 w-40 text-right font-medium">{{ __('Sell Price') }}</th>
                        <th class="px-4 py-3 w-40 text-right font-medium">{{ __('Subtotal') }}</th>
                        <th class="px-4 py-3 w-12 text-center"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($items as $index => $item)
                        <tr wire:key="item-{{ $index }}" class="group hover:bg-gray-50/50">
                            <td class="px-4 py-2 align-top pt-3">
                                <div wire:ignore>
                                    <x-tom-select
                                        name="product_{{ $index }}"
                                        wire:model="items.{{ $index }}.product_id"
                                        :url="route('ajax.products.search')"
                                        placeholder="Select Product"
                                        data-initial-label="{{ $this->getProductName($index) }}"
                                    />
                                </div>
                                @error("items.{$index}.product_id")
                                    <span class="text-destructive text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="px-4 py-2 align-top pt-3">
                                <x-text-input
                                    type="number"
                                    wire:model.blur="items.{{ $index }}.quantity"
                                    min="1"
                                    class="text-center w-full"
                                />
                                @error("items.{$index}.quantity")
                                    <span class="text-destructive text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="px-4 py-2 align-top pt-3">
                                <x-currency-input
                                    wire:model.live.debounce.1000ms="items.{{ $index }}.unit_price"
                                    class="text-right w-full"
                                />
                                @error("items.{$index}.unit_price")
                                    <span class="text-destructive text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="px-4 py-2 align-top pt-3">
                                <x-currency-input
                                    wire:model.blur="items.{{ $index }}.selling_price"
                                    class="text-right w-full"
                                />
                                @if(((int)($item['selling_price'] ?? 0)) < ((int)($item['unit_price'] ?? 0)) && ((int)($item['selling_price'] ?? 0)) > 0)
                                    <div class="text-xs text-amber-600 mt-1 flex items-center justify-end font-medium">
                                        <x-heroicon-s-exclamation-triangle class="w-3 h-3 mr-1" />
                                        Low margin
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right font-medium text-gray-900 align-top pt-4">
                                Rp {{ number_format((int)$item['subtotal'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-center align-top pt-3">
                                <button
                                    wire:click="removeItem({{ $index }})"
                                    class="text-muted-foreground hover:text-destructive transition-colors p-1 rounded-md hover:bg-gray-100"
                                    title="Remove Item"
                                >
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50/50 font-medium">
                    <tr>
                        <td colspan="6" class="px-4 py-3 border-t border-gray-200">
                            <button
                                wire:click="addItem"
                                type="button"
                                class="text-sm text-primary hover:text-primary/90 font-medium flex items-center gap-1 transition-colors"
                            >
                                <x-heroicon-o-plus-circle class="w-5 h-5" />
                                {{ __('Add Product Item') }}
                            </button>
                        </td>
                    </tr>
                    <tr class="bg-gray-100 border-t border-gray-200">
                        <td colspan="4" class="px-4 py-4 text-right font-bold text-gray-900 text-base flex-1">
                            {{ __('Total Purchase') }}:
                        </td>
                        <td class="px-4 py-4 text-right font-bold text-primary text-xl whitespace-nowrap">
                            Rp {{ number_format((int)$this->total, 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-x-4 pt-6 border-t border-gray-200">
        <x-secondary-button href="{{ route('purchases.index') }}" wire:navigate>
            {{ __('Cancel') }}
        </x-secondary-button>

        <x-primary-button wire:click="save" wire:loading.attr="disabled">
            <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ $purchaseId ? __('Update Purchase') : __('Create Purchase') }}
        </x-primary-button>
    </div>
</div>
