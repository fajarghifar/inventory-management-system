{{-- APP CONTAINER --}}
    <div
        x-data="posSystem()"
        x-init="init()"
        x-cloak
        class="fixed inset-0 top-[4rem] bg-gray-50 dark:bg-gray-950 p-4 lg:p-6 z-0 overflow-hidden flex flex-col"
    >
        {{-- LAYOUT GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 h-full min-h-0">

            {{-- LEFT PANEL: Operations (Search & Cart) --}}
            <div class="lg:col-span-2 flex flex-col gap-4 lg:gap-6 h-full min-h-0">
                <div
    class="bg-white dark:bg-gray-900 px-6 py-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 shrink-0 relative z-50 transition-all duration-300 hover:shadow-md">

    {{-- Search Wrapper --}}
    <div class="relative w-full group">

        {{-- Icon Indicator --}}
        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500 transition-colors group-focus-within:text-primary-500"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        {{-- Search Input --}}
        {{-- Handles debounce (300ms), Enter (select first), and Esc (clear) events --}}
        <input
            type="text"
            id="product-search-input"
            x-ref="searchInput"
            x-model="searchQuery"
            @input.debounce.300ms="searchProducts()"
            @keydown.enter.prevent="selectFirstProduct()"
            @keydown.escape="clearSearch()"
            placeholder="Cari Produk (Scan Barcode / Nama / SKU)... [F1]"
            class="block w-full pl-14 pr-12 py-4 border border-gray-200 dark:border-gray-700 rounded-xl text-lg font-medium text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 shadow-inner focus:shadow-lg"
            autocomplete="off"
        />

        {{-- Loading Indicator --}}
        <div x-show="isSearching" class="absolute inset-y-0 right-0 pr-5 flex items-center">
            <svg class="animate-spin h-6 w-6 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        {{-- Clear Button --}}
        <div x-show="!isSearching && searchQuery.length > 0" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <button @click="clearSearch()"
                class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Results Dropdown --}}
        <div
            x-show="searchQuery.length > 0"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="absolute w-full mt-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-2xl
            max-h-[600px] overflow-y-auto z-50 divide-y divide-gray-50 dark:divide-gray-700/50 ring-1 ring-black/5"
            style="display: none;"
        >
            {{-- Empty State --}}
            <template x-if="!isSearching && searchResults.length === 0">
                <div class="p-12 text-center flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                    <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-full mb-3">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-base font-semibold text-gray-900 dark:text-white">Produk tidak ditemukan</span>
                    <p class="text-sm mt-1 text-gray-400">Coba kata kunci lain atau scan ulang barcode.</p>
                </div>
            </template>

            {{-- Product List Iteration --}}
            <template x-for="(product, index) in searchResults" :key="product.id">
                <div
                    @click="addToCart(product)"
                    class="p-4 hover:bg-primary-50 dark:hover:bg-primary-900/10 cursor-pointer transition-all duration-200 group flex justify-between items-center border-l-4 border-transparent hover:border-primary-500"
                >
                    {{-- Product Info (Left) --}}
                    <div class="flex-1">
                        <div class="font-bold text-gray-900 dark:text-white text-base group-hover:text-primary-700 dark:group-hover:text-primary-400 transition-colors mb-1"
                            x-text="product.name"></div>

                        {{-- Metadata: SKU & Stock --}}
                        <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-3">
                            <span
                                class="bg-gray-100 dark:bg-gray-700/50 px-2 py-0.5 rounded text-xs font-mono font-bold tracking-wide border border-gray-200 dark:border-gray-600/50 text-gray-600 dark:text-gray-300"
                                x-text="product.sku"></span>

                            {{-- Dynamic Stock Badge --}}
                            <span
                                class="font-bold px-2.5 py-0.5 rounded-full text-[10px] flex items-center gap-1.5 uppercase tracking-wider"
                                :class="product.stock > 0 ? 'text-green-700 bg-green-50 dark:bg-green-900/20 dark:text-green-400' : 'text-red-700 bg-red-50 dark:bg-red-900/20 dark:text-red-400'"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                <span x-text="product.stock > 0 ? product.stock + ' Unit' : 'Habis'"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Pricing (Right) --}}
                    <div class="text-right pl-4">
                        <div class="font-black text-primary-600 dark:text-primary-400 text-lg tabular-nums tracking-tight"
                            x-text="formatRupiah(product.price)">
                        </div>
                        <div class="text-xs font-medium text-gray-400 dark:text-gray-500" x-text="'/ ' + product.unit"></div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
                <div
    class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 flex-1 flex flex-col min-h-0 overflow-hidden relative z-0">

    {{-- Cart Header --}}
    <div
        class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 rounded-t-2xl flex justify-between items-center shrink-0">
        {{-- Title --}}
        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg flex items-center gap-3 tracking-tight">
            <div class="bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 p-2.5 rounded-xl">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            KERANJANG BELANJA
        </h3>

        {{-- Counter Badge --}}
        <span
            class="text-[10px] font-bold bg-gray-900 dark:bg-gray-800 text-white dark:text-gray-300 px-3 py-1 rounded-full shadow-md"
            x-text="cart.length + ' ITEM'">
        </span>
    </div>

    {{-- Cart Table Container (Scrollable) --}}
    <div class="flex-1 overflow-y-auto custom-scroll bg-white dark:bg-gray-900 relative">
        <table class="w-full text-sm text-left border-collapse">

            {{-- Sticky Header --}}
            <thead
                class="text-gray-500 dark:text-gray-400 font-bold sticky top-0 z-10 text-[10px] uppercase tracking-wider bg-white dark:bg-gray-900 shadow-[0_2px_4px_-2px_rgba(0,0,0,0.05)]">
                <tr>
                    <th
                        class="py-4 px-6 w-[40%] bg-gray-50/80 dark:bg-gray-800/80 backdrop-blur border-b border-gray-100 dark:border-gray-800">
                        Produk</th>
                    <th
                        class="py-4 px-2 text-right w-[15%] bg-gray-50/80 dark:bg-gray-800/80 backdrop-blur border-b border-gray-100 dark:border-gray-800">
                        Harga</th>
                    <th
                        class="py-4 px-2 text-center w-[15%] bg-gray-50/80 dark:bg-gray-800/80 backdrop-blur border-b border-gray-100 dark:border-gray-800">
                        Qty</th>
                    <th
                        class="py-4 px-2 text-right w-[15%] bg-gray-50/80 dark:bg-gray-800/80 backdrop-blur border-b border-gray-100 dark:border-gray-800">
                        Diskon</th>
                    <th
                        class="py-4 px-6 text-right w-[15%] bg-gray-50/80 dark:bg-gray-800/80 backdrop-blur border-b border-gray-100 dark:border-gray-800">
                        Total</th>
                    <th class="bg-gray-50/80 dark:bg-gray-800/80 backdrop-blur border-b border-gray-100 dark:border-gray-800"></th>
                </tr>
            </thead>

            <tbody class="divide-y divide-dashed divide-gray-100 dark:divide-gray-800">

                {{-- Empty State --}}
                <template x-if="cart.length === 0">
                    <tr>
                        <td colspan="6" class="p-20 text-center select-none">
                            <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-600 transition-all duration-500">
                                <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-full mb-6 relative group">
                                    <div
                                        class="absolute inset-0 bg-primary-500/10 rounded-full scale-0 group-hover:scale-110 transition-transform duration-500">
                                    </div>
                                    <svg class="w-16 h-16 opacity-50 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Keranjang Kosong</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto mb-6">
                                    Belum ada produk yang ditambahkan. Gunakan kolom pencarian atau scan barcode untuk memulai transaksi.
                                </p>
                                <div
                                    class="flex items-center gap-2 text-xs font-mono text-gray-400 bg-gray-50 dark:bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500 animate-pulse"></span>
                                    Tekan [F1] untuk mencari produk
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>

                {{-- Items Loop --}}
                <template x-for="(item, index) in cart" :key="item.id">
                    <tr class="group bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">

                        {{-- Product Info --}}
                        <td class="py-5 px-6 align-middle">
                            <div class="font-bold text-gray-900 dark:text-white text-sm leading-relaxed" x-text="item.name"></div>

                            {{-- Metadata: SKU & Stock --}}
                            <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-2.5">
                                <span
                                    class="font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700"
                                    x-text="item.sku"></span>
                                <div class="flex items-center gap-1.5" title="Stok Tersedia">
                                    <div class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                    <span x-text="'Stok: ' + item.stock"></span>
                                </div>
                                {{-- Stock Alert --}}
                                <span x-show="item.quantity >= item.stock"
                                    class="text-red-600 font-bold ml-auto text-[9px] bg-red-50 dark:bg-red-900/20 px-1.5 py-0.5 rounded border border-red-100 dark:border-red-800">MAX</span>
                            </div>
                        </td>

                        {{-- Unit Price --}}
                        <td class="py-5 px-2 text-right align-middle text-sm font-medium text-gray-600 dark:text-gray-400 tabular-nums"
                            x-text="formatRupiah(item.price)"></td>

                        {{-- Qty Control --}}
                        <td class="py-5 px-2 align-middle">
                            <div
                                class="flex items-center justify-center bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 w-[110px] mx-auto h-10 overflow-hidden shadow-sm group-hover:border-primary-200 dark:group-hover:border-primary-800 transition-colors">
                                <button @click="updateQuantity(index, item.quantity - 1)"
                                    class="w-8 h-full flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition active:bg-gray-100 font-bold text-lg leading-none">−</button>

                                <input
                                    type="number"
                                    x-model.number="item.quantity"
                                    @change="updateQuantity(index, item.quantity)"
                                    class="flex-1 w-full text-center border-none p-0 text-sm font-bold bg-transparent text-gray-900 dark:text-white focus:ring-0 appearance-none selection:bg-primary-100"
                                >

                                <button @click="updateQuantity(index, item.quantity + 1)"
                                    class="w-8 h-full flex items-center justify-center text-gray-400 hover:text-primary-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition active:bg-gray-100 font-bold text-lg leading-none">+</button>
                            </div>
                        </td>

                        {{-- Discount Input --}}
                        <td class="py-5 px-2 text-right align-middle">
                            <div class="relative flex justify-end">
                                <input
                                    type="number"
                                    x-model.number="item.discount"
                                    min="0"
                                    :max="item.price"
                                    class="w-24 text-right bg-transparent border-b border-dashed border-gray-300 dark:border-gray-600 focus:border-primary-500 border-0 focus:ring-0 px-1 py-1 text-sm font-medium text-gray-500 dark:text-gray-400 focus:text-primary-600 placeholder-gray-300 transition-colors hover:border-gray-400"
                                    placeholder="0"
                                >
                            </div>
                        </td>

                        {{-- Subtotal (Calculated) --}}
                        <td
                            class="py-5 px-6 text-right align-middle font-bold text-gray-900 dark:text-white tabular-nums text-sm tracking-tight"
                            x-text="formatRupiah((item.price * item.quantity) - ((item.discount || 0) * item.quantity))"
                        ></td>

                        {{-- Actions --}}
                        <td class="py-5 px-2 text-center align-middle">
                            <button
                                @click="removeFromCart(index)"
                                class="text-gray-300 hover:text-red-500 rounded-lg p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all opacity-0 group-hover:opacity-100 focus:opacity-100"
                                title="Hapus Item"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
            </div>

            {{-- RIGHT PANEL: Payment & Checkout --}}
            <div class="flex flex-col gap-4 lg:gap-6 h-full min-h-0">
                <div
    class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col h-full shrink-0 relative z-20">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 rounded-t-2xl">
        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg flex items-center gap-3 tracking-tight">
            <div class="bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 p-2.5 rounded-xl">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            RINCIAN PEMBAYARAN
        </h3>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="px-6 pt-4">
            <div
                class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl text-sm font-medium flex items-start gap-3">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="space-y-1">
                    <p class="font-bold">Terjadi Kesalahan:</p>
                    <ul class="list-disc list-inside text-xs opacity-90">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Content --}}
    <div class="p-6 space-y-8 flex-1 overflow-y-auto custom-scroll">

        {{-- Customer Selection --}}
        <div class="space-y-3">
            <label
                class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider flex justify-between items-center">
                PELANGGAN
                <button type="button" @click="openCustomerModal"
                    class="text-[10px] font-bold text-primary-600 dark:text-primary-400 hover:underline hover:text-primary-700 transition">
                    + BARU (F3)
                </button>
            </label>
            <div class="relative group">
                <input
                    type="text"
                    x-model="customerSearch"
                    @input.debounce.300ms="searchCustomers()"
                    @click="showCustomerDropdown = true"
                    @click.away="showCustomerDropdown = false"
                    placeholder="Cari Pelanggan..."
                    class="block w-full pl-11 pr-10 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm font-semibold shadow-sm placeholder-gray-400 focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-gray-900 dark:text-white"
                >
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>

                {{-- Loading Spinner --}}
                <div x-show="isSearchingCustomer" class="absolute inset-y-0 right-0 pr-3 flex items-center" style="display: none;">
                    <svg class="animate-spin h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Clear Customer --}}
                <div x-show="selectedCustomer && !isSearchingCustomer" class="absolute inset-y-0 right-0 pr-2 flex items-center">
                    <button @click="clearCustomer"
                        class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                        title="Reset Pelanggan">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Customer Dropdown --}}
                <div
                    x-show="showCustomerDropdown && (customerSearchResults.length > 0 || customerSearch.length > 0)"
                    class="absolute w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl max-h-64 overflow-y-auto z-30 divide-y divide-gray-50 dark:divide-gray-700/50"
                    style="display: none;"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                >
                    <template x-for="customer in customerSearchResults" :key="customer.id">
                        <div @click="selectCustomer(customer)"
                            class="px-5 py-3.5 hover:bg-primary-50 dark:hover:bg-primary-900/10 cursor-pointer transition-colors group">
                            <div class="font-bold text-gray-800 dark:text-white text-sm group-hover:text-primary-700 dark:group-hover:text-primary-400"
                                x-text="customer.name"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-0.5">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span x-text="customer.phone || 'Tanpa No. HP'"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="customerSearchResults.length === 0" class="px-5 py-8 text-center">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pelanggan tidak ditemukan</p>
                    </div>
                    </div>
                    </div>

            {{-- Selected Customer Card --}}
            <div x-show="selectedCustomer"
                class="flex items-center gap-3 px-4 py-3 bg-primary-50 dark:bg-primary-900/10 border border-primary-100 dark:border-primary-800/50 rounded-xl"
                x-transition>
                <div
                    class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-800 flex items-center justify-center text-primary-600 dark:text-primary-300 font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-bold text-primary-600 dark:text-primary-400 leading-none mb-1">Pelanggan
                        Terpilih</div>
                    <div class="text-sm font-bold text-gray-900 dark:text-white leading-none" x-text="selectedCustomer?.name"></div>
                </div>
            </div>
        </div>

        {{-- Payment Method --}}
        <div class="space-y-3">
            <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">METODE PEMBAYARAN</label>
            <div class="grid grid-cols-2 gap-3">
                <button @click="paymentMethod = 'cash'"
                    class="relative py-3 px-4 rounded-xl border-2 font-bold text-sm transition-all shadow-sm flex flex-row items-center justify-center gap-2.5 group overflow-hidden"
                    :class="paymentMethod === 'cash' ? 'bg-primary-600 border-primary-600 text-white shadow-primary-500/30 shadow-md' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 hover:border-gray-300 dark:hover:bg-gray-700'">

                    <svg class="w-5 h-5 relative z-10"
                        :class="paymentMethod === 'cash' ? 'text-white' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="relative z-10">TUNAI</span>

                    {{-- Active Background Pattern --}}
                    <div x-show="paymentMethod === 'cash'"
                        class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IndoaXRlIi8+PC9zdmc+')]">
                    </div>
                </button>

                <button @click="paymentMethod = 'transfer'"
                    class="relative py-3 px-4 rounded-xl border-2 font-bold text-sm transition-all shadow-sm flex flex-row items-center justify-center gap-2.5 group overflow-hidden"
                    :class="paymentMethod === 'transfer' ? 'bg-blue-600 border-blue-600 text-white shadow-blue-500/30 shadow-md' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 hover:border-gray-300 dark:hover:bg-gray-700'">

                    <svg class="w-5 h-5 relative z-10"
                        :class="paymentMethod === 'transfer' ? 'text-white' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span class="relative z-10">TRANSFER</span>
                </button>
                </div>
                </div>

        {{-- Cash Amount Input (Only for Cash) --}}
        <div x-show="paymentMethod === 'cash'" x-transition.opacity.duration.300ms class="space-y-3">
            <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">JUMLAH UANG (RP)</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span
                        class="text-gray-400 group-focus-within:text-primary-500 font-black text-lg transition-colors">Rp</span>
                </div>
                <!-- Prevent default form submission on Enter -->
                <input type="text" x-model="cashReceived" @keydown.enter.prevent="openPaymentConfirmation"
                    class="block w-full pl-12 pr-4 py-4 rounded-xl border border-gray-300 dark:border-gray-600 text-2xl font-black shadow-sm placeholder-gray-300 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 dark:bg-gray-800 dark:text-white transition-all tabular-nums tracking-tight"
                    placeholder="0">
            </div>

            {{-- Quick Amounts --}}
            <div class="grid grid-cols-4 gap-2">
                <button type="button" @click="cashReceived = total"
                    class="px-2 py-2 text-[10px] font-bold bg-primary-50 hover:bg-primary-100 text-primary-700 border border-primary-100 rounded-lg transition-colors">
                    UANG PAS
                </button>
                <button type="button" @click="suggestAmount(10000)"
                    class="px-2 py-2 text-[10px] font-bold bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-300 hover:bg-gray-50 transition-colors shadow-sm">
                    +10.000
                </button>
                <button type="button" @click="suggestAmount(20000)"
                    class="px-2 py-2 text-[10px] font-bold bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-300 hover:bg-gray-50 transition-colors shadow-sm">
                    +20.000
                </button>
                <button type="button" @click="suggestAmount(50000)"
                    class="px-2 py-2 text-[10px] font-bold bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-300 hover:bg-gray-50 transition-colors shadow-sm">
                    +50.000
                </button>
                </div>
                </div>

                {{-- Total Summary & Change --}}
                <div class="space-y-3 pt-2">

                    {{-- Subtotal & Discount (Compact) --}}
                    <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 px-1 font-medium">
                        <span>Subtotal</span>
                        <span class="font-bold border-b border-gray-200 dark:border-gray-700 border-dashed"
                            x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div x-show="totalDiscount > 0"
                        class="flex items-center justify-between text-sm text-green-600 dark:text-green-400 px-1 font-medium">
                        <span>Diskon</span>
                        <span class="font-bold border-b border-green-200 dark:border-green-800 border-dashed"
                            x-text="'- ' + formatRupiah(totalDiscount)"></span>
            </div>

            {{-- Grand Total --}}
            <div
                class="bg-primary-50 dark:bg-gray-800 border border-primary-100 dark:border-gray-700 text-primary-900 dark:text-white rounded-xl p-4 flex justify-between items-center shadow-sm">
                <div>
                    <div class="text-[10px] font-bold text-primary-600 dark:text-gray-400 uppercase tracking-wider">Total Tagihan
                    </div>
                    <div class="text-3xl font-black tracking-tight leading-none mt-0.5" x-text="formatRupiah(total)"></div>
                    </div>

                {{-- Dynamic Change Display (Inside Total Box) --}}
                <div x-show="paymentMethod === 'cash' && cashReceived > 0" class="text-right">
                    <div class="text-[10px] font-bold uppercase tracking-wider" :class="change < 0 ? 'text-red-500' : 'text-green-600'"
                        x-text="change < 0 ? 'KURANG' : 'KEMBALI'"></div>
                    <div class="text-xl font-bold leading-none mt-0.5" :class="change < 0 ? 'text-red-600' : 'text-green-600'"
                        x-text="formatRupiah(Math.abs(change))"></div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div>
            <label class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2 block">CATATAN
                TRANSAKSI</label>
            <input type="text" x-model="notes"
                class="block w-full border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm focus:ring-primary-500 focus:border-primary-500 px-4 py-3 bg-white dark:bg-gray-800 placeholder-gray-400 text-gray-900 dark:text-white transition-all text-sm"
                placeholder="Contoh: Meja 5, Bungkus, dll...">
        </div>
    </div>

    {{-- Footer Actions --}}
    <div
        class="px-6 py-5 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 mt-auto backdrop-blur-md rounded-b-2xl">
        <button @click="openPaymentConfirmation"
            :disabled="cart.length === 0 || (paymentMethod === 'cash' && cashReceived < total)"
            class="w-full bg-blue-600 dark:bg-blue-600 text-white rounded-xl py-4 font-bold text-base shadow-xl hover:shadow-2xl hover:bg-blue-700 dark:hover:bg-blue-500 transform hover:-translate-y-1 active:translate-y-0 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none disabled:transform-none flex items-center justify-center gap-2 group ring-4 ring-transparent focus:ring-blue-200 dark:focus:ring-blue-900/50">
            <span class="tracking-wide">BAYAR SEKARANG [F2]</span>
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </button>

        <button @click="clearCart" :disabled="cart.length === 0"
            class="w-full mt-4 flex items-center justify-center gap-2 text-gray-400 hover:text-red-600 dark:text-gray-500 dark:hover:text-red-400 py-2 rounded-lg font-semibold text-xs transition-colors disabled:opacity-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Batalkan Transaksi
        </button>
    </div>
</div>
            </div>
        </div>

        {{-- MODALS LAYER --}}
        <div>
    {{-- Toast Notification --}}
    <div x-show="notification.show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-6 right-6 z-[9999] flex items-start gap-4 px-6 py-5 rounded-2xl shadow-2xl min-w-[340px] max-w-md border backdrop-blur-xl transition-all"
         :class="notification.type === 'error' ? 'bg-white/95 dark:bg-gray-800/95 border-red-100 dark:border-red-900/30' : 'bg-white/95 dark:bg-gray-800/95 border-green-100 dark:border-green-900/30'"
         style="display: none;">

        {{-- Icon --}}
        <div class="shrink-0 pt-0.5">
            <template x-if="notification.type === 'error'">
                <div class="bg-red-100 dark:bg-red-900/30 p-2 rounded-xl text-red-600 dark:text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </template>
            <template x-if="notification.type !== 'error'">
                <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-xl text-green-600 dark:text-green-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </template>
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <h4 class="font-bold text-sm uppercase tracking-wide mb-1"
                :class="notification.type === 'error' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                x-text="notification.type === 'error' ? 'Gagal' : 'Berhasil'"></h4>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 leading-snug break-words"
                x-text="notification.message"></p>
        </div>

        {{-- Close --}}
        <button @click="notification.show = false"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    {{-- Payment Confirmation Modal --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-[9000]" style="display: none;">
        {{-- Backdrop --}}
        <div x-show="showConfirmModal" x-transition.opacity.duration.300ms
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

        {{-- Panel --}}
        <div class="fixed inset-0 z-[9010] flex items-center justify-center p-4">
            <div x-show="showConfirmModal"
                 @click.away="showConfirmModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl ring-1 ring-gray-200 dark:ring-gray-700">

                {{-- Header --}}
                <div
                    class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <span class="bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 p-2 rounded-xl">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        Konfirmasi Pembayaran
                    </h3>
                    <button @click="showConfirmModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition bg-gray-50 dark:bg-gray-700/50 p-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-6">
                    {{-- Summary Grid --}}
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-100 dark:border-gray-600/50">
                            <div class="text-[10px] text-gray-400 dark:text-gray-500 mb-1.5 uppercase tracking-wide font-bold">Pelanggan
                            </div>
                            <div class="font-bold text-gray-900 dark:text-white truncate text-base"
                                x-text="selectedCustomer ? selectedCustomer.name : 'Guest (Umum)'"></div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-100 dark:border-gray-600/50">
                                <div class="text-[10px] text-gray-400 dark:text-gray-500 mb-1.5 uppercase tracking-wide font-bold">Metode</div>
                                <div class="font-bold text-gray-900 dark:text-white uppercase text-base"
                                    x-text="paymentMethod === 'cash' ? 'TUNAI' : 'TRANSFER'"></div>
                        </div>
                    </div>

                    {{-- Status Selector --}}
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block tracking-wide">Status Pesanan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" @click="saleStatus = 'done'"
                                    class="py-3.5 rounded-xl border font-bold text-sm transition-all shadow-sm flex justify-center items-center gap-2 relative overflow-hidden"
                                    :class="saleStatus === 'done' ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-500 ring-1 ring-green-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50'">
                                <svg x-show="saleStatus === 'done'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                SELESAI
                            </button>
                            <button type="button" @click="saleStatus = 'pending'"
                                    class="py-3.5 rounded-xl border font-bold text-sm transition-all shadow-sm flex justify-center items-center gap-2 relative overflow-hidden"
                                    :class="saleStatus === 'pending' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 border-orange-500 ring-1 ring-orange-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50'">
                                <svg x-show="saleStatus === 'pending'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                PENDING
                            </button>
                        </div>
                    </div>

                    {{-- Financial Summary --}}
                    <div class="space-y-4 pt-6 border-t border-dashed border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Total Tagihan</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white tabular-nums tracking-tight"
                                x-text="formatRupiah(total)"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Uang Diterima</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white tabular-nums tracking-tight"
                                x-text="formatRupiah(cashReceived)"></span>
                        </div>

                        {{-- Change Box --}}
                        <div class="rounded-xl p-5 flex flex-col items-center justify-center transition-all duration-300 border bg-white dark:bg-gray-800 shadow-sm"
                            :class="change < 0 ? 'border-red-200 dark:border-red-800' : 'border-green-200 dark:border-green-800'">
                            <span class="text-[10px] font-bold uppercase tracking-widest mb-1"
                                :class="change < 0 ? 'text-red-500 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                                KEMBALIAN
                            </span>
                            <span class="text-4xl font-black tabular-nums tracking-tighter"
                                :class="change < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                                  x-text="formatRupiah(change)">
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-6 py-5 bg-gray-50 dark:bg-gray-800/50 flex gap-3 border-t border-gray-100 dark:border-gray-700 rounded-b-2xl">
                    <button type="button" @click="showConfirmModal = false"
                            class="w-1/3 px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm active:scale-[0.98]">
                        Batal
                    </button>
                    <button type="button" x-ref="btnConfirmPay" @click="processPayment"
                            class="w-2/3 px-4 py-3 bg-gray-900 dark:bg-primary-600 text-white rounded-xl text-sm font-bold hover:bg-black dark:hover:bg-primary-500 transition shadow-xl flex justify-center items-center gap-2 focus:ring-4 focus:ring-gray-200 dark:focus:ring-primary-900/50 active:scale-[0.98]">
                        <span>PROSES & CETAK</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- New Customer Modal --}}
    <div x-show="showCustomerModal" class="fixed inset-0 z-[9000]" style="display: none;">
        <div x-show="showCustomerModal" x-transition.opacity
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-[9010] flex items-center justify-center p-4">
            <div x-show="showCustomerModal"
                 @click.away="showCustomerModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl ring-1 ring-gray-200 dark:ring-gray-700">

                {{-- Header --}}
                <div
                    class="bg-white dark:bg-gray-800 px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 p-2 rounded-xl">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        </span>
                        Pelanggan Baru
                    </h3>
                    <button @click="showCustomerModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition bg-gray-50 dark:bg-gray-700/50 p-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Form Content --}}
                <div class="px-6 py-6 space-y-5">
                    {{-- Name --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Nama Lengkap
                            <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 group-focus-within:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" x-model="newCustomerName" x-ref="newCustomerInput"
                                class="block w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-sm font-medium shadow-sm placeholder-gray-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-all"
                                placeholder="Contoh: Budi Santoso">
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">No. HP /
                            WhatsApp</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 group-focus-within:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <input type="text" x-model="newCustomerPhone"
                                class="block w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-sm font-medium shadow-sm placeholder-gray-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-all"
                                placeholder="0812...">
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Alamat
                            Domisili</label>
                        <div class="relative group">
                            <div class="absolute top-3 left-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 group-focus-within:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <textarea x-model="newCustomerAddress" rows="2"
                                class="block w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-sm font-medium shadow-sm placeholder-gray-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:bg-gray-700 dark:text-white resize-none transition-all"
                                placeholder="Nama jalan, nomor rumah..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-5 border-t border-gray-100 dark:border-gray-700 rounded-b-2xl">
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="showCustomerModal = false"
                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm active:scale-[0.98]">
                            Batal
                        </button>
                        <button type="button" @click="saveCustomer" :disabled="isSavingCustomer"
                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-primary-600 border border-transparent rounded-xl text-sm font-bold text-white hover:bg-primary-700 focus:ring-4 focus:ring-primary-500/30 shadow-md transition disabled:opacity-70 disabled:cursor-not-allowed active:scale-[0.98]">
                            <svg x-show="isSavingCustomer" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isSavingCustomer ? 'Menyimpan...' : 'SIMPAN PELANGGAN'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
