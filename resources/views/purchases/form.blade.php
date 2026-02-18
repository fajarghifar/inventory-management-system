<div class="space-y-6">
    <!-- Header Input Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <!-- Supplier -->
        <div class="space-y-2">
            <x-input-label for="supplier_id" :value="__('Supplier')" required />
            <div class="w-full">
                <select id="supplier_id" name="supplier_id"
                        x-init="initSupplierSelect($el)"
                        x-model="supplier_id"
                        autocomplete="off">
                    <option value=""></option>
                    @if(old('supplier_id'))
                        @php
                            $oldSupplier = \App\Models\Supplier::find(old('supplier_id'));
                        @endphp
                        @if($oldSupplier)
                            <option value="{{ $oldSupplier->id }}" selected>{{ $oldSupplier->name . ($oldSupplier->phone ? ' | ' . $oldSupplier->phone : '') }}</option>
                        @endif
                    @elseif(isset($purchase) && $purchase->supplier)
                        <option value="{{ $purchase->supplier_id }}" selected>{{ $purchase->supplier->name . ($purchase->supplier->phone ? ' | ' . $purchase->supplier->phone : '') }}</option>
                    @endif
                </select>
            </div>
            <x-input-error :messages="$errors->get('supplier_id')" />
        </div>

        <!-- Invoice (Optional) -->
        <div class="space-y-2">
            <x-input-label for="invoice_number" :value="__('Invoice Number (Optional)')" />
            <x-text-input
                id="invoice_number"
                type="text"
                name="invoice_number"
                :value="old('invoice_number', $purchase->invoice_number ?? '')"
                placeholder="Leave empty for drafts"
                class="block w-full"
            />
            <x-input-error :messages="$errors->get('invoice_number')" />
        </div>

        <!-- Proof Image -->
        <div class="space-y-2">
            <x-input-label for="proof_image" :value="__('Proof of Receipt')" />
            <input
                id="proof_image"
                type="file"
                name="proof_image"
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
                @if(isset($purchase) && $purchase->proof_image)
                    <img src="{{ Storage::url($purchase->proof_image) }}" class="h-20 w-auto rounded border border-gray-200 object-cover">
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
                    name="purchase_date"
                    :value="old('purchase_date', $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : date('Y-m-d'))"
                    class="block w-full"
                />
                <x-input-error :messages="$errors->get('purchase_date')" />
            </div>
            <div class="space-y-2">
                <x-input-label for="due_date" :value="__('Due Date')" />
                <x-text-input
                    id="due_date"
                    type="date"
                    name="due_date"
                    :value="old('due_date', $purchase->due_date ? \Carbon\Carbon::parse($purchase->due_date)->format('Y-m-d') : '')"
                    class="block w-full"
                />
                <x-input-error :messages="$errors->get('due_date')" />
            </div>
        </div>

         <!-- Status (Read Only) -->
         <div class="space-y-2">
            <x-input-label :value="__('Status')" />
            <div class="flex h-10 w-full items-center rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                {{ isset($purchase) && $purchase->status ? $purchase->status->label() : 'Draft (Default)' }}
            </div>
        </div>

        <!-- Notes -->
        <div class="md:col-span-2 space-y-2">
            <x-input-label for="notes" :value="__('Notes')" />
            <textarea
                id="notes"
                name="notes"
                rows="2"
                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                placeholder="Additional notes..."
            >{{ old('notes', $purchase->notes ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('notes')" />
        </div>
    </div>

    <!-- Items Section -->
    <div class="space-y-4">
        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
                {{ __('Items') }}
            </h3>
            <span class="text-sm text-muted-foreground" x-text="items.length + ' items'"></span>
        </div>

        <!-- Master Search -->
        <div class="w-full">
            <select
                id="master_product_search"
                x-init="initMasterSearch($el)"
                placeholder="Search and add product..."
                autocomplete="off"
            ></select>
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
                        <template x-for="(item, index) in items" :key="item.key">
                             <tr class="group hover:bg-gray-50/50">
                                <td class="px-4 py-2 align-top pt-3">
                                    <div class="w-full">
                                        <input
                                            type="text"
                                            :name="`items[${index}][product_name]`"
                                            x-model="item.product_name"
                                            class="flex h-10 w-full rounded-md border border-input bg-gray-50 px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                            readonly
                                        />
                                        <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                    </div>
                                </td>
                                <td class="px-4 py-2 align-top pt-3">
                                    <input
                                        type="number"
                                        :name="`items[${index}][quantity]`"
                                        x-model.number="item.quantity"
                                        @input="calculateLine(index)"
                                        min="1"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 text-center"
                                        placeholder="1"
                                    />
                                    <p x-show="hasError(`items.${index}.quantity`)" x-text="getError(`items.${index}.quantity`)" class="text-sm text-red-600 mt-1 space-y-1"></p>
                                </td>
                                <!-- Buy Price Currency Input -->
                                <td class="px-4 py-2 align-top pt-3" x-data="{
                                    display: '',
                                    init() {
                                        this.display = new Intl.NumberFormat('id-ID').format(item.unit_price || 0);
                                        this.$watch('item.unit_price', value => this.display = new Intl.NumberFormat('id-ID').format(value || 0));
                                    },
                                    update(e) {
                                        let raw = e.target.value.replace(/[^0-9]/g, '');
                                        item.unit_price = raw ? parseInt(raw) : 0;
                                        this.display = new Intl.NumberFormat('id-ID').format(item.unit_price);
                                        calculateLine(index);
                                    }
                                }">
                                    <input
                                        type="text"
                                        x-model="display"
                                        @input="update($event)"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 text-right"
                                        placeholder="0"
                                    />
                                    <input type="hidden" :name="`items[${index}][unit_price]`" :value="item.unit_price">
                                    <p x-show="hasError(`items.${index}.unit_price`)" x-text="getError(`items.${index}.unit_price`)" class="text-sm text-red-600 mt-1 space-y-1"></p>
                                </td>
                                <!-- Sell Price Currency Input -->
                                <td class="px-4 py-2 align-top pt-3" x-data="{
                                    display: '',
                                    init() {
                                        this.display = new Intl.NumberFormat('id-ID').format(item.selling_price || 0);
                                        this.$watch('item.selling_price', value => this.display = new Intl.NumberFormat('id-ID').format(value || 0));
                                    },
                                    update(e) {
                                        let raw = e.target.value.replace(/[^0-9]/g, '');
                                        item.selling_price = raw ? parseInt(raw) : 0;
                                        this.display = new Intl.NumberFormat('id-ID').format(item.selling_price);
                                    }
                                }">
                                    <input
                                        type="text"
                                        x-model="display"
                                        @input="update($event)"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 text-right"
                                        placeholder="0"
                                    />
                                    <input type="hidden" :name="`items[${index}][selling_price]`" :value="item.selling_price">
                                    <p x-show="hasError(`items.${index}.selling_price`)" x-text="getError(`items.${index}.selling_price`)" class="text-sm text-red-600 mt-1 space-y-1"></p>
                                    <template x-if="(parseInt(item.selling_price) || 0) < (parseInt(item.unit_price) || 0) && (parseInt(item.selling_price) || 0) > 0">
                                        <div class="text-xs text-amber-600 mt-1 flex items-center justify-end font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 mr-1">
                                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                            Low margin
                                        </div>
                                    </template>
                                </td>
                                <td class="px-4 py-2 text-right font-medium text-gray-900 align-top pt-4">
                                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format( parseInt(item.subtotal) || 0 )"></span>
                                </td>
                                <td class="px-4 py-2 text-center align-top pt-3">
                                    <button
                                        type="button"
                                        @click="removeItem(index)"
                                        class="text-muted-foreground hover:text-destructive transition-colors p-1 rounded-md hover:bg-gray-100"
                                        title="Remove Item"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="items.length === 0">
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">
                                    No items added. Search products above to add.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-gray-50/50 font-medium">
                        <!-- Removed the Add Button row -->
                        <tr class="bg-gray-100 border-t border-gray-200">
                            <td colspan="4" class="px-4 py-4 text-right font-bold text-gray-900 text-base flex-1">
                                {{ __('Total Purchase') }}:
                            </td>
                            <td class="px-4 py-4 text-right font-bold text-primary text-xl whitespace-nowrap">
                                <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(total)"></span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-x-4 pt-6 border-t border-gray-200">
        <a href="{{ route('purchases.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
            {{ __('Cancel') }}
        </a>

        <x-primary-button class="flex items-center gap-2" ::disabled="loading">
            <svg x-show="loading" class="animate-spin -ml-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="loading ? 'Processing...' : ({{ isset($purchase->id) ? '`Update Purchase`' : '`Create Purchase`' }})"></span>
        </x-primary-button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('purchaseForm', (initialData) => ({
            items: (initialData.items || []).map(i => ({
                ...i,
                key: i.key || Math.random().toString(36).substr(2, 9),
                subtotal: parseInt(i.subtotal) || 0
            })),
            supplier_id: initialData.supplier_id || '',
            status: initialData.status || 'draft',
            loading: false,
            errors: initialData.errors || {},

            init() {
                // Initialize checks or other logic if needed
            },

            hasError(field) {
                return !!this.errors[field];
            },

            getError(field) {
                return this.errors[field] ? this.errors[field][0] : '';
            },

            submitForm(e) {
                if (this.loading) return;
                this.loading = true;
                e.target.submit();
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },

            calculateLine(index) {
                let item = this.items[index];
                let qty = parseInt(item.quantity);
                let price = parseInt(item.unit_price);

                // Ensure no NaN
                qty = isNaN(qty) ? 0 : qty;
                price = isNaN(price) ? 0 : price;

                item.subtotal = qty * price;
            },

            get total() {
                return this.items.reduce((sum, item) => {
                    let sub = parseInt(item.subtotal);
                    return sum + (isNaN(sub) ? 0 : sub);
                }, 0);
            },

            // Helper for TomSelect
            waitForTomSelect(callback) {
                if (window.TomSelect) {
                    callback();
                } else {
                    setTimeout(() => this.waitForTomSelect(callback), 50);
                }
            },

            initSupplierSelect(el) {
                let self = this;
                this.waitForTomSelect(() => {
                    new TomSelect(el, {
                        placeholder: 'Select Supplier...',
                        preload: 'focus',
                        valueField: 'value',
                        labelField: 'text',
                        searchField: 'text',
                        onChange: function(value) {
                            self.supplier_id = value;
                        },
                        load: function(query, callback) {
                            var url = '{{ route("ajax.suppliers.search") }}';

                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ q: query })
                            })
                                .then(response => response.json())
                                .then(json => {
                                    callback(json);
                                }).catch(() => {
                                    callback();
                                });
                        }
                    });
                });
            },

            // Callback to add product from Master Search
            addProduct(product) {
                let existingIndex = this.items.findIndex(i => i.product_id == product.value);

                if (existingIndex !== -1) {
                    this.items[existingIndex].quantity += 1;
                    this.calculateLine(existingIndex);

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: 'Product already exists. Quantity updated.',
                            type: 'info'
                        }
                    }));
                } else {
                    this.items.push({
                        key: Math.random().toString(36).substr(2, 9),
                        product_id: product.value,
                        product_name: product.text,
                        quantity: 1,
                        unit_price: product.price || 0,
                        selling_price: product.selling_price || 0,
                        subtotal: (product.price || 0) * 1
                    });

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: 'Product "' + product.text + '" added to list.',
                            type: 'success'
                        }
                    }));
                }
            },

            initMasterSearch(el) {
                let self = this;
                this.waitForTomSelect(() => {
                    let ts = new TomSelect(el, {
                        placeholder: 'Search Product to Add...',
                        preload: 'focus',
                        valueField: 'value',
                        labelField: 'text',
                        searchField: 'text',
                        closeAfterSelect: false,
                        openOnFocus: true,
                        load: function(query, callback) {
                            var url = '{{ route("ajax.products.search") }}';

                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ q: query })
                            })
                            .then(response => response.json())
                            .then(json => {
                                callback(json);
                            }).catch(() => {
                                callback();
                            });
                        },
                        onItemAdd: function(value, item) {
                            let data = this.options[value];
                            if (data) {
                                self.addProduct(data);
                            }
                            this.clear(true);
                            this.focus();
                        }
                    });
                });
            }
        }));
    });
</script>
@endpush
