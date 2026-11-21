<div>
    {{-- Global Container: Mobile-First with Desktop Two-Panel Layout --}}
    <div class="space-y-4">

        {{-- Top Bar: Title & Mode Switcher --}}
        <div class="flex items-center justify-between gap-3 bg-white px-4 py-3 sm:py-3.5 rounded-2xl border border-gray-100 shadow-xs">
            <div class="flex items-center gap-2.5">
                @if($step !== 'select' && $step !== 'completed')
                    <button
                        type="button"
                        wire:click="{{ $step === 'payment' ? 'goToReview' : 'goToSelect' }}"
                        class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors lg:hidden"
                        title="Back to previous step"
                    >
                        <x-heroicon-o-arrow-left class="w-5 h-5" />
                    </button>
                @endif
                <div>
                    <h1 class="text-lg sm:text-xl font-bold tracking-tight text-gray-900 flex items-center gap-2">
                        <span>Record Sale</span>
                        <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Quick Sale
                        </span>
                    </h1>
                    <p class="text-xs text-gray-400 hidden sm:block">Select products, enter payment, and record sale in seconds.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Switch to Advanced POS Register --}}
                <a
                    href="{{ route('sales.pos') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-brand bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl transition-colors"
                    title="Open Advanced POS with barcode scanner and hold cart"
                >
                    <x-heroicon-o-computer-desktop class="w-4 h-4 text-gray-500" />
                    <span class="hidden sm:inline">Advanced POS</span>
                    <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5 opacity-60" />
                </a>
            </div>
        </div>

        {{-- =========================================================================
             SCREEN D: SALE COMPLETED (SUCCESS SCREEN)
             ========================================================================= --}}
        @if($step === 'completed')
            <div class="max-w-md mx-auto bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 text-center space-y-5 animate-fade-in">
                {{-- Success Green Checkmark --}}
                <div class="w-20 h-20 rounded-full bg-emerald-50 border-4 border-emerald-100 flex items-center justify-center mx-auto text-emerald-600 shadow-inner">
                    <x-heroicon-o-check class="w-10 h-10 stroke-[2.5]" />
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        Transaction Completed
                    </span>
                    <h2 class="text-2xl font-black text-gray-900 mt-2">Sale Recorded!</h2>
                    <p class="font-mono text-sm font-bold text-gray-500 mt-0.5">
                        Invoice #{{ $completedInvoice }}
                    </p>
                </div>

                {{-- Amount Paid & Change Summary Card --}}
                <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100 space-y-2.5 text-sm">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Sale Total</span>
                        <span class="font-mono font-bold text-gray-900 text-base">
                            ETB {{ number_format($completedTotal, 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-gray-600">
                        <span>Payment Method</span>
                        <span class="font-semibold text-gray-800 capitalize">
                            {{ $completedMethod === 'cash' ? 'Cash' : 'Bank Transfer' }}
                        </span>
                    </div>

                    @if($completedMethod === 'cash')
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Cash Received</span>
                            <span class="font-mono font-semibold text-gray-800">
                                ETB {{ number_format($completedPaid, 2) }}
                            </span>
                        </div>

                        <div class="pt-2 border-t border-gray-200/60 flex justify-between items-center text-emerald-700 font-bold">
                            <span>Change Returned</span>
                            <span class="font-mono text-lg text-emerald-800 font-black">
                                ETB {{ number_format($completedChange, 2) }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Print & Share Receipt Actions --}}
                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <a
                        href="{{ route('sales.receipt', $completedSaleId) }}"
                        target="_blank"
                        class="h-12 inline-flex items-center justify-center gap-1.5 px-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition-colors"
                    >
                        <x-heroicon-o-receipt-percent class="w-4 h-4 text-gray-600" />
                        <span>Thermal Receipt</span>
                    </a>

                    <a
                        href="{{ route('sales.print', $completedSaleId) }}"
                        target="_blank"
                        class="h-12 inline-flex items-center justify-center gap-1.5 px-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition-colors"
                    >
                        <x-heroicon-o-printer class="w-4 h-4 text-gray-600" />
                        <span>A4 Invoice</span>
                    </a>
                </div>

                {{-- Primary Button: New Sale --}}
                <button
                    type="button"
                    wire:click="startNewSale"
                    class="w-full h-13 inline-flex items-center justify-center gap-2 px-4 rounded-2xl bg-brand hover:bg-brand-700 text-white font-bold text-sm shadow-md transition-all active:scale-98"
                >
                    <x-heroicon-o-plus class="w-5 h-5" />
                    <span>Start New Sale</span>
                </button>

                <div>
                    <a
                        href="{{ route('sales.index') }}"
                        class="text-xs font-semibold text-gray-500 hover:text-brand transition-colors inline-flex items-center gap-1"
                    >
                        <span>View Sales History</span>
                        <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>

        {{-- =========================================================================
             MOBILE STEP B: REVIEW SALE (QUANTITY ADJUSTMENTS & TOTALS)
             ========================================================================= --}}
        @elseif($step === 'review')
            <div class="max-w-2xl mx-auto space-y-4 animate-fade-in lg:hidden">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-4 sm:p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Your Sale</h2>
                            <p class="text-xs text-gray-500">{{ count($cart) }} {{ \Illuminate\Support\Str::plural('product', count($cart)) }} selected</p>
                        </div>
                        <button
                            type="button"
                            wire:click="clearCart"
                            wire:confirm="Clear all items from this sale?"
                            class="text-xs text-rose-600 hover:text-rose-700 font-semibold p-1"
                        >
                            Clear All
                        </button>
                    </div>

                    {{-- Items List --}}
                    <div class="space-y-3 divide-y divide-gray-100">
                        @foreach($cart as $item)
                            <div class="pt-3 first:pt-0 flex items-center justify-between gap-3">
                                {{-- Thumbnail & Info --}}
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 shrink-0 overflow-hidden flex items-center justify-center text-gray-400">
                                        @if($item['image_url'])
                                            <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                        @else
                                            <x-heroicon-o-cube class="w-6 h-6 stroke-1 text-gray-300" />
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-sm font-bold text-gray-900 truncate">{{ $item['name'] }}</h3>
                                        <div class="text-xs text-gray-500 font-mono">
                                            ETB {{ number_format($item['selling_price'], 2) }} / {{ $item['unit'] }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Controls & Subtotal --}}
                                <div class="flex items-center gap-2.5 shrink-0">
                                    {{-- + / - Quantity Controls (>= 44px touch targets on mobile) --}}
                                    <div class="flex items-center rounded-xl border border-gray-200 bg-gray-50/80 p-0.5 select-none">
                                        <button
                                            type="button"
                                            wire:click="decrementQty({{ $item['product_id'] }})"
                                            class="w-9 h-9 rounded-lg bg-white shadow-2xs hover:bg-gray-100 text-gray-700 font-bold flex items-center justify-center text-base transition-all active:scale-90"
                                            aria-label="Decrease quantity"
                                        >
                                            &minus;
                                        </button>
                                        <input
                                            type="number"
                                            min="1"
                                            max="{{ $item['max_quantity'] }}"
                                            value="{{ $item['quantity'] }}"
                                            wire:change="updateQty({{ $item['product_id'] }}, $event.target.value)"
                                            class="w-11 h-9 p-0 text-center font-mono font-bold text-sm text-gray-900 bg-transparent border-none focus:ring-1 focus:ring-brand rounded"
                                        />
                                        <button
                                            type="button"
                                            wire:click="incrementQty({{ $item['product_id'] }})"
                                            class="w-9 h-9 rounded-lg bg-white shadow-2xs hover:bg-gray-100 text-gray-700 font-bold flex items-center justify-center text-base transition-all active:scale-90"
                                            aria-label="Increase quantity"
                                        >
                                            &plus;
                                        </button>
                                    </div>

                                    <div class="text-right w-20">
                                        <span class="font-mono font-bold text-sm text-gray-900 block">
                                            ETB {{ number_format($item['quantity'] * $item['selling_price'], 2) }}
                                        </span>
                                    </div>

                                    <button
                                        type="button"
                                        wire:click="removeFromCart({{ $item['product_id'] }})"
                                        class="w-9 h-9 rounded-xl text-gray-300 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors"
                                        title="Remove item"
                                    >
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Summary Box --}}
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 space-y-2.5 font-mono text-sm pt-4">
                        <div class="flex justify-between text-gray-600 font-sans">
                            <span>Subtotal</span>
                            <span class="font-bold font-mono text-gray-900">ETB {{ number_format($this->subtotal, 2) }}</span>
                        </div>

                        {{-- Discount Input / Display --}}
                        <div class="flex items-center justify-between text-xs font-sans text-gray-600">
                            <span class="flex items-center gap-1">
                                <x-heroicon-o-tag class="w-3.5 h-3.5 text-gray-400" />
                                <span>Discount (ETB)</span>
                            </span>
                            <div class="flex items-center gap-1 font-mono">
                                <input
                                    type="number"
                                    inputmode="decimal"
                                    step="any"
                                    min="0"
                                    max="{{ $this->subtotal }}"
                                    wire:model.live.debounce.300ms="globalDiscount"
                                    placeholder="0.00"
                                    class="w-24 h-8 text-xs font-mono font-bold text-right rounded-lg border border-gray-300 px-2 focus:ring-1 focus:ring-brand focus:border-brand bg-white"
                                />
                                @if($globalDiscount > 0)
                                    <button type="button" wire:click="$set('globalDiscount', 0)" class="text-gray-400 hover:text-rose-600 p-1" title="Remove discount">
                                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if($globalDiscount > 0)
                            <div class="flex justify-between text-rose-600 font-sans text-xs">
                                <span>Discount applied</span>
                                <span class="font-mono font-bold">- ETB {{ number_format((float)$globalDiscount, 2) }}</span>
                            </div>
                        @endif

                        <div class="pt-2 border-t border-gray-200 flex justify-between items-center text-base">
                            <span class="font-bold text-gray-900 font-sans">Total</span>
                            <span class="font-black text-brand text-xl">
                                ETB {{ number_format($this->total, 2) }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-2 pt-2">
                        <button
                            type="button"
                            wire:click="goToPayment"
                            class="w-full h-13 inline-flex items-center justify-center gap-2 rounded-2xl bg-brand hover:bg-brand-700 text-white font-bold text-base shadow-sm transition-all active:scale-98"
                        >
                            <span>Continue to Payment</span>
                            <x-heroicon-o-arrow-right class="w-5 h-5" />
                        </button>

                        <button
                            type="button"
                            wire:click="goToSelect"
                            class="w-full h-11 inline-flex items-center justify-center gap-1.5 rounded-xl text-gray-600 hover:text-gray-900 font-semibold text-xs transition-colors"
                        >
                            <x-heroicon-o-plus class="w-4 h-4" />
                            <span>Add more products</span>
                        </button>
                    </div>
                </div>
            </div>

        {{-- =========================================================================
             MOBILE STEP C: SIMPLE PAYMENT (TOTAL, CASH RECEIVED, CHANGE)
             ========================================================================= --}}
        @elseif($step === 'payment')
            <div class="max-w-md mx-auto space-y-4 animate-fade-in lg:hidden">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5 sm:p-6 space-y-5">
                    {{-- TOTAL TO PAY Banner --}}
                    <div class="text-center bg-gray-50/80 rounded-2xl p-4 border border-gray-100">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Total To Pay</span>
                        <div class="text-3xl font-black text-brand font-mono tracking-tight mt-1">
                            ETB {{ number_format($this->total, 2) }}
                        </div>
                        <span class="text-xs text-gray-500 mt-0.5 block">
                            {{ count($cart) }} {{ \Illuminate\Support\Str::plural('product', count($cart)) }} · {{ $this->totalUnits }} total units
                        </span>
                    </div>

                    {{-- Payment Method (Cash vs Transfer) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Payment Method</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                type="button"
                                wire:click="setPaymentMethod('cash')"
                                class="h-12 rounded-xl flex items-center justify-center gap-2 text-xs font-bold transition-all border {{ $paymentMethod === 'cash' ? 'bg-brand text-white border-brand shadow-xs' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}"
                            >
                                <x-heroicon-o-banknotes class="w-4 h-4" />
                                <span>Cash</span>
                            </button>

                            <button
                                type="button"
                                wire:click="setPaymentMethod('transfer')"
                                class="h-12 rounded-xl flex items-center justify-center gap-2 text-xs font-bold transition-all border {{ $paymentMethod === 'transfer' ? 'bg-brand text-white border-brand shadow-xs' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}"
                            >
                                <x-heroicon-o-credit-card class="w-4 h-4" />
                                <span>Bank Transfer</span>
                            </button>
                        </div>
                    </div>

                    {{-- Cash Received Input & Quick Amount Chips --}}
                    @if($paymentMethod === 'cash')
                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Cash Received</label>

                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-mono text-sm font-bold text-gray-400">ETB</span>
                                <input
                                    type="number"
                                    inputmode="decimal"
                                    step="any"
                                    wire:model.live="cashReceived"
                                    placeholder="0.00"
                                    class="w-full h-13 pl-14 pr-4 font-mono text-xl font-bold text-gray-900 bg-gray-50/60 border border-gray-200 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-all"
                                />
                            </div>

                            {{-- Quick Amount Chips --}}
                            <div>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block mb-1.5">Quick Amounts</span>
                                <div class="grid grid-cols-4 gap-1.5 text-xs font-mono font-bold">
                                    <button
                                        type="button"
                                        wire:click="setExactAmount"
                                        class="h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors"
                                    >
                                        Exact
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="addCash(10)"
                                        class="h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors"
                                    >
                                        +10
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="addCash(20)"
                                        class="h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors"
                                    >
                                        +20
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="addCash(50)"
                                        class="h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors"
                                    >
                                        +50
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="addCash(100)"
                                        class="h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors"
                                    >
                                        +100
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="addCash(200)"
                                        class="h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors"
                                    >
                                        +200
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="addCash(500)"
                                        class="h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors"
                                    >
                                        +500
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="addCash(1000)"
                                        class="h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors"
                                    >
                                        +1,000
                                    </button>
                                </div>
                            </div>

                            {{-- Automated Change / Remaining Balance Calculation --}}
                            @if($this->difference > 0)
                                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center justify-between text-emerald-800">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600 shrink-0" />
                                        <span class="font-bold text-xs">Change to Return</span>
                                    </div>
                                    <span class="font-mono text-xl font-black">
                                        ETB {{ number_format($this->change, 2) }}
                                    </span>
                                </div>
                            @elseif($this->difference == 0)
                                <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-3.5 flex items-center justify-between text-emerald-800 text-xs font-bold">
                                    <span class="flex items-center gap-1.5">
                                        <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-600" />
                                        <span>Exact Payment Received</span>
                                    </span>
                                    <span class="font-mono">ETB 0.00 change</span>
                                </div>
                            @else
                                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 flex items-center justify-between text-rose-800">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-rose-600 shrink-0" />
                                        <div>
                                            <span class="font-bold text-xs block">Remaining Balance</span>
                                            <span class="text-[11px] text-rose-600">Enter full amount to complete</span>
                                        </div>
                                    </div>
                                    <span class="font-mono text-lg font-black text-rose-700">
                                        ETB {{ number_format($this->remainingBalance, 2) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Optional Note --}}
                    <div>
                        <input
                            type="text"
                            wire:model="notes"
                            placeholder="Optional note or customer reference..."
                            class="w-full text-base sm:text-xs bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand"
                        />
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-2 pt-2">
                        <button
                            type="button"
                            wire:click="confirmSale"
                            wire:loading.attr="disabled"
                            class="w-full h-14 inline-flex items-center justify-center gap-2 rounded-2xl bg-brand hover:bg-brand-700 disabled:opacity-50 text-white font-bold text-base shadow-sm transition-all active:scale-98"
                        >
                            <span wire:loading.remove wire:target="confirmSale">
                                Confirm Sale · ETB {{ number_format($this->total, 2) }}
                            </span>
                            <span wire:loading wire:target="confirmSale" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Recording Sale...</span>
                            </span>
                        </button>

                        <button
                            type="button"
                            wire:click="goToReview"
                            class="w-full h-10 inline-flex items-center justify-center gap-1.5 rounded-xl text-gray-500 hover:text-gray-900 font-semibold text-xs"
                        >
                            <x-heroicon-o-arrow-left class="w-4 h-4" />
                            <span>Back to cart review</span>
                        </button>
                    </div>
                </div>
            </div>

        {{-- =========================================================================
             SCREEN A & DESKTOP TWO-PANEL WORKFLOW
             ========================================================================= --}}
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

                {{-- =====================================================================
                     LEFT PANEL: PRODUCT SELECTION & SEARCH (65% on Desktop)
                     ===================================================================== --}}
                <div class="lg:col-span-7 xl:col-span-8 space-y-4">
                    {{-- Search Bar & Category Filter Card --}}
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs space-y-3">
                        {{-- Search Input --}}
                        <div class="relative">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                            <input
                                type="text"
                                wire:model.live.debounce.250ms="search"
                                wire:keydown.enter.prevent="handleBarcodeSearch"
                                placeholder="Search products by name, SKU, or barcode..."
                                class="w-full h-11 pl-11 pr-10 text-base sm:text-sm bg-gray-50/70 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-all"
                            />
                            @if($search !== '')
                                <button
                                    type="button"
                                    wire:click="$set('search', '')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1"
                                >
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                </button>
                            @endif
                        </div>

                        {{-- Category Shortcuts Pills --}}
                        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 text-xs select-none touch-scroll">
                            <button
                                type="button"
                                wire:click="selectCategory(null)"
                                class="px-3 py-1.5 rounded-xl font-medium whitespace-nowrap transition-colors {{ is_null($selectedCategory) ? 'bg-brand text-white shadow-xs font-semibold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                            >
                                All Products
                            </button>
                            @foreach($categories as $category)
                                <button
                                    type="button"
                                    wire:click="selectCategory({{ $category->id }})"
                                    class="px-3 py-1.5 rounded-xl font-medium whitespace-nowrap transition-colors {{ $selectedCategory === $category->id ? 'bg-brand text-white shadow-xs font-semibold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                >
                                    {{ $category->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Products Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                        @forelse($products as $product)
                            @php
                                $inCartQty = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
                                $isOutOfStock = $product->quantity <= 0;
                            @endphp
                            <div
                                wire:key="prod-{{ $product->id }}"
                                class="bg-white rounded-2xl border transition-all relative flex flex-col justify-between p-3 shadow-2xs {{ $inCartQty > 0 ? 'border-brand ring-2 ring-brand/20' : 'border-gray-100 hover:border-gray-200' }}"
                            >
                                {{-- In-Cart Badge --}}
                                @if($inCartQty > 0)
                                    <div class="absolute top-2.5 right-2.5 z-10 w-6 h-6 rounded-full bg-brand text-white font-mono font-bold text-xs flex items-center justify-center shadow-xs">
                                        {{ $inCartQty }}
                                    </div>
                                @endif

                                <div>
                                    {{-- Thumbnail --}}
                                    <div class="w-full h-28 rounded-xl bg-gray-50 border border-gray-100/80 overflow-hidden flex items-center justify-center text-gray-300 relative">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <x-heroicon-o-cube class="w-10 h-10 stroke-1 text-gray-300" />
                                        @endif

                                        {{-- Stock Indicator Pill --}}
                                        <div class="absolute bottom-1.5 left-1.5">
                                            @if($isOutOfStock)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700">
                                                    Out of stock
                                                </span>
                                            @elseif($product->quantity <= $product->min_stock)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">
                                                    {{ $product->quantity }} left
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-white/90 backdrop-blur-xs text-gray-600 border border-gray-200">
                                                    {{ $product->quantity }} in stock
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Name & Unit --}}
                                    <div class="mt-2.5">
                                        <h3 class="text-xs sm:text-sm font-bold text-gray-900 line-clamp-2 leading-snug" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </h3>
                                        <p class="text-[11px] text-gray-400 font-mono mt-0.5">
                                            {{ $product->unit?->symbol ?: $product->unit?->name ?: 'pcs' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Price & Add Action --}}
                                <div class="mt-3 pt-2 border-t border-gray-100 flex items-center justify-between gap-1.5">
                                    <div>
                                        <span class="text-[10px] text-gray-400 block leading-none">Price</span>
                                        <span class="font-mono font-black text-xs sm:text-sm text-gray-900 mt-0.5 block">
                                            ETB {{ number_format($product->selling_price, 2) }}
                                        </span>
                                    </div>

                                    <button
                                        type="button"
                                        wire:click="addToCart({{ $product->id }})"
                                        @if($isOutOfStock) disabled @endif
                                        class="h-9 px-3 rounded-xl font-bold text-xs inline-flex items-center gap-1 transition-all active:scale-95 shadow-2xs {{ $isOutOfStock ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ($inCartQty > 0 ? 'bg-brand text-white hover:bg-brand-700' : 'bg-brand-50 hover:bg-brand-100 text-brand border border-brand-200/80') }}"
                                    >
                                        <x-heroicon-o-plus class="w-3.5 h-3.5" />
                                        <span>Add</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-gray-100 p-8">
                                <x-heroicon-o-cube class="w-10 h-10 text-gray-300 stroke-1 mx-auto" />
                                <p class="mt-2 text-sm font-bold text-gray-700">No products found</p>
                                <p class="text-xs text-gray-400 mt-0.5">Try searching for something else or pick a different category.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- =====================================================================
                     RIGHT PANEL: CURRENT SALE CART & DESKTOP CHECKOUT (35% on Desktop)
                     ===================================================================== --}}
                <div class="hidden lg:block lg:col-span-5 xl:col-span-4 sticky top-4">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 space-y-4">
                        {{-- Cart Header --}}
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-bold text-gray-900">Current Sale</h2>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-brand-50 text-brand border border-brand-200">
                                    {{ count($cart) }}
                                </span>
                            </div>

                            @if(!empty($cart))
                                <button
                                    type="button"
                                    wire:click="clearCart"
                                    wire:confirm="Clear all products from current sale?"
                                    class="text-xs font-semibold text-rose-600 hover:text-rose-700 transition-colors"
                                >
                                    Clear
                                </button>
                            @endif
                        </div>

                        {{-- Cart Content --}}
                        @if(empty($cart))
                            {{-- Empty Cart Helpful State --}}
                            <div class="py-12 text-center space-y-2">
                                <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 text-gray-300 flex items-center justify-center mx-auto">
                                    <x-heroicon-o-shopping-bag class="w-7 h-7 stroke-1" />
                                </div>
                                <h3 class="text-sm font-bold text-gray-700">Your sale is empty</h3>
                                <p class="text-xs text-gray-400 max-w-xs mx-auto">
                                    Search for a product or tap "+ Add" on the left catalog to start recording a sale.
                                </p>
                            </div>
                        @else
                            {{-- Itemized Cart List --}}
                            <div class="space-y-2.5 max-h-[300px] overflow-y-auto pr-1 divide-y divide-gray-100">
                                @foreach($cart as $item)
                                    <div class="pt-2.5 first:pt-0 flex items-center justify-between gap-2 text-xs">
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-bold text-gray-900 truncate">{{ $item['name'] }}</h4>
                                            <span class="text-gray-400 font-mono text-[11px]">
                                                ETB {{ number_format($item['selling_price'], 2) }} / {{ $item['unit'] }}
                                            </span>
                                        </div>

                                        {{-- Quantity Controls --}}
                                        <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 p-0.5 shrink-0">
                                            <button
                                                type="button"
                                                wire:click="decrementQty({{ $item['product_id'] }})"
                                                class="w-6 h-6 rounded bg-white hover:bg-gray-100 text-gray-700 font-bold flex items-center justify-center text-xs shadow-2xs"
                                            >
                                                &minus;
                                            </button>
                                            <span class="w-7 text-center font-mono font-bold text-gray-900">
                                                {{ $item['quantity'] }}
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="incrementQty({{ $item['product_id'] }})"
                                                class="w-6 h-6 rounded bg-white hover:bg-gray-100 text-gray-700 font-bold flex items-center justify-center text-xs shadow-2xs"
                                            >
                                                &plus;
                                            </button>
                                        </div>

                                        <div class="w-18 text-right font-mono font-bold text-gray-900 shrink-0">
                                            ETB {{ number_format($item['quantity'] * $item['selling_price'], 2) }}
                                        </div>

                                        <button
                                            type="button"
                                            wire:click="removeFromCart({{ $item['product_id'] }})"
                                            class="text-gray-300 hover:text-rose-600 transition-colors p-0.5"
                                        >
                                            <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Financial Calculations --}}
                            <div class="bg-gray-50/80 rounded-xl p-3.5 border border-gray-100 space-y-2 font-mono text-xs">
                                <div class="flex justify-between text-gray-600 font-sans">
                                    <span>Subtotal</span>
                                    <span class="font-bold text-gray-900 font-mono">ETB {{ number_format($this->subtotal, 2) }}</span>
                                </div>

                                {{-- Discount Row --}}
                                <div class="flex items-center justify-between text-[11px] font-sans text-gray-600">
                                    <span>Discount</span>
                                    <input
                                        type="number"
                                        inputmode="decimal"
                                        step="any"
                                        min="0"
                                        max="{{ $this->subtotal }}"
                                        wire:model.live.debounce.300ms="globalDiscount"
                                        placeholder="0.00"
                                        class="w-20 h-6 text-xs font-mono font-bold text-right rounded border border-gray-300 px-1.5 focus:ring-1 focus:ring-brand focus:border-brand bg-white"
                                    />
                                </div>

                                @if($globalDiscount > 0)
                                    <div class="flex justify-between text-rose-600 font-sans">
                                        <span>Discount applied</span>
                                        <span class="font-bold font-mono">- ETB {{ number_format((float)$globalDiscount, 2) }}</span>
                                    </div>
                                @endif

                                <div class="pt-2 border-t border-gray-200 flex justify-between items-center text-sm font-sans">
                                    <span class="font-bold text-gray-900">Total Price</span>
                                    <span class="font-black text-brand text-lg font-mono">
                                        ETB {{ number_format($this->total, 2) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Payment Section --}}
                            <div class="space-y-3 pt-1">
                                {{-- Payment Method --}}
                                <div class="grid grid-cols-2 gap-1.5">
                                    <button
                                        type="button"
                                        wire:click="setPaymentMethod('cash')"
                                        class="h-9 rounded-xl flex items-center justify-center gap-1.5 text-xs font-bold border transition-all {{ $paymentMethod === 'cash' ? 'bg-brand text-white border-brand' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}"
                                    >
                                        <x-heroicon-o-banknotes class="w-3.5 h-3.5" />
                                        <span>Cash</span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="setPaymentMethod('transfer')"
                                        class="h-9 rounded-xl flex items-center justify-center gap-1.5 text-xs font-bold border transition-all {{ $paymentMethod === 'transfer' ? 'bg-brand text-white border-brand' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}"
                                    >
                                        <x-heroicon-o-credit-card class="w-3.5 h-3.5" />
                                        <span>Transfer</span>
                                    </button>
                                </div>

                                {{-- Cash Received & Quick Chips (If Cash) --}}
                                @if($paymentMethod === 'cash')
                                    <div class="space-y-2">
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-mono text-xs font-bold text-gray-400">ETB</span>
                                            <input
                                                type="number"
                                                inputmode="decimal"
                                                step="any"
                                                wire:model.live="cashReceived"
                                                placeholder="Amount received"
                                                class="w-full h-10 pl-12 pr-3 font-mono text-sm font-bold text-gray-900 bg-gray-50/60 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                                            />
                                        </div>

                                        {{-- Quick Amount Chips --}}
                                        <div class="grid grid-cols-4 gap-1 text-[11px] font-mono font-bold">
                                            <button type="button" wire:click="setExactAmount" class="h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">Exact</button>
                                            <button type="button" wire:click="addCash(10)" class="h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">+10</button>
                                            <button type="button" wire:click="addCash(50)" class="h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">+50</button>
                                            <button type="button" wire:click="addCash(100)" class="h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">+100</button>
                                        </div>

                                        {{-- Change vs Due Feedback --}}
                                        @if($this->difference > 0)
                                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-2.5 flex items-center justify-between text-emerald-800 text-xs">
                                                <span class="font-bold">Change to Return</span>
                                                <span class="font-mono font-black text-sm">ETB {{ number_format($this->change, 2) }}</span>
                                            </div>
                                        @elseif($this->difference == 0)
                                            <div class="bg-emerald-50/60 border border-emerald-200 rounded-xl p-2 text-emerald-800 text-[11px] font-bold text-center">
                                                Exact Payment (ETB 0.00 change)
                                            </div>
                                        @else
                                            <div class="bg-rose-50 border border-rose-200 rounded-xl p-2.5 flex items-center justify-between text-rose-800 text-xs">
                                                <span class="font-bold">Remaining Due</span>
                                                <span class="font-mono font-black text-rose-700">ETB {{ number_format($this->remainingBalance, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Confirm Button --}}
                                <button
                                    type="button"
                                    wire:click="confirmSale"
                                    wire:loading.attr="disabled"
                                    class="w-full h-12 inline-flex items-center justify-center gap-2 rounded-xl bg-brand hover:bg-brand-700 disabled:opacity-50 text-white font-bold text-sm shadow-sm transition-all active:scale-98"
                                >
                                    <span wire:loading.remove wire:target="confirmSale">
                                        Confirm Sale · ETB {{ number_format($this->total, 2) }}
                                    </span>
                                    <span wire:loading wire:target="confirmSale" class="inline-flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>Recording...</span>
                                    </span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- =========================================================================
                 MOBILE BOTTOM FLOATING CART BAR (Visible only on mobile/tablet when cart has items)
                 ========================================================================= --}}
            @if(!empty($cart) && $step === 'select')
                <div class="fixed bottom-floating-bar inset-x-0 z-30 p-3 lg:hidden pointer-events-none animate-slide-up">
                    <div class="max-w-lg mx-auto bg-gray-900/95 backdrop-blur-md text-white rounded-2xl shadow-xl p-3 flex items-center justify-between pointer-events-auto border border-white/10">
                        <div class="flex items-center gap-3 pl-1">
                            <div class="w-10 h-10 rounded-xl bg-brand text-white flex items-center justify-center font-bold text-sm">
                                {{ $this->totalUnits }}
                            </div>
                            <div>
                                <span class="text-[11px] text-gray-400 block leading-tight">Total Sale</span>
                                <span class="font-mono font-bold text-base text-white">
                                    ETB {{ number_format($this->total, 2) }}
                                </span>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="goToReview"
                            class="h-11 px-5 rounded-xl bg-brand hover:bg-brand-700 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition-all active:scale-95"
                        >
                            <span>Review Sale</span>
                            <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            @endif
        @endif

    </div>
</div>
