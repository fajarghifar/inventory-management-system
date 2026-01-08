<div class="space-y-6">
    <!-- Header Input Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
        <!-- Supplier -->
        <div wire:ignore>
            <x-form-searchable-select
                name="supplier_id"
                label="Supplier"
                :options="$suppliers"
                wire:model="supplier_id"
                optionValue="id"
                optionLabel="name"
                placeholder="Select Supplier"
                required
            />
        </div>

        <!-- Invoice (Optional) -->
        <div>
            <x-form-input name="invoice_number" label="Invoice Number (Optional)" wire:model="invoice_number" id="invoice_number" placeholder="Leave empty for drafts" />
        </div>

        <!-- Dates -->
        <div>
            <x-form-input name="purchase_date" label="Purchase Date" type="date" wire:model="purchase_date" id="purchase_date" required />
        </div>

        <div>
            <x-form-input name="due_date" label="Due Date" type="date" wire:model="due_date" id="due_date" />
        </div>

        <!-- Notes -->
        <div class="md:col-span-2">
            <x-form-textarea name="notes" label="Notes" wire:model="notes" id="notes" rows="2" placeholder="Additional notes..." />
        </div>
    </div>

    <!-- Items Section -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="font-medium text-gray-900 dark:text-gray-100">Items</h3>
            <span class="text-sm text-gray-500">Total Items: {{ count($items) }}</span>
        </div>

        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 w-1/3">Product</th>
                    <th class="px-4 py-3 w-24 text-center">Qty</th>
                    <th class="px-4 py-3 w-32 text-right">Price</th>
                    <th class="px-4 py-3 w-32 text-right">Subtotal</th>
                    <th class="px-4 py-3 w-12"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($items as $index => $item)
                    <tr wire:key="item-{{ $index }}">
                        <td class="px-4 py-2">
                            <div wire:ignore>
                                <x-form-searchable-select
                                    name="product_{{ $index }}"
                                    label=""
                                    :options="$products"
                                    wire:model.live="items.{{ $index }}.product_id"
                                    optionValue="id"
                                    optionLabel="name"
                                    placeholder="Select Product"
                                />
                            </div>
                            @error("items.{$index}.product_id") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" wire:model.live="items.{{ $index }}.quantity" min="1" class="w-full text-center border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm h-9 text-sm">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" wire:model.live="items.{{ $index }}.unit_price" class="w-full text-right border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm h-9 text-sm">
                        </td>
                        <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-gray-100">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <button wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <button wire:click="addItem" type="button" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium flex items-center gap-1">
                            <x-heroicon-o-plus-circle class="w-5 h-5" />
                            Add Item
                        </button>
                    </td>
                </tr>
                <tr class="bg-gray-100 dark:bg-gray-800">
                    <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-900 dark:text-gray-100">Total Purchase:</td>
                    <td class="px-4 py-3 text-right font-bold text-indigo-600 text-lg">
                        Rp {{ number_format($this->total, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-4 mt-6">
        <x-button variant="secondary" href="{{ route('purchases.index') }}">
            Cancel
        </x-button>
        <x-button wire:click="save" wire:loading.attr="disabled">
            <span wire:loading.remove>Save Draft</span>
            <span wire:loading>Saving...</span>
        </x-button>
    </div>
</div>
