<div class="space-y-6">
    <!-- Header Input Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
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

        <!-- Proof Image -->
        <div>
            <x-form-input type="file" name="proof_image" label="Proof of Receipt" wire:model="proof_image" accept="image/*" />
            <div class="mt-2">
                @if ($proof_image)
                    <img src="{{ $proof_image->temporaryUrl() }}" class="h-20 w-auto rounded border border-gray-200 object-cover">
                @elseif ($existing_proof_image)
                    <img src="{{ Storage::url($existing_proof_image) }}" class="h-20 w-auto rounded border border-gray-200 object-cover">
                @endif
            </div>
            @error('proof_image') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
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
    <div class="bg-white border border-gray-200 rounded-lg">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-medium text-gray-900">Items</h3>
            <span class="text-sm text-gray-500">Total Items: {{ count($items) }}</span>
        </div>

        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-3 w-1/3">Product</th>
                    <th class="px-4 py-3 w-24 text-center">Qty</th>
                    <th class="px-4 py-3 w-32 text-right">Buying Price</th>
                    <th class="px-4 py-3 w-32 text-right">Selling Price</th>
                    <th class="px-4 py-3 w-32 text-right">Subtotal</th>
                    <th class="px-4 py-3 w-12"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
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
                            <input type="number" wire:model.blur="items.{{ $index }}.quantity" min="1" class="w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-9 text-sm">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" wire:model.blur="items.{{ $index }}.unit_price" class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-9 text-sm">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" wire:model.blur="items.{{ $index }}.selling_price" class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-9 text-sm">
                            @if(((int)($item['selling_price'] ?? 0)) < ((int)($item['unit_price'] ?? 0)) && ((int)($item['selling_price'] ?? 0)) > 0)
                                <div class="text-xs text-amber-600 mt-1 flex items-center justify-end font-medium">
                                    <x-heroicon-s-exclamation-triangle class="w-3 h-3 mr-1" />
                                    Lower than buy price
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right font-medium text-gray-900">
                            Rp {{ number_format((int)$item['subtotal'], 0, ',', '.') }}
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
                    <td colspan="6" class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                        <button wire:click="addItem" type="button" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium flex items-center gap-1">
                            <x-heroicon-o-plus-circle class="w-5 h-5" />
                            Add Item
                        </button>
                    </td>
                </tr>
                <tr class="bg-gray-100">
                    <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-900">Total Purchase:</td>
                    <td class="px-4 py-3 text-right font-bold text-indigo-600 text-lg">
                        Rp {{ number_format((int)$this->total, 0, ',', '.') }}
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
        <x-button wire:click="save" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">Save Draft</span>
            <span wire:loading wire:target="save">Saving...</span>
        </x-button>
    </div>
</div>
