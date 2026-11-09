<x-app-layout title="POS">
    <div class="mx-auto px-3 sm:px-6 lg:px-8 py-4"
         x-data="pos()"
         x-init="init()"
         @keydown.window.f1.prevent="productTs && productTs.focus()"
         @keydown.window.f2.prevent="customerTs && customerTs.focus()"
         @keydown.window.f3.prevent="openConfirmation()"
         @keydown.window.f4.prevent="openCustomerModal()"
    >
        <!-- POS Top Ribbon: Title, Online Status & Offline Sync Badge -->
        <div class="mb-3 flex items-center justify-between gap-2 bg-white dark:bg-slate-900 px-3 sm:px-4 py-2 rounded-lg border border-gray-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center space-x-2">
                <h1 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    POS Register
                </h1>
                <!-- Online/Offline Live Status Pill -->
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                      :class="isOnline ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'">
                    <span class="w-1.5 h-1.5 rounded-full" :class="isOnline ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'"></span>
                    <span x-text="isOnline ? 'Online' : 'Offline'"></span>
                </span>
            </div>

            <div class="flex items-center space-x-1.5">
                <!-- POS Audio Toggle Button -->
                <button
                    type="button"
                    @click="isAudioMuted = window.posAudio ? window.posAudio.toggleMute() : !isAudioMuted"
                    class="p-1 rounded-md text-xs font-semibold border transition-colors flex items-center justify-center w-7 h-7"
                    :class="isAudioMuted ? 'bg-gray-100 text-gray-400 border-gray-200' : 'bg-indigo-50 text-indigo-600 border-indigo-200'"
                    :title="isAudioMuted ? 'Unmute POS Sound' : 'Mute POS Sound'"
                >
                    <template x-if="!isAudioMuted">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                    </template>
                    <template x-if="isAudioMuted">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15zM17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path></svg>
                    </template>
                </button>

                <!-- Hold Cart Button -->
                <button
                    type="button"
                    x-show="cart.length > 0"
                    @click="holdCurrentCart()"
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-800 rounded-md text-xs font-semibold shadow-sm transition-colors"
                    title="Hold current cart & serve next customer"
                >
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="hidden sm:inline">Hold</span>
                </button>

                <!-- Recall Tickets Button -->
                <button
                    type="button"
                    x-show="heldTickets.length > 0"
                    @click="$dispatch('open-modal', { name: 'recall-tickets-modal' })"
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-50 hover:bg-purple-100 border border-purple-300 text-purple-800 rounded-md text-xs font-semibold shadow-sm transition-colors"
                    title="View & resume held cart tickets"
                >
                    <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span class="hidden sm:inline">Held:</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-purple-200 text-purple-900" x-text="heldTickets.length">0</span>
                </button>

                <!-- Offline Sales Queue Button -->
                <button
                    type="button"
                    @click="$dispatch('open-modal', { name: 'offline-queue-modal' })"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold transition-colors border"
                    :class="conflictSalesCount > 0 ? 'bg-red-50 text-red-700 border-red-300 hover:bg-red-100' : (pendingSalesCount > 0 ? 'bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100')"
                >
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="hidden sm:inline">Offline:</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
                          :class="conflictSalesCount > 0 ? 'bg-red-200 text-red-900' : (pendingSalesCount > 0 ? 'bg-amber-200 text-amber-900' : 'bg-gray-200 text-gray-800')"
                          x-text="pendingSalesCount + (conflictSalesCount > 0 ? ' (' + conflictSalesCount + '!)' : '')">0</span>
                </button>

                <!-- Sync Now Button -->
                <button
                    type="button"
                    x-show="pendingSalesCount > 0 && isOnline"
                    @click="syncOfflineSales()"
                    :disabled="isSyncing"
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-md text-xs font-semibold shadow-sm transition-colors"
                >
                    <svg class="w-3 h-3" :class="isSyncing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span x-text="isSyncing ? '...' : 'Sync'"></span>
                </button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row space-y-4 lg:space-y-0 lg:space-x-4 relative">

            <!-- Left Side: Transaction Details (70%) -->
            <div class="w-full lg:w-[70%] flex flex-col space-y-4 h-full">
                <!-- Search Bar (TomSelect) + Phone Camera Scanner -->
                <div class="flex items-center gap-2 mb-2 relative z-20">
                    <div class="flex-1">
                        <select
                            x-ref="productSelect"
                            placeholder="Search Products (Name or SKU) [F1]..."
                            autocomplete="off"
                        ></select>
                    </div>

                    <!-- Instant Phone Camera Barcode Scanner Button -->
                    <button
                        type="button"
                        @click="openCameraScanner()"
                        class="inline-flex items-center justify-center gap-1.5 h-[42px] px-3 sm:px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-md text-xs sm:text-sm font-semibold shadow-sm transition-all focus:outline-none flex-shrink-0"
                        title="Scan Barcode with Phone Camera"
                    >
                        <svg class="w-5 h-5 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="hidden sm:inline">Camera Scan</span>
                        <span class="inline sm:hidden font-bold">Scan</span>
                    </button>
                </div>

                <!-- Cart Container -->
                <div class="flex-1 bg-white rounded-lg shadow border border-gray-200 overflow-hidden flex flex-col">
                    <!-- 1. Mobile Phone Touch Cart View (Shown on < lg screens) -->
                    <div class="block lg:hidden flex-1 overflow-y-auto">
                        <!-- Empty State for Mobile -->
                        <template x-if="cart.length === 0">
                            <div class="py-14 px-4 text-center">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <h4 class="font-bold text-sm text-gray-800">Your Cart is Empty</h4>
                                <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Use the search box above or scan barcodes with your camera.</p>
                                <button
                                    type="button"
                                    @click="openCameraScanner()"
                                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-all active:scale-95"
                                >
                                    <svg class="w-4 h-4 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>Start Camera Scanner</span>
                                </button>
                            </div>
                        </template>

                        <!-- Cart Items List for Mobile -->
                        <div x-show="cart.length > 0" class="p-3 space-y-2.5">
                            <template x-for="(item, index) in cart" :key="item.id">
                                <div class="p-3 bg-gray-50/80 border border-gray-200 rounded-xl space-y-2.5 shadow-sm">
                                    <!-- Header: Name, SKU & Remove Button -->
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold text-sm text-gray-900 truncate" x-text="item.name"></h4>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[11px] font-mono text-gray-500" x-text="item.sku"></span>
                                                <span class="text-[11px] text-gray-400" x-text="'&bull; ' + (item.unit || 'unit')"></span>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeFromCart(index)"
                                            class="w-7 h-7 rounded-full bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center flex-shrink-0 transition-colors"
                                            title="Remove item"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>

                                    <!-- Price & Touch-Friendly Stepper -->
                                    <div class="flex items-center justify-between pt-2 border-t border-gray-200/80">
                                        <!-- Stepper [-] [ Qty ] [+] -->
                                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white shadow-sm">
                                            <button
                                                type="button"
                                                @click="decrementQty(index)"
                                                class="w-9 h-8 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 font-bold text-sm flex items-center justify-center transition-colors"
                                            >
                                                -
                                            </button>
                                            <input
                                                type="number"
                                                x-model="item.quantity"
                                                min="1"
                                                :max="item.max_stock"
                                                @input="validateQty(index)"
                                                class="w-11 h-8 text-center text-xs font-bold border-0 focus:ring-0 p-0 text-gray-900"
                                            >
                                            <button
                                                type="button"
                                                @click="incrementQty(index)"
                                                class="w-9 h-8 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 font-bold text-sm flex items-center justify-center transition-colors"
                                            >
                                                +
                                            </button>
                                        </div>

                                        <!-- Line Total -->
                                        <div class="text-right">
                                            <div class="text-[11px] text-gray-400" x-text="formatCurrency(item.price) + ' / unit'"></div>
                                            <div class="text-sm font-extrabold text-gray-900" x-text="formatCurrency((item.price - (item.discount || 0)) * item.quantity)"></div>
                                        </div>
                                    </div>

                                    <!-- Max stock alert -->
                                    <div x-show="item.quantity >= item.max_stock" class="text-[11px] text-amber-600 font-medium">
                                        Max available stock reached (<span x-text="item.max_stock"></span>)
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Mobile Quick Checkout Bar (Sticky when cart has items) -->
                        <div x-show="cart.length > 0" class="p-3 bg-indigo-50 border-t border-indigo-100 flex items-center justify-between">
                            <div>
                                <div class="text-[11px] text-indigo-700 font-semibold" x-text="cart.reduce((sum, it) => sum + parseInt(it.quantity || 1), 0) + ' item(s)'"></div>
                                <div class="text-base font-extrabold text-indigo-950" x-text="formatCurrency(total)"></div>
                            </div>
                            <button
                                type="button"
                                @click="openConfirmation()"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-lg text-xs font-bold shadow-md transition-colors flex items-center gap-1.5"
                            >
                                <span>Pay Now</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- 2. Desktop Spreadsheet Table View (Shown on lg+ screens) -->
                    <div class="hidden lg:block overflow-x-auto flex-1">
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
                                                <div class="absolute inset-y-0 flex items-center pointer-events-none" :class="window.currencyPosition === 'left' ? 'left-0 pl-2' : 'right-0 pr-2'">
                                                    <span class="text-gray-500 sm:text-xs" x-text="window.currencySymbol"></span>
                                                </div>
                                                <input
                                                    type="text"
                                                    :value="formatNumber(item.discount)"
                                                    @input="item.discount = unformatNumber($event.target.value)"
                                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                    :class="window.currencyPosition === 'left' ? 'pl-8 pr-2 text-right' : 'pr-8 pl-2 text-left'"
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
            <div class="w-full lg:w-[30%] flex flex-col bg-white rounded-lg shadow border border-gray-200 h-auto lg:h-full">
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
                                <div class="absolute inset-y-0 flex items-center pointer-events-none" :class="window.currencyPosition === 'left' ? 'left-0 pl-2' : 'right-0 pr-2'">
                                    <span class="text-gray-500 sm:text-xs" x-text="window.currencySymbol"></span>
                                </div>
                                <input
                                    type="text"
                                    :value="formatNumber(globalDiscount)"
                                    @input="globalDiscount = unformatNumber($event.target.value)"
                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-1"
                                    :class="window.currencyPosition === 'left' ? 'pl-8 pr-2 text-right' : 'pr-8 pl-2 text-left'"
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
                                    <div class="absolute inset-y-0 flex items-center pointer-events-none" :class="window.currencyPosition === 'left' ? 'left-0 pl-3' : 'right-0 pr-3'">
                                        <span class="text-gray-500 font-bold" x-text="window.currencySymbol"></span>
                                    </div>
                                    <input
                                        type="text"
                                        :value="formatNumber(payment.cash_received)"
                                        @input="payment.cash_received = unformatNumber($event.target.value)"
                                        class="block w-full py-3 text-lg font-bold text-gray-900 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                        :class="window.currencyPosition === 'left' ? 'pl-10 pr-3 text-left' : 'pr-10 pl-3 text-right'"
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

                    // Real POS Machine State
                    isAudioMuted: localStorage.getItem('pos_sound_muted') === 'true',
                    heldTickets: JSON.parse(localStorage.getItem('pos_held_tickets') || '[]'),
                    lastCompletedSale: null,

                    // Phone Camera Barcode Scanner State
                    isCameraOpen: false,
                    cameraLoading: false,
                    cameraError: null,
                    cameraHasTorch: false,
                    cameraTorchOn: false,
                    lastScannedItem: null,
                    lastScannedUnregisteredCode: null,

                    // PWA & Offline State
                    isOnline: navigator.onLine,
                    isSyncing: false,
                    offlineSalesList: [],
                    pendingSalesCount: 0,
                    conflictSalesCount: 0,
                    offlineFilter: 'all',

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

                        // PWA Network & Offline Listeners
                        window.addEventListener('online', () => {
                            this.isOnline = true;
                            this.syncOfflineSales();
                        });
                        window.addEventListener('offline', () => {
                            this.isOnline = false;
                        });
                        window.addEventListener('pwa-sales-updated', () => {
                            this.refreshOfflineSales();
                        });

                        // Hardware Barcode Scanner Global Intercept & Shortcuts
                        let barcodeBuffer = '';
                        let lastKeyTime = 0;
                        window.addEventListener('keydown', (e) => {
                            // If sale-complete modal is open:
                            if (this.lastCompletedSale) {
                                if (e.key === 'Enter') {
                                    e.preventDefault();
                                    if (this.lastCompletedSale.receiptUrl) {
                                        window.open(this.lastCompletedSale.receiptUrl, '_blank', 'width=420,height=650');
                                    }
                                    return;
                                } else if (e.key === ' ' || e.key === 'Escape') {
                                    e.preventDefault();
                                    this.$dispatch('close-modal', { name: 'sale-complete-modal' });
                                    this.lastCompletedSale = null;
                                    return;
                                }
                            }

                            const now = Date.now();
                            const isEditable = ['INPUT', 'TEXTAREA'].includes(e.target.tagName);

                            if (now - lastKeyTime > 120) {
                                barcodeBuffer = '';
                            }
                            lastKeyTime = now;

                            if (e.key === 'Enter') {
                                if (barcodeBuffer.length >= 3 && !isEditable) {
                                    e.preventDefault();
                                    this.scanBarcode(barcodeBuffer.trim());
                                    barcodeBuffer = '';
                                }
                            } else if (e.key.length === 1 && !isEditable) {
                                barcodeBuffer += e.key;
                            }
                        });

                        // Pre-cache catalog for offline searches
                        if (navigator.onLine) {
                            fetch('{{ route("ajax.products.catalog") }}')
                                .then(res => res.json())
                                .then(products => {
                                    if (window.PwaDB && Array.isArray(products)) {
                                        window.PwaDB.cacheProducts(products);
                                    }
                                })
                                .catch(e => console.warn('[POS] Could not precache catalog:', e));
                        }

                        this.refreshOfflineSales();
                        if (navigator.onLine) {
                            this.syncOfflineSales();
                        }

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

                                if (!navigator.onLine && window.PwaDB) {
                                    window.PwaDB.searchProducts(query).then(callback).catch(() => callback());
                                    return;
                                }

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
                                    }).catch(() => {
                                        if (window.PwaDB) {
                                            window.PwaDB.searchProducts(query).then(callback).catch(() => callback());
                                        } else {
                                            callback();
                                        }
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

                        // Intercept Enter key inside TomSelect input (e.g. handheld scanner or quick typed barcode)
                        if (this.productTs && this.productTs.control_input) {
                            this.productTs.control_input.addEventListener('keydown', (e) => {
                                if (e.key === 'Enter') {
                                    const val = (this.productTs.control_input.value || '').trim();
                                    if (val.length >= 2) {
                                        e.preventDefault();
                                        this.scanBarcode(val);
                                        this.productTs.clear(true);
                                        this.productTs.setTextboxValue('');
                                    }
                                }
                            });
                        }
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
                                window.posAudio && window.posAudio.playBeep();
                                this.$dispatch('toast', { message: 'Product already exists. Quantity updated.', type: 'info' });
                            } else {
                                window.posAudio && window.posAudio.playError();
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
                                window.posAudio && window.posAudio.playBeep();
                                this.$dispatch('toast', { message: 'Product "' + product.name + '" added to cart.', type: 'success' });
                            } else {
                                window.posAudio && window.posAudio.playError();
                                this.$dispatch('toast', { message: 'Out of Stock!', type: 'error' });
                            }
                        }
                    },

                    validateQty(index) {
                        const item = this.cart[index];
                        if (!item) return;
                        if (item.quantity > item.max_stock) {
                            item.quantity = item.max_stock;
                            window.posAudio && window.posAudio.playError();
                            this.$dispatch('toast', { message: 'Maximum stock limit reached', type: 'warning' });
                        }
                        if (item.quantity < 1) item.quantity = 1;
                    },

                    incrementQty(index) {
                        const item = this.cart[index];
                        if (!item) return;
                        if (item.quantity < item.max_stock) {
                            item.quantity++;
                            window.posAudio && window.posAudio.playBeep();
                            this.validateQty(index);
                        } else {
                            window.posAudio && window.posAudio.playError();
                            this.$dispatch('toast', { message: 'Maximum stock limit reached', type: 'warning' });
                        }
                    },

                    decrementQty(index) {
                        const item = this.cart[index];
                        if (!item) return;
                        if (item.quantity > 1) {
                            item.quantity--;
                            this.validateQty(index);
                        }
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
                        return window.formatMoney(value);
                    },

                    unformatNumber(value) {
                        if(typeof value !== 'string') return value || 0;
                        
                        // Let's strip out the thousand separator safely
                        // e.g. "1,000.50" with thousand="," -> "1000.50"
                        let raw = value;
                        if(window.thousandSeparator) {
                            raw = raw.split(window.thousandSeparator).join('');
                        }
                        
                        // Replace the designated decimal separator with a standard dot for JS parsing
                        if(window.decimalSeparator && window.decimalSeparator !== '.') {
                            raw = raw.replace(window.decimalSeparator, '.');
                        }

                        // Remove any characters that aren't numbers, dot, or minus sign
                        raw = raw.replace(/[^0-9\.-]/g, '');
                        
                        // If it ends with a dot or dot-zero(s), we don't want to parse it yet 
                        // as it breaks the user's typing experience (e.g. typing "10." becomes "10" instantly)
                        // However, for pure JS calculation, we must return a number.
                        // The trick here is handled in the formatNumber/binding, but unformat must return string if it ends with dot
                        
                        if (raw === '' || raw === '-') return 0;
                        // Return the raw string if the user is actively typing a decimal
                        if (raw.endsWith('.')) return raw;
                        
                        return parseFloat(raw) || 0;
                    },

                    formatNumber(value) {
                        // If the user is mid-typing a decimal (e.g "10."), just return as is
                        // to prevent the cursor from jumping and erasing the dot.
                        if (typeof value === 'string' && value.endsWith('.')) {
                             return value.replace('.', window.decimalSeparator);
                        }
                        
                        let amount = parseFloat(value) || 0;
                        let isNegative = amount < 0;
                        amount = Math.abs(amount);

                        // Only force fraction digits if we are NOT actively typing
                        // Because formatting actively typing "0.5" might force "0.50" immediately.
                        // We will just let `Math.abs` Handle it unless formatting for display.
                        
                        let strAmount = amount.toString();
                        // if we want to force fraction, use toFixed here. 
                        // But for active input fields, forcing .toFixed(2) while typing is bad UX.
                        // For display fields (Total), it's fine. 
                        // Since this is used in BOTH, we check if it's an integer.
                        
                        let parts = strAmount.split('.');
                        let integerPart = parts[0];
                        let decimalPart = parts.length > 1 ? window.decimalSeparator + parts[1] : '';

                        let rgx = /(\d+)(\d{3})/;
                        while (rgx.test(integerPart)) {
                            integerPart = integerPart.replace(rgx, '$1' + window.thousandSeparator + '$2');
                        }

                        let num = integerPart + decimalPart;
                        return isNegative ? '-' + num : num;
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

                    // Submit Sale (Online + Offline Resilient)
                    async submitSale() {
                        this.isSubmitting = true;

                        const transactionUuid = (typeof crypto !== 'undefined' && crypto.randomUUID)
                            ? crypto.randomUUID()
                            : 'offline-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9);

                        try {
                            const items = this.cart.map(item => ({
                                product_id: item.id,
                                quantity: parseInt(item.quantity) || 1,
                                unit_price: parseFloat(item.price) || 0,
                                discount: parseFloat(item.discount) || 0
                            }));

                            const payload = {
                                offline_uuid: transactionUuid,
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

                            // If offline, queue in IndexedDB immediately
                            if (!navigator.onLine) {
                                await this.recordOfflineSale(payload, transactionUuid);
                                return;
                            }

                            // If online, send to server
                            const res = await fetch('{{ route("sales.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(payload)
                            });

                            const data = await res.json().catch(() => ({}));

                            if (res.ok && (data.success || data.data)) {
                                this.$dispatch('close-modal', { name: 'confirmation-modal' });

                                const completedId = data.data?.id;
                                this.lastCompletedSale = {
                                    id: completedId,
                                    invoice: data.data?.invoice_number || 'Completed',
                                    total: this.total,
                                    cashReceived: this.payment.cash_received,
                                    change: this.change,
                                    receiptUrl: completedId ? `/sales/${completedId}/receipt` : null,
                                    printUrl: data.print_url || (completedId ? `/sales/${completedId}/print` : null)
                                };

                                window.posAudio && window.posAudio.playCashChime();

                                this.clearStorage();
                                this.resetForm();

                                this.$dispatch('open-modal', { name: 'sale-complete-modal' });
                                this.$dispatch('toast', { message: 'Transaction Successful!', type: 'success' });

                            } else if (res.status === 409 || data.conflict) {
                                window.posAudio && window.posAudio.playError();
                                this.$dispatch('toast', { message: data.message || 'Inventory conflict on server.', type: 'error' });
                            } else {
                                window.posAudio && window.posAudio.playError();
                                this.$dispatch('toast', { message: data.message || 'Error occurred during transaction.', type: 'error' });
                            }

                        } catch (e) {
                            console.warn('[POS] Network error, queuing transaction offline:', e);
                            const items = this.cart.map(item => ({
                                product_id: item.id,
                                quantity: parseInt(item.quantity) || 1,
                                unit_price: parseFloat(item.price) || 0,
                                discount: parseFloat(item.discount) || 0
                            }));
                            const payload = {
                                offline_uuid: transactionUuid,
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
                            await this.recordOfflineSale(payload, transactionUuid);
                        } finally {
                            this.isSubmitting = false;
                        }
                    },

                    async recordOfflineSale(payload, uuid) {
                        try {
                            if (window.PwaDB) {
                                await window.PwaDB.saveOfflineSale(payload);
                            }
                            this.$dispatch('close-modal', { name: 'confirmation-modal' });

                            this.lastCompletedSale = {
                                id: null,
                                invoice: 'OFFLINE-' + uuid.substring(0, 8),
                                total: this.total,
                                cashReceived: this.payment.cash_received,
                                change: this.change,
                                isOffline: true
                            };

                            window.posAudio && window.posAudio.playCashChime();

                            this.clearStorage();
                            this.resetForm();
                            await this.refreshOfflineSales();

                            this.$dispatch('open-modal', { name: 'sale-complete-modal' });
                            this.$dispatch('toast', {
                                message: `Offline Sale Saved! Queued locally (UUID: ${uuid.substring(0, 8)}...). It will automatically sync when internet reconnects.`,
                                type: 'info'
                            });
                        } catch (err) {
                            console.error('[POS] Failed to record offline sale:', err);
                            this.$dispatch('toast', { message: 'Failed to record offline sale: ' + err.message, type: 'error' });
                        }
                    },

                    async scanBarcode(code) {
                        if (!code) return null;
                        const cleanCode = String(code).trim();
                        try {
                            if (!navigator.onLine && window.PwaDB) {
                                const results = await window.PwaDB.searchProducts(cleanCode);
                                const match = (results || []).find(p =>
                                    (p.sku && p.sku.toLowerCase() === cleanCode.toLowerCase()) ||
                                    (p.name && p.name.toLowerCase() === cleanCode.toLowerCase())
                                ) || (results && results[0]);

                                if (match) {
                                    this.addToCart(match);
                                    return match;
                                }
                            }

                            const res = await fetch('{{ route("ajax.products.search") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ q: cleanCode })
                            });

                            if (!res.ok) throw new Error('Search failed');

                            const products = await res.json();
                            const match = (products || []).find(p =>
                                (p.sku && p.sku.toLowerCase() === cleanCode.toLowerCase()) ||
                                (p.name && p.name.toLowerCase() === cleanCode.toLowerCase())
                            ) || (products && products[0]);

                            if (match) {
                                this.addToCart(match);
                                return match;
                            } else {
                                window.posAudio && window.posAudio.playError();
                                this.$dispatch('toast', {
                                    message: 'Barcode "' + cleanCode + '" not found in store products!',
                                    type: 'error'
                                });
                                return null;
                            }
                        } catch (e) {
                            console.warn('[POS Barcode Error]:', e);
                            window.posAudio && window.posAudio.playError();
                            return null;
                        }
                    },

                    async openCameraScanner() {
                        this.isCameraOpen = true;
                        this.cameraLoading = true;
                        this.cameraError = null;
                        this.lastScannedItem = null;
                        this.lastScannedUnregisteredCode = null;

                        this.$nextTick(async () => {
                            try {
                                if (!window.posCamera) {
                                    throw new Error("Camera scanner library not ready. Please refresh the page.");
                                }
                                await window.posCamera.start('pos-camera-viewfinder', async (code) => {
                                    await this.handleCameraScan(code);
                                });
                                this.cameraLoading = false;
                                this.cameraHasTorch = window.posCamera.hasTorch;
                            } catch (err) {
                                console.error("[Camera Scan Error]:", err);
                                this.cameraLoading = false;
                                this.cameraError = (err && err.message) ? err.message : "Camera access denied. Please allow camera permissions in your browser.";
                            }
                        });
                    },

                    async handleCameraScan(code) {
                        if (!code) return;
                        const cleanCode = String(code).trim();
                        const match = await this.scanBarcode(cleanCode);
                        if (match) {
                            this.lastScannedItem = match;
                            this.lastScannedUnregisteredCode = null;
                            setTimeout(() => {
                                if (this.lastScannedItem === match) {
                                    this.lastScannedItem = null;
                                }
                            }, 3000);
                        } else {
                            this.lastScannedUnregisteredCode = cleanCode;
                            this.lastScannedItem = null;
                            setTimeout(() => {
                                if (this.lastScannedUnregisteredCode === cleanCode) {
                                    this.lastScannedUnregisteredCode = null;
                                }
                            }, 4000);
                        }
                    },

                    async toggleCameraTorch() {
                        if (window.posCamera) {
                            this.cameraTorchOn = await window.posCamera.toggleTorch();
                        }
                    },

                    async closeCameraScanner() {
                        if (window.posCamera) {
                            await window.posCamera.stop();
                        }
                        this.isCameraOpen = false;
                        this.cameraLoading = false;
                        this.cameraError = null;
                        this.lastScannedItem = null;
                    },

                    holdCurrentCart() {
                        if (this.cart.length === 0) {
                            this.$dispatch('toast', { message: 'Cart is empty!', type: 'warning' });
                            return;
                        }
                        const ticketId = Date.now();
                        const ticketNum = this.heldTickets.length + 1;
                        const ticket = {
                            id: ticketId,
                            title: 'Ticket #' + ticketNum,
                            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                            customer: this.selectedCustomer ? { ...this.selectedCustomer } : null,
                            cart: JSON.parse(JSON.stringify(this.cart)),
                            payment: { ...this.payment },
                            globalDiscount: this.globalDiscount,
                            total: this.total,
                            itemCount: this.cart.reduce((s, i) => s + (parseInt(i.quantity) || 1), 0)
                        };

                        this.heldTickets.push(ticket);
                        localStorage.setItem('pos_held_tickets', JSON.stringify(this.heldTickets));
                        this.clearStorage();
                        this.resetForm();
                        window.posAudio && window.posAudio.playBeep();
                        this.$dispatch('toast', { message: ticket.title + ' saved to Hold queue.', type: 'info' });
                    },

                    recallTicket(ticketId) {
                        const idx = this.heldTickets.findIndex(t => t.id === ticketId);
                        if (idx === -1) return;
                        const ticket = this.heldTickets[idx];

                        if (this.cart.length > 0) {
                            if (!confirm('Replace current cart with held ticket?')) {
                                return;
                            }
                        }

                        this.cart = ticket.cart || [];
                        this.selectedCustomer = ticket.customer || null;
                        this.payment = ticket.payment || { method: 'cash', cash_received: 0, notes: '' };
                        this.globalDiscount = ticket.globalDiscount || 0;

                        this.heldTickets.splice(idx, 1);
                        localStorage.setItem('pos_held_tickets', JSON.stringify(this.heldTickets));

                        this.$dispatch('close-modal', { name: 'recall-tickets-modal' });
                        window.posAudio && window.posAudio.playBeep();
                        this.$dispatch('toast', { message: ticket.title + ' resumed.', type: 'success' });
                    },

                    discardTicket(ticketId) {
                        this.heldTickets = this.heldTickets.filter(t => t.id !== ticketId);
                        localStorage.setItem('pos_held_tickets', JSON.stringify(this.heldTickets));
                        this.$dispatch('toast', { message: 'Held ticket discarded.', type: 'info' });
                    },

                    async refreshOfflineSales() {
                        if (!window.PwaDB) return;
                        try {
                            const all = await window.PwaDB.getAllSales();
                            this.offlineSalesList = all;
                            this.pendingSalesCount = all.filter(s => s.status === 'pending' || s.status === 'synchronizing').length;
                            this.conflictSalesCount = all.filter(s => s.status === 'requires_review').length;
                        } catch (err) {
                            console.warn('[POS] Failed to refresh offline sales:', err);
                        }
                    },

                    async syncOfflineSales() {
                        if (!navigator.onLine || !window.PwaDB || this.isSyncing) return;
                        this.isSyncing = true;
                        try {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            const result = await window.PwaDB.syncPendingSales('{{ route("sales.store") }}', token);
                            await this.refreshOfflineSales();

                            if (result.synced > 0) {
                                this.$dispatch('toast', {
                                    message: `Successfully synchronized ${result.synced} offline sale(s)!`,
                                    type: 'success'
                                });
                            }
                            if (result.conflicts > 0) {
                                this.$dispatch('toast', {
                                    message: `${result.conflicts} offline sale(s) require review due to inventory conflicts.`,
                                    type: 'error'
                                });
                            }
                        } catch (err) {
                            console.error('[POS] Sync error:', err);
                        } finally {
                            this.isSyncing = false;
                        }
                    },

                    async retryOfflineSale(uuid) {
                        if (!window.PwaDB) return;
                        await window.PwaDB.updateSale(uuid, { status: 'pending', last_error: null });
                        await this.refreshOfflineSales();
                        this.syncOfflineSales();
                    },

                    async discardOfflineSale(uuid) {
                        if (!confirm('Are you sure you want to discard this offline transaction? This action cannot be undone.')) {
                            return;
                        }
                        if (window.PwaDB) {
                            await window.PwaDB.deleteSale(uuid);
                            await this.refreshOfflineSales();
                            this.$dispatch('toast', { message: 'Offline transaction discarded.', type: 'info' });
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

        <!-- Offline Transactions Queue Modal -->
        <x-modal name="offline-queue-modal" focusable>
            <div class="p-6">
                <!-- Header -->
                <div class="mb-4 flex items-center justify-between border-b border-gray-200 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Offline Sales Queue
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Sales recorded offline are stored locally in IndexedDB with UUID idempotency.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="syncOfflineSales()"
                            :disabled="isSyncing || pendingSalesCount === 0 || !isOnline"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 text-white rounded-md text-xs font-semibold shadow-sm transition-colors"
                        >
                            <svg class="w-3.5 h-3.5" :class="isSyncing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span x-text="isSyncing ? 'Syncing...' : 'Sync Pending (' + pendingSalesCount + ')'"></span>
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex space-x-2 mb-4 border-b border-gray-100 pb-2">
                    <button
                        type="button"
                        @click="offlineFilter = 'all'"
                        class="px-2.5 py-1 rounded text-xs font-semibold transition-colors"
                        :class="offlineFilter === 'all' ? 'bg-indigo-100 text-indigo-800' : 'text-gray-500 hover:text-gray-800'"
                    >
                        All (<span x-text="offlineSalesList.length"></span>)
                    </button>
                    <button
                        type="button"
                        @click="offlineFilter = 'pending'"
                        class="px-2.5 py-1 rounded text-xs font-semibold transition-colors"
                        :class="offlineFilter === 'pending' ? 'bg-amber-100 text-amber-800' : 'text-gray-500 hover:text-gray-800'"
                    >
                        Pending (<span x-text="pendingSalesCount"></span>)
                    </button>
                    <button
                        type="button"
                        @click="offlineFilter = 'conflict'"
                        class="px-2.5 py-1 rounded text-xs font-semibold transition-colors"
                        :class="offlineFilter === 'conflict' ? 'bg-red-100 text-red-800' : 'text-gray-500 hover:text-gray-800'"
                    >
                        Requires Review (<span x-text="conflictSalesCount"></span>)
                    </button>
                </div>

                <!-- Transaction List -->
                <div class="max-h-96 overflow-y-auto space-y-3">
                    <template x-for="sale in offlineSalesList.filter(s => {
                        if (offlineFilter === 'pending') return s.status === 'pending' || s.status === 'synchronizing';
                        if (offlineFilter === 'conflict') return s.status === 'requires_review';
                        return true;
                    })" :key="sale.uuid">
                        <div class="p-3.5 rounded-lg border bg-white shadow-sm transition-all"
                             :class="{
                                'border-amber-300 bg-amber-50/30': sale.status === 'pending',
                                'border-blue-300 bg-blue-50/30': sale.status === 'synchronizing',
                                'border-emerald-300 bg-emerald-50/20': sale.status === 'synced',
                                'border-red-300 bg-red-50/40': sale.status === 'requires_review'
                             }">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-gray-900" x-text="'UUID: ' + sale.uuid.slice(0, 8) + '...'"></span>
                                        <!-- Status Badge -->
                                        <template x-if="sale.status === 'pending'">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-800">Pending Sync</span>
                                        </template>
                                        <template x-if="sale.status === 'synchronizing'">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800 animate-pulse">Syncing...</span>
                                        </template>
                                        <template x-if="sale.status === 'synced'">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800" x-text="'Synced: #' + (sale.invoice_number || 'OK')"></span>
                                        </template>
                                        <template x-if="sale.status === 'requires_review'">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-red-100 text-red-800">Conflict / Review</span>
                                        </template>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-1">
                                        Recorded: <span x-text="new Date(sale.created_at).toLocaleString()"></span>
                                        &bull; Items: <span class="font-semibold" x-text="sale.payload.items?.length || 0"></span>
                                        &bull; Method: <span class="capitalize font-semibold" x-text="sale.payload.payment_method"></span>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-xs font-bold text-gray-900">
                                        <span x-text="formatCurrency(sale.payload.items?.reduce((sum, it) => sum + ((it.unit_price - (it.discount || 0)) * it.quantity), 0) - (sale.payload.global_discount || 0))"></span>
                                    </div>
                                    <!-- Action Buttons -->
                                    <div class="mt-2 flex items-center justify-end gap-1.5">
                                        <template x-if="sale.status === 'requires_review' || sale.status === 'pending'">
                                            <button
                                                type="button"
                                                @click="retryOfflineSale(sale.uuid)"
                                                class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded text-[11px] font-semibold transition-colors"
                                            >
                                                Retry
                                            </button>
                                        </template>
                                        <template x-if="sale.status !== 'synced'">
                                            <button
                                                type="button"
                                                @click="discardOfflineSale(sale.uuid)"
                                                class="px-2 py-1 bg-red-50 hover:bg-red-100 text-red-700 rounded text-[11px] font-semibold transition-colors"
                                            >
                                                Discard
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Conflict Error Message -->
                            <template x-if="sale.last_error">
                                <div class="mt-2 p-2 bg-red-100/80 border border-red-200 rounded text-[11px] text-red-800 flex items-start gap-1.5">
                                    <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <div class="flex-1">
                                        <strong>Server Note:</strong> <span x-text="sale.last_error"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="offlineSalesList.length === 0">
                        <div class="py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm font-medium text-gray-600">No offline transactions queued</p>
                            <p class="text-xs text-gray-400 mt-1">All sales are up to date with the server.</p>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="mt-6 border-t border-gray-100 pt-3 flex justify-end">
                    <button
                        type="button"
                        @click="$dispatch('close-modal', { name: 'offline-queue-modal' })"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md text-xs font-semibold transition-colors"
                    >
                        Close
                    </button>
                </div>
            </div>
        </x-modal>

        <!-- Held Cart Tickets Modal -->
        <x-modal name="recall-tickets-modal" focusable maxWidth="lg">
            <div class="p-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-purple-50 text-purple-700 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Held Cart Tickets</h3>
                            <p class="text-xs text-gray-500">Resume or discard parked transactions</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800" x-text="heldTickets.length + ' Tickets'"></span>
                </div>

                <div class="space-y-3 max-h-[60vh] overflow-y-auto">
                    <template x-for="(ticket, idx) in heldTickets" :key="ticket.id">
                        <div class="p-3.5 bg-gray-50/80 rounded-xl border border-gray-200 hover:border-purple-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-sm text-gray-900" x-text="ticket.title"></h4>
                                    <span class="text-xs text-gray-400" x-text="'&bull; ' + ticket.time"></span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Customer: <span class="font-medium text-gray-700" x-text="ticket.customer ? ticket.customer.name : 'Guest'"></span>
                                    &bull; Items: <span class="font-medium text-gray-700" x-text="ticket.itemCount"></span>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5 truncate max-w-xs" x-text="ticket.cart.map(i => i.quantity + 'x ' + i.name).join(', ')"></div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-3 pt-2 sm:pt-0 border-t sm:border-0 border-gray-200">
                                <div class="text-right">
                                    <div class="text-xs text-gray-400">Total</div>
                                    <div class="text-sm font-bold text-indigo-600" x-text="formatCurrency(ticket.total)"></div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click="recallTicket(ticket.id)"
                                        class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors"
                                    >
                                        Resume Cart
                                    </button>
                                    <button
                                        type="button"
                                        @click="discardTicket(ticket.id)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Discard Ticket"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="heldTickets.length === 0">
                        <div class="py-12 text-center text-gray-400">
                            <p class="text-sm font-medium text-gray-600">No held cart tickets</p>
                            <p class="text-xs text-gray-400 mt-1">Use "Hold Cart" when a customer needs to pause checkout.</p>
                        </div>
                    </template>
                </div>

                <div class="mt-5 border-t border-gray-100 pt-3 flex justify-end">
                    <button
                        type="button"
                        @click="$dispatch('close-modal', { name: 'recall-tickets-modal' })"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-semibold transition-colors"
                    >
                        Close
                    </button>
                </div>
            </div>
        </x-modal>

        <!-- Real POS Post-Checkout Completion Modal -->
        <x-modal name="sale-complete-modal" focusable maxWidth="md">
            <div class="p-6 text-center space-y-4" x-data x-show="lastCompletedSale">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>

                <div>
                    <h3 class="text-xl font-bold text-gray-900">Payment Completed!</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Invoice <span class="font-mono font-bold text-gray-800" x-text="'#' + (lastCompletedSale?.invoice || '')"></span>
                    </p>
                </div>

                <!-- Large Change Due Display -->
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl max-w-xs mx-auto">
                    <span class="text-xs uppercase font-bold text-emerald-700 tracking-wider">Change Due</span>
                    <div class="text-3xl font-black text-emerald-800 mt-0.5" x-text="formatCurrency(lastCompletedSale?.change || 0)"></div>
                </div>

                <!-- Print & New Sale Buttons -->
                <div class="pt-3 flex flex-col gap-2 border-t border-gray-100">
                    <template x-if="lastCompletedSale?.receiptUrl">
                        <button
                            type="button"
                            @click="window.open(lastCompletedSale.receiptUrl, '_blank', 'width=420,height=650')"
                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print Thermal Receipt (Enter)
                        </button>
                    </template>

                    <template x-if="lastCompletedSale?.printUrl">
                        <button
                            type="button"
                            @click="window.open(lastCompletedSale.printUrl, '_blank')"
                            class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-semibold transition-colors"
                        >
                            Print Full A5 Invoice
                        </button>
                    </template>

                    <button
                        type="button"
                        @click="$dispatch('close-modal', { name: 'sale-complete-modal' }); lastCompletedSale = null;"
                        class="w-full py-2.5 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-semibold transition-colors"
                    >
                        Next Customer / New Sale (Space)
                    </button>
                </div>
            </div>
        </x-modal>

        <!-- Phone Camera Barcode Scanner Modal -->
        <div
            x-show="isCameraOpen"
            x-cloak
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-black/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-4"
            @keydown.escape.window="closeCameraScanner()"
        >
            <div
                class="relative w-full max-w-md bg-slate-900 border border-slate-800 text-white rounded-2xl overflow-hidden shadow-2xl flex flex-col"
                @click.stop
            >
                <!-- Scanner Header -->
                <div class="p-3.5 sm:p-4 bg-slate-950/90 backdrop-blur-md flex items-center justify-between border-b border-slate-800 z-20">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600/30 border border-indigo-500/50 flex items-center justify-center text-indigo-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-white leading-tight">Barcode Scanner</h3>
                            <p class="text-[11px] text-slate-400">Aim camera at barcode</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <!-- Torch/Flashlight Toggle -->
                        <button
                            type="button"
                            x-show="cameraHasTorch"
                            @click="toggleCameraTorch()"
                            class="p-2 rounded-lg transition-colors border"
                            :class="cameraTorchOn ? 'bg-amber-400 text-slate-900 border-amber-300' : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700'"
                            title="Toggle Flashlight"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        </button>

                        <!-- Close Button -->
                        <button
                            type="button"
                            @click="closeCameraScanner()"
                            class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Camera Viewport Area -->
                <div class="relative bg-black w-full min-h-[340px] max-h-[460px] flex items-center justify-center overflow-hidden">
                    <!-- Loading state -->
                    <div x-show="cameraLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 z-20 gap-3">
                        <div class="w-10 h-10 border-3 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                        <p class="text-xs text-slate-300 font-medium">Starting camera...</p>
                        <p class="text-[11px] text-slate-500">Please tap "Allow" if your browser prompts for permission</p>
                    </div>

                    <!-- Error state -->
                    <div x-show="cameraError" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 p-6 text-center z-20 gap-3">
                        <div class="w-12 h-12 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h4 class="font-bold text-sm text-white">Camera Access Required</h4>
                        <p class="text-xs text-slate-400 max-w-xs" x-text="cameraError"></p>
                        <button
                            type="button"
                            @click="openCameraScanner()"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold mt-2 shadow-md"
                        >
                            Retry Camera
                        </button>
                    </div>

                    <!-- Video Mount Container -->
                    <div id="pos-camera-viewfinder" class="w-full h-full min-h-[340px]"></div>

                    <!-- Scanning Reticle Laser Overlay -->
                    <div x-show="!cameraLoading && !cameraError" class="absolute inset-0 pointer-events-none flex items-center justify-center z-10">
                        <div class="w-[82%] h-44 border-2 border-indigo-400/80 rounded-2xl relative shadow-[0_0_25px_rgba(99,102,241,0.3)]">
                            <span class="absolute -top-1 -left-1 w-4 h-4 border-t-3 border-l-3 border-indigo-400 rounded-tl"></span>
                            <span class="absolute -top-1 -right-1 w-4 h-4 border-t-3 border-r-3 border-indigo-400 rounded-tr"></span>
                            <span class="absolute -bottom-1 -left-1 w-4 h-4 border-b-3 border-l-3 border-indigo-400 rounded-bl"></span>
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 border-b-3 border-r-3 border-indigo-400 rounded-br"></span>
                            <div class="w-full h-[2px] bg-gradient-to-r from-transparent via-red-500 to-transparent shadow-[0_0_8px_rgba(239,68,68,1)] animate-bounce mt-20"></div>
                        </div>
                    </div>

                    <!-- Instant Scanned Item Toast Banner (Success Match) -->
                    <template x-if="lastScannedItem">
                        <div class="absolute bottom-4 inset-x-4 bg-emerald-600/95 backdrop-blur-md text-white p-3 rounded-xl shadow-lg flex items-center justify-between z-30 border border-emerald-400/40">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-full bg-white text-emerald-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    ✓
                                </div>
                                <div class="truncate">
                                    <div class="font-bold text-xs truncate" x-text="lastScannedItem.name"></div>
                                    <div class="text-[11px] text-emerald-100" x-text="formatCurrency(lastScannedItem.price) + ' (Added)'"></div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-emerald-800/80 px-2 py-0.5 rounded text-emerald-200 flex-shrink-0 ml-2">
                                In Cart
                            </span>
                        </div>
                    </template>

                    <!-- Unregistered Barcode Notice (Camera detected code, but no product matched) -->
                    <template x-if="lastScannedUnregisteredCode">
                        <div class="absolute bottom-4 inset-x-4 bg-amber-600/95 backdrop-blur-md text-white p-3 rounded-xl shadow-lg flex items-center justify-between z-30 border border-amber-400/40 animate-pulse">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-full bg-white text-amber-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    !
                                </div>
                                <div class="truncate">
                                    <div class="font-bold text-xs truncate">Barcode Detected: <span class="font-mono text-amber-100 underline" x-text="lastScannedUnregisteredCode"></span></div>
                                    <div class="text-[11px] text-amber-100">Not assigned to any product yet</div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-amber-800/80 px-2 py-0.5 rounded text-amber-200 flex-shrink-0 ml-2">
                                No Match
                            </span>
                        </div>
                    </template>
                </div>

                <!-- Scanner Footer -->
                <div class="p-3.5 sm:p-4 bg-slate-950 border-t border-slate-800 flex items-center justify-between z-20">
                    <div class="text-xs text-slate-400">
                        <span>Cart:</span>
                        <span class="font-bold text-white text-sm ml-1" x-text="cart.reduce((s, i) => s + (parseInt(i.quantity) || 1), 0) + ' items'">0 items</span>
                    </div>

                    <button
                        type="button"
                        @click="closeCameraScanner()"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition-all active:scale-95 flex items-center gap-1.5"
                    >
                        <span>Done & View Cart &rarr;</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Listen for customer created -->
        <div @customer-created.window="selectedCustomer = $event.detail; customerSearch = '';"></div>
    </div>
</x-app-layout>
