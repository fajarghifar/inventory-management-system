<x-app-layout title="POS">
    <div class="mx-auto sm:px-6 lg:px-8 py-4"
         x-data="pos()"
         x-init="init()"
         @keydown.window.f1.prevent="productTs && productTs.focus()"
         @keydown.window.f2.prevent="customerTs && customerTs.focus()"
         @keydown.window.f3.prevent="openConfirmation()"
         @keydown.window.f4.prevent="openCustomerModal()"
    >
        <div class="flex flex-col lg:flex-row h-[calc(100vh-100px)] space-y-4 lg:space-y-0 lg:space-x-4 relative">

            <!-- Left Side: Transaction Details (70%) -->
            <div class="w-full lg:w-[70%] flex flex-col space-y-4 h-full">
                <!-- Search Bar (TomSelect) -->
                <div class="relative z-20 mb-2">
                    <select
                        x-ref="productSelect"
                        placeholder="Search Products (Name or SKU) [F1]..."
                        autocomplete="off"
                    ></select>
                </div>

                <!-- Cart Table -->
                <div class="flex-1 bg-white rounded-lg shadow border border-gray-200 overflow-hidden flex flex-col">
                    <div class="overflow-x-auto flex-1">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Disc/Unit</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="(item, index) in cart" :key="item.id">
                                    <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'" class="hover:bg-indigo-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900" x-text="item.name"></div>
                                            <div class="text-xs text-gray-500" x-text="item.sku"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500" x-text="formatCurrency(item.price)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center">
                                                <input type="number" x-model="item.quantity" min="1" :max="item.max_stock"
                                                    @input="validateQty(index)"
                                                    class="w-20 text-center border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm"
                                                    placeholder="1">
                                            </div>
                                            <div x-show="item.quantity > item.max_stock" class="text-xs text-red-600 mt-1">
                                                Max: <span x-text="item.max_stock"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500" x-text="item.unit"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="relative rounded-md shadow-sm w-32 ml-auto">
                                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-xs">Rp</span>
                                                </div>
                                                <input
                                                    type="text"
                                                    :value="formatNumber(item.discount)"
                                                    @input="item.discount = unformatNumber($event.target.value)"
                                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-8 pr-2 sm:text-sm border-gray-300 rounded-md text-right"
                                                    placeholder="0"
                                                >
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900" x-text="formatCurrency((item.price - item.discount) * item.quantity)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <button @click="removeFromCart(index)" class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 focus:outline-none transition-colors mx-auto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="cart.length === 0">
                                    <tr>
                                        <td colspan="7" class="px-6 py-20 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                <p class="text-base font-medium">Cart is empty</p>
                                                <p class="text-sm text-gray-400">Search products above to start transaction</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Payment Details (30%) -->
            <div class="w-full lg:w-[30%] flex flex-col bg-white rounded-lg shadow border border-gray-200 h-full">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Payment Details</h2>
                </div>

                <div class="p-4 space-y-6 flex-1 overflow-y-auto">
                    <!-- Customer Section -->
                    <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100 relative group">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-indigo-500 uppercase">Customer</span>
                            <button @click="openCustomerModal()" class="text-[10px] font-semibold text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-200 bg-white px-2 py-1 rounded transition-colors flex items-center">
                                + New (F4)
                            </button>
                        </div>

                        <div class="relative">
                            <template x-if="selectedCustomer">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-900" x-text="selectedCustomer.name"></h3>
                                        <p class="text-sm text-gray-600" x-text="selectedCustomer.phone || 'No Phone'"></p>
                                    </div>
                                    <button @click="resetCustomer()" class="text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <div x-show="!selectedCustomer">
                                <select
                                    x-ref="customerSelect"
                                    placeholder="Search Customer [F2]..."
                                    autocomplete="off"
                                ></select>
                            </div>
                        </div>
                    </div>

                    <!-- Totals Section -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-gray-600 text-sm font-medium">
                            <span>Subtotal</span>
                            <span x-text="formatCurrency(subtotal)"></span>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                             <span class="text-sm font-medium text-gray-500">Discount (Global)</span>
                             <div class="relative w-32">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-xs">Rp</span>
                                </div>
                                <input
                                    type="text"
                                    :value="formatNumber(globalDiscount)"
                                    @input="globalDiscount = unformatNumber($event.target.value)"
                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-8 pr-2 sm:text-sm border-gray-300 rounded-md text-right py-1"
                                    placeholder="0"
                                >
                             </div>
                        </div>
                        <div class="flex justify-between text-red-500 text-sm" x-show="totalDiscount > 0">
                            <span>Total Discount</span>
                            <span x-text="'- ' + formatCurrency(totalDiscount)"></span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <span class="text-lg font-bold text-gray-800">TOTAL</span>
                            <span class="text-2xl font-extrabold text-blue-600" x-text="formatCurrency(total)"></span>
                        </div>
                    </div>

                    <!-- Payment Input -->
                    <div class="space-y-4 pt-4 border-t border-gray-200">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Payment Method</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    @click="payment.method = 'cash'"
                                    class="px-4 py-2 text-sm font-medium rounded-md border"
                                    :class="payment.method === 'cash' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                >
                                    CASH
                                </button>
                                <button
                                    @click="payment.method = 'transfer'"
                                    class="px-4 py-2 text-sm font-medium rounded-md border"
                                    :class="payment.method === 'transfer' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                >
                                    TRANSFER
                                </button>
                            </div>
                        </div>

                        <template x-if="payment.method === 'cash'">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cash Received</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-bold">Rp</span>
                                    </div>
                                    <input
                                        type="text"
                                        :value="formatNumber(payment.cash_received)"
                                        @input="payment.cash_received = unformatNumber($event.target.value)"
                                        class="block w-full pl-10 pr-3 py-3 text-lg font-bold text-gray-900 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="0"
                                    >
                                </div>

                                <!-- Quick Cash Buttons -->
                                <div class="grid grid-cols-4 gap-2 mt-2">
                                    <button @click="payment.cash_received = total" class="px-2 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-xs font-bold text-gray-700">
                                        EXACT
                                    </button>
                                    <button @click="payment.cash_received = (parseInt(payment.cash_received) || 0) + 100000" class="px-2 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-xs font-bold text-gray-700">
                                        +100K
                                    </button>
                                    <button @click="payment.cash_received = (parseInt(payment.cash_received) || 0) + 50000" class="px-2 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-xs font-bold text-gray-700">
                                        +50K
                                    </button>
                                    <button @click="payment.cash_received = (parseInt(payment.cash_received) || 0) + 20000" class="px-2 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-xs font-bold text-gray-700">
                                        +20K
                                    </button>
                                    <button @click="payment.cash_received = (parseInt(payment.cash_received) || 0) + 10000" class="px-2 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-xs font-bold text-gray-700">
                                        +10K
                                    </button>
                                    <button @click="payment.cash_received = (parseInt(payment.cash_received) || 0) + 5000" class="px-2 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-xs font-bold text-gray-700">
                                        +5K
                                    </button>
                                    <button @click="payment.cash_received = (parseInt(payment.cash_received) || 0) + 2000" class="px-2 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-xs font-bold text-gray-700">
                                        +2K
                                    </button>
                                    <button @click="payment.cash_received = (parseInt(payment.cash_received) || 0) + 1000" class="px-2 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-xs font-bold text-gray-700">
                                        +1K
                                    </button>
                                </div>
                                <div class="bg-green-50 p-3 rounded-md border border-green-100 flex justify-between items-center mt-2"
                                     :class="change < 0 ? 'bg-red-50 border-red-100 text-red-800' : 'bg-green-50 border-green-100 text-green-800'">
                                    <span class="text-sm font-medium uppercase" x-text="change < 0 ? 'Due' : 'Change'"></span>
                                    <span class="text-xl font-bold"
                                          :class="change < 0 ? 'text-red-700' : 'text-green-700'"
                                          x-text="formatCurrency(Math.abs(change))"></span>
                                </div>
                            </div>
                        </template>

                        <div>
                            <textarea
                                x-model="payment.notes"
                                rows="3"
                                class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400 py-2"
                                placeholder="Transaction Notes / Address..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-4 border-t border-gray-200 bg-gray-50 flex gap-3">
                    <button
                        @click="$dispatch('open-modal', { name: 'cancel-modal' })"
                        class="w-1/3 py-3 text-sm font-bold text-red-600 hover:text-white bg-white border border-red-200 hover:bg-red-600 rounded-lg flex items-center justify-center transition-colors shadow-sm"
                    >
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        CANCEL
                    </button>

                    <button
                        @click="openConfirmation()"
                        :disabled="isSubmitting || cart.length === 0"
                        class="w-2/3 flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <template x-if="isSubmitting">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <span x-text="isSubmitting ? 'Processing...' : 'PAY (F3)'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Alpine Component Logic -->
        <script>
            function pos() {
                return {
                    cart: [],
                    selectedCustomer: null,
                    payment: {
                        method: 'cash',
                        cash_received: 0,
                        notes: ''
                    },
                    globalDiscount: 0,
                    saleStatus: 'completed',
                    isSubmitting: false,

                    // TomSelect Instances
                    productTs: null,
                    customerTs: null,

                    lastSearchQuery: '',

                    init() {
                         // Load from LocalStorage
                        const savedCart = localStorage.getItem('pos_cart');
                        if (savedCart) this.cart = JSON.parse(savedCart);

                        const savedCustomer = localStorage.getItem('pos_customer');
                        if (savedCustomer) this.selectedCustomer = JSON.parse(savedCustomer);

                        const savedPayment = localStorage.getItem('pos_payment');
                        if (savedPayment) this.payment = JSON.parse(savedPayment);

                        const savedGlobalDiscount = localStorage.getItem('pos_globalDiscount');
                        if (savedGlobalDiscount) this.globalDiscount = parseInt(savedGlobalDiscount);

                        // Watchers
                        this.$watch('cart', (val) => localStorage.setItem('pos_cart', JSON.stringify(val)));
                        this.$watch('selectedCustomer', (val) => localStorage.setItem('pos_customer', JSON.stringify(val)));
                        this.$watch('payment', (val) => localStorage.setItem('pos_payment', JSON.stringify(val)));
                        this.$watch('globalDiscount', (val) => localStorage.setItem('pos_globalDiscount', val));

                        this.initProductSelect();
                        this.initCustomerSelect();
                    },

                    initProductSelect() {
                        if (!this.$refs.productSelect) return;

                        if (this.productTs) {
                            this.productTs.destroy();
                            this.productTs = null;
                        }

                        this.productTs = new TomSelect(this.$refs.productSelect, {
                            valueField: 'id',
                            labelField: 'name',
                            searchField: ['name', 'sku'],
                            closeAfterSelect: false,
                            openOnFocus: true,
                            preload: 'focus', // UX Improvement
                            load: (query, callback) => {
                                this.lastSearchQuery = query;

                                fetch('{{ route("ajax.products.search") }}', {
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
                                    }).catch(()=>{
                                        callback();
                                    });
                            },
                            render: {
                                option: (item, escape) => {
                                    return `
                                        <div class="py-2 px-3 border-b border-gray-100">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <div class="font-medium text-gray-900">${escape(item.name)}</div>
                                                    <div class="text-xs text-gray-500">${escape(item.sku)}</div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-bold text-indigo-600">${this.formatCurrency(item.selling_price)}</div>
                                                    <div class="text-xs ${item.quantity > 0 ? 'text-green-600' : 'text-red-600'}">
                                                        Stock: ${escape(item.quantity)} ${escape(item.unit?.symbol || '')}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                },
                                item: (item, escape) => {
                                    return `<div>${escape(item.name)}</div>`;
                                }
                            },
                            onChange: (value) => {
                                if (value) {
                                    const item = this.productTs.options[value];
                                    if(item) {
                                        this.addToCart(item);

                                        // Improved Logic: Prevent double input on Enter
                                        const cleanup = () => {
                                            this.productTs.clear(true);
                                            this.productTs.setTextboxValue(this.lastSearchQuery);
                                            this.productTs.refreshOptions(false);
                                        };

                                        // Small delay to ensure TS internal "Enter" handling is done
                                        setTimeout(cleanup, 10);
                                    }
                                }
                            }
                        });
                    },

                    initCustomerSelect() {
                        if (!this.$refs.customerSelect) return;

                        if (this.customerTs) {
                            this.customerTs.destroy();
                            this.customerTs = null;
                        }

                        this.customerTs = new TomSelect(this.$refs.customerSelect, {
                            valueField: 'value',
                            labelField: 'text',
                            searchField: 'text',
                            preload: 'focus', // UX Improvement
                            openOnFocus: true, // UX Improvement
                            load: (query, callback) => {
                                var url = '{{ route("ajax.customers.search") }}';

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
                                    }).catch(()=>{
                                        callback();
                                    });
                            },
                            render: {
                                option: (item, escape) => {
                                    return `
                                        <div class="py-2 px-3 hover:bg-indigo-50">
                                            <div class="font-medium text-gray-900">${escape(item.name)}</div>
                                            <div class="text-xs text-gray-500">${escape(item.phone || 'No Phone')}</div>
                                        </div>
                                    `;
                                }
                            },
                            onChange: (value) => {
                                if (value) {
                                    const item = this.customerTs.options[value];
                                    if(item) {
                                        // Update selected customer object structure to match what UI expects
                                        // The UI expects { id, name, phone, ... }
                                        // Our controller returns { value, text, name, phone }
                                        // So we map value to id for consistency if needed, or just use item.
                                        this.selectedCustomer = {
                                            id: item.value,
                                            name: item.name,
                                            phone: item.phone,
                                            // email and address are not returned by search currently.
                                            // If needed we should add them to controller.
                                            email: item.email || '',
                                            address: item.address || ''
                                        };
                                        this.customerTs.clear();
                                    }
                                }
                            }
                        });
                    },

                    resetCustomer() {
                        this.selectedCustomer = null;
                        this.$nextTick(() => {
                            this.customerTs && this.customerTs.focus();
                        });
                    },

                    clearStorage() {
                        localStorage.removeItem('pos_cart');
                        localStorage.removeItem('pos_customer');
                        localStorage.removeItem('pos_payment');
                        localStorage.removeItem('pos_globalDiscount');
                    },

                    // Cart Management
                    addToCart(product) {
                        const existing = this.cart.find(item => item.id === product.id);
                        if (existing) {
                            if (existing.quantity < product.quantity) {
                                existing.quantity++;
                                this.$dispatch('toast', { message: 'Product already exists. Quantity updated.', type: 'info' });
                            } else {
                                this.$dispatch('toast', { message: 'Insufficient stock!', type: 'error' });
                            }
                        } else {
                            if (product.quantity > 0) {
                                this.cart.push({
                                    id: product.id,
                                    name: product.name,
                                    sku: product.sku,
                                    price: product.selling_price,
                                    quantity: 1,
                                    max_stock: product.quantity,
                                    unit: product.unit ? product.unit.symbol : '',
                                    discount: 0
                                });
                                this.$dispatch('toast', { message: 'Product "' + product.name + '" added to cart.', type: 'success' });
                            } else {
                                this.$dispatch('toast', { message: 'Out of Stock!', type: 'error' });
                            }
                        }
                    },

                    validateQty(index) {
                        const item = this.cart[index];
                        if (item.quantity > item.max_stock) {
                            item.quantity = item.max_stock;
                            this.$dispatch('toast', { message: 'Maksimum stok tercapai', type: 'warning' });
                        }
                        if (item.quantity < 1) item.quantity = 1;
                    },

                    removeFromCart(index) {
                        const removedItem = this.cart[index];
                        this.cart.splice(index, 1);
                        this.$dispatch('toast', { message: 'Product "' + removedItem.name + '" removed from cart.', type: 'info' });
                    },

                    // Customer Modal Open
                    openCustomerModal() {
                        this.$dispatch('open-modal', { name: 'customer-modal' });
                        this.$nextTick(() => {
                            setTimeout(() => {
                                this.$refs.nameInput && this.$refs.nameInput.focus();
                            }, 100); // Small delay to ensure modal transition
                        });
                    },

                    // Computed properties (simulated getters in Alpine)
                    get subtotal() {
                        return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                    },

                    get totalDiscount() {
                        return this.cart.reduce((sum, item) => sum + (item.discount * item.quantity), 0);
                    },

                    get total() {
                        return this.subtotal - this.totalDiscount - this.globalDiscount;
                    },

                    get change() {
                        if (this.payment.method !== 'cash') return 0;
                        return this.payment.cash_received - this.total;
                    },

                    // Helpers
                    formatCurrency(value) {
                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value).replace('Rp', 'Rp ');
                    },

                    formatNumber(value) {
                        return new Intl.NumberFormat('id-ID').format(value);
                    },

                    unformatNumber(value) {
                        return parseInt(value.replace(/\./g, '')) || 0;
                    },

                    // Confirmation
                    openConfirmation() {
                        if (this.cart.length === 0) return;
                        if (this.payment.method === 'cash' && this.payment.cash_received < this.total) {
                            this.$dispatch('toast', { message: 'Insufficient payment!', type: 'error' });
                            return;
                        }

                        this.$dispatch('open-modal', { name: 'confirmation-modal' });
                    },

                    // Submit Sale
                    async submitSale() {
                        this.isSubmitting = true;

                        try {
                            const items = this.cart.map(item => ({
                                product_id: item.id,
                                quantity: item.quantity,
                                unit_price: item.price,
                                discount: item.discount
                            }));

                            const payload = {
                                customer_id: this.selectedCustomer?.id,
                                items: items,
                                payment_method: this.payment.method,
                                cash_received: this.payment.cash_received,
                                notes: this.payment.notes,
                                global_discount: this.globalDiscount,
                                status: this.saleStatus,
                                sale_date: new Date().toISOString().slice(0, 10),
                                _token: '{{ csrf_token() }}'
                            };

                            const res = await fetch('{{ route("sales.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(payload)
                            });

                            const data = await res.json();

                            if (res.ok && data.success) {
                                this.$dispatch('close-modal', { name: 'confirmation-modal' });

                                if (data.print_url) {
                                    window.open(data.print_url, '_blank');
                                }

                                this.clearStorage();
                                this.resetForm();

                                this.$dispatch('toast', { message: 'Transaction Successful!', type: 'success' });

                            } else {
                                this.$dispatch('toast', { message: data.message || 'Error occurred', type: 'error' });
                            }

                        } catch (e) {
                            console.error(e);
                            this.$dispatch('toast', { message: 'Network error occurred', type: 'error' });
                        } finally {
                            this.isSubmitting = false;
                        }
                    },

                    resetForm() {
                        this.cart = [];
                        this.selectedCustomer = null;
                        this.payment = {
                            method: 'cash',
                            cash_received: 0,
                            notes: ''
                        };
                        this.globalDiscount = 0;
                        // Reset TomSelects
                        this.productTs && this.productTs.clear();
                        this.customerTs && this.customerTs.clear();
                    }
                }
            }
        </script>

        <!-- Confirmation Modal -->
        <x-modal name="confirmation-modal" focusable>
            <div class="p-6">
                <!-- Header -->
                <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                        Payment Confirmation
                    </h3>
                    <p class="text-sm text-muted-foreground">
                        Please review transaction details before processing.
                    </p>
                </div>

                <!-- Summary Grid -->
                <div class="grid gap-4 py-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Total Items</span>
                        <span class="font-semibold" x-text="cart.reduce((sum, item) => sum + parseInt(item.quantity), 0)"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Subtotal</span>
                        <span class="font-semibold" x-text="formatCurrency(subtotal)"></span>
                    </div>
                    <div class="flex items-center justify-between text-red-600" x-show="totalDiscount > 0">
                        <span class="text-sm font-medium">Discount</span>
                        <span class="font-semibold" x-text="'- ' + formatCurrency(totalDiscount)"></span>
                    </div>
                    <div class="flex items-center justify-between text-red-600" x-show="globalDiscount > 0">
                        <span class="text-sm font-medium">Extra Discount (Global)</span>
                        <span class="font-semibold" x-text="'- ' + formatCurrency(globalDiscount)"></span>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-100 pt-2 mt-2">
                        <span class="text-lg font-bold">Total Bill</span>
                        <span class="text-lg font-bold text-blue-600" x-text="formatCurrency(total)"></span>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-100 pt-2 mt-2" x-show="payment.method === 'cash'">
                        <span class="text-sm font-medium text-gray-500">Cash Received</span>
                        <span class="font-semibold" x-text="formatCurrency(payment.cash_received)"></span>
                    </div>
                    <div class="flex items-center justify-between" x-show="payment.method === 'cash'">
                        <span class="text-sm font-medium text-gray-500">Change</span>
                        <span class="font-bold text-green-600" x-text="formatCurrency(change)"></span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 border-t border-gray-200 pt-4 space-y-4">
                    <!-- Status Selection -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Sale Status</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                @click="saleStatus = 'completed'"
                                class="px-4 py-2 text-sm font-medium rounded-md border flex items-center justify-center transition-colors"
                                :class="saleStatus === 'completed' ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-green-50'"
                            >
                                <svg x-show="saleStatus === 'completed'" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                COMPLETED
                            </button>
                            <button
                                @click="saleStatus = 'pending'"
                                class="px-4 py-2 text-sm font-medium rounded-md border flex items-center justify-center transition-colors"
                                :class="saleStatus === 'pending' ? 'bg-yellow-500 text-white border-yellow-500 shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-yellow-50'"
                            >
                                <svg x-show="saleStatus === 'pending'" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                PENDING
                            </button>
                        </div>
                    </div>

                    <button
                        @click="submitSale()"
                        :disabled="isSubmitting"
                        class="w-full flex justify-center items-center py-3 px-4 rounded-lg shadow-sm text-lg font-bold text-white focus:outline-none disabled:opacity-50 transition-colors"
                        :class="saleStatus === 'completed' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-600 hover:bg-gray-700'"
                    >
                        <template x-if="isSubmitting">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span x-text="isSubmitting ? 'Processing...' : 'PROCESS SALE'"></span>
                    </button>

                    <x-secondary-button
                        type="button"
                        @click="$dispatch('close-modal', { name: 'confirmation-modal' })"
                        class="w-full justify-center"
                    >
                        Back
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>

        <!-- Create Customer Modal (Still using Blade/Alpine Hybrid for ease if reusable) -->
        <x-modal name="customer-modal" focusable>
            <div class="p-6" x-data="{
                newCust: { name: '', email: '', phone: '', address: '', notes: '' },
                errors: {},
                loading: false,
                async save() {
                    this.errors = {}; // Reset errors

                    if (!this.newCust.name.trim()) {
                        this.errors.name = 'Nama wajib diisi.';
                        return;
                    }

                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("ajax.customers.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.newCust)
                        });
                        const data = await res.json();

                        if (res.ok) {
                            this.$dispatch('close-modal', { name: 'customer-modal' });
                            this.$dispatch('customer-created', data);
                            this.newCust = { name: '', email: '', phone: '', address: '', notes: '' };
                            this.errors = {};
                        } else {
                            if (data.errors) {
                                // Map Laravel validation errors to Alpine errors object
                                // Laravel returns { errors: { name: ['Error msg'], ... } }
                                Object.keys(data.errors).forEach(key => {
                                    this.errors[key] = data.errors[key][0];
                                });
                            } else {
                                // Fallback if generic error
                                this.$dispatch('toast', { message: data.message || 'Error creating customer', type: 'error' });
                            }
                        }
                    } catch(e) { console.error(e); }
                    finally { this.loading = false; }
                }
            }">
                <!-- Header -->
                <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                        {{ __('Create New Customer') }}
                    </h3>
                    <p class="text-sm text-muted-foreground">
                        {{ __('Add a new customer to your records for this sale.') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Name -->
                    <div>
                        <x-form-input
                            name="new_name"
                            label="Full Name"
                            x-model="newCust.name"
                            x-ref="nameInput"
                            required
                        />
                        <p x-show="errors.name" x-text="errors.name" class="text-sm font-medium text-red-600 mt-1" style="display: none;"></p>
                    </div>

                    <!-- Contact Info -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/2">
                            <x-form-input
                                name="new_email"
                                label="Email"
                                type="email"
                                x-model="newCust.email"
                            />
                            <p x-show="errors.email" x-text="errors.email" class="text-sm font-medium text-red-600 mt-1" style="display: none;"></p>
                        </div>
                        <div class="w-full sm:w-1/2">
                            <x-form-input
                                name="new_phone"
                                label="Phone"
                                x-model="newCust.phone"
                            />
                            <p x-show="errors.phone" x-text="errors.phone" class="text-sm font-medium text-red-600 mt-1" style="display: none;"></p>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="space-y-2">
                        <x-input-label for="new_address" :value="__('Address')" />
                        <textarea
                            id="new_address"
                            x-model="newCust.address"
                            rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Full Address"
                        ></textarea>
                        <p x-show="errors.address" x-text="errors.address" class="text-sm font-medium text-red-600 mt-1" style="display: none;"></p>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <x-input-label for="new_notes" :value="__('Notes')" />
                        <textarea
                            id="new_notes"
                            x-model="newCust.notes"
                            rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Additional notes..."
                        ></textarea>
                        <p x-show="errors.notes" x-text="errors.notes" class="text-sm font-medium text-red-600 mt-1" style="display: none;"></p>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4">
                        <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'customer-modal' })">
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-primary-button type="button" @click="save()" x-bind:disabled="loading">
                            <template x-if="loading">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Saving...' : 'Save Customer'"></span>
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </x-modal>

        <!-- Cancel Confirmation Modal -->
        <x-modal name="cancel-modal" focusable>
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Cancel Transaction?
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to cancel? All current items and selections will be lost.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <x-danger-button @click="resetForm(); clearStorage(); $dispatch('close-modal', { name: 'cancel-modal' }); $dispatch('toast', { message: 'Transaction Cancelled', type: 'info' })" class="w-full sm:w-auto justify-center">
                    {{ __('Yes, Cancel Transaction') }}
                </x-danger-button>
                <button
                    type="button"
                    @click="$dispatch('close-modal', { name: 'cancel-modal' })"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors"
                >
                    {{ __('No, Return') }}
                </button>
            </div>
        </x-modal>

        <!-- Listen for customer created -->
        <div @customer-created.window="selectedCustomer = $event.detail; customerSearch = '';"></div>
    </div>
</x-app-layout>
