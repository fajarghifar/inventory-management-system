<div class="h-[calc(100vh-4.5rem)] bg-zinc-50 dark:bg-zinc-950 flex overflow-hidden font-sans">

    <!-- LEFT PANEL: Search & Cart (Flexible Width) -->
    <div class="flex-1 flex flex-col min-w-0 border-r border-zinc-200 dark:border-zinc-800">

        <!-- HEADER: Search Bar -->
        <div class="p-3 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 shrink-0 z-20">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="h-4 w-4 text-zinc-400" />
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    class="block w-full pl-9 pr-10 h-10 rounded-md border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 text-sm focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 focus:border-transparent transition-all placeholder:text-zinc-400"
                    placeholder="Search Product (Scan Barcode / Name / SKU)... [F1]"
                    autofocus
                    autocomplete="off"
                />

                <!-- Loading -->
                <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                    <svg class="animate-spin h-5 w-5 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <!-- Clear -->
                @if(strlen($search) > 0)
                    <button wire:click="$set('search', '')" wire:loading.remove wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 transition-colors">
                        <x-heroicon-s-x-circle class="h-5 w-5" />
                    </button>
                @endif

                <!-- Dropdown Results -->
                @if(strlen($search) > 0)
                    <div class="absolute w-full mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md shadow-lg max-h-[70vh] overflow-y-auto z-50 divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($this->products as $product)
                            <div wire:click="addToCart({{ $product->id }})" class="p-3 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer flex justify-between items-center group">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ $product->name }}</div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-mono bg-zinc-100 dark:bg-zinc-800 px-1.5 rounded text-zinc-500">{{ $product->sku }}</span>
                                        <span class="text-[10px] {{ $product->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">Stock: {{ $product->quantity }}</span>
                                    </div>
                                </div>
                                <div class="font-bold text-sm tabular-nums">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-sm text-zinc-500">Product not found</div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>

        <!-- CART CONTENT -->
        <div class="flex-1 overflow-y-auto relative bg-white dark:bg-zinc-950">
            <table class="w-full text-sm text-left">
                <thead class="text-[10px] text-zinc-500 font-bold uppercase bg-zinc-50/90 dark:bg-zinc-900/90 sticky top-0 backdrop-blur-md z-10 border-b border-zinc-200 dark:border-zinc-800 tracking-wider">
                    <tr>
                        <th class="px-4 py-3 w-[35%]">Product</th>
                        <th class="px-2 py-3 text-right w-[15%]">Price</th>
                        <th class="px-2 py-3 text-center w-[20%]">Qty</th>
                        <th class="px-2 py-3 text-right w-[15%]">Disc.</th>
                        <th class="px-4 py-3 text-right w-[15%]">Total</th>
                        <th class="px-2 py-3 w-8"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($cart as $id => $item)
                        <tr class="group odd:bg-white even:bg-zinc-50 dark:odd:bg-zinc-900 dark:even:bg-zinc-800/30 hover:bg-indigo-50/60 dark:hover:bg-indigo-900/20 transition-all duration-200">
                            <!-- Product -->
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate text-sm" title="{{ $item['name'] }}">{{ $item['name'] }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] font-mono text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-1.5 rounded border border-zinc-200 dark:border-zinc-700">{{ $item['sku'] }}</span>
                                            @if($item['quantity'] >= ($item['max_stock'] ?? 0))
                                                <span class="text-[9px] text-rose-600 bg-rose-50 dark:bg-rose-900/20 px-1 rounded font-bold border border-rose-100 dark:border-rose-900">MAX</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="px-2 py-3 align-middle text-right font-medium text-zinc-600 dark:text-zinc-400 tabular-nums">
                                {{ number_format($item['price'], 0, ',', '.') }}
                            </td>

                            <!-- Qty -->
                            <td class="px-2 py-3 align-middle">
                                <div class="flex items-center justify-center bg-white dark:bg-zinc-900 rounded-md shadow-sm ring-1 ring-zinc-200 dark:ring-zinc-700 h-8 w-28 mx-auto overflow-hidden group-hover:ring-indigo-200 transition-shadow">
                                    <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})" class="w-9 h-full flex items-center justify-center text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-zinc-800 transition active:scale-90">
                                        <x-heroicon-s-minus-small class="w-4 h-4" />
                                    </button>
                                    <input type="number"
                                        value="{{ $item['quantity'] }}"
                                        wire:change="updateQuantity({{ $id }}, $event.target.value)"
                                        class="flex-1 w-full h-full text-center border-0 p-0 text-sm font-bold text-zinc-900 dark:text-white focus:ring-0 spin-hide bg-transparent selection:bg-indigo-100"
                                    >
                                    <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})" class="w-9 h-full flex items-center justify-center text-zinc-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-zinc-800 transition active:scale-90">
                                        <x-heroicon-s-plus-small class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>

                            <td class="px-2 py-3 align-middle text-right">
                                <div class="relative w-24 ml-auto group/input">
                                    <input
                                        type="number"
                                        value="{{ $item['discount'] ?? 0 }}"
                                        wire:change="updateDiscount({{ $id }}, $event.target.value)"
                                        class="block w-full text-right h-8 py-1 px-2 text-sm font-bold bg-transparent border-b border-dashed border-zinc-300 dark:border-zinc-700 focus:border-indigo-600 focus:ring-0 placeholder-zinc-300 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-t"
                                        placeholder="0"
                                    >
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="px-4 py-3 align-middle text-right font-bold text-zinc-900 dark:text-zinc-100 tabular-nums text-sm tracking-tight">
                                {{ number_format(($item['price'] * $item['quantity']) - ($item['discount'] ?? 0), 0, ',', '.') }}
                            </td>

                            <!-- Delete -->
                            <td class="px-2 py-3 align-middle text-center">
                                <button wire:click="removeItem({{ $id }})" class="text-zinc-300 hover:text-rose-500 p-1.5 rounded-full hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors transform hover:scale-110" title="Remove">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-20 text-center select-none">
                                <div class="flex flex-col items-center justify-center text-zinc-300 dark:text-zinc-700">
                                    <x-heroicon-o-shopping-cart class="w-16 h-16 mb-4 stroke-1" />
                                    <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Empty Cart</div>
                                    <p class="text-xs text-zinc-500 mt-1 max-w-[200px]">Scan a product or search to add items to the cart.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- CART FOOTER INFO -->
        <div class="bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 p-3 text-xs text-zinc-500 flex justify-between shrink-0">
            <span>{{ count($cart) }} Items in Cart</span>
            <span>Press <kbd class="font-mono bg-zinc-100 px-1 rounded border border-zinc-200">F2</kbd> to Pay</span>
        </div>
    </div>

    <!-- RIGHT PANEL: Sidebar (Fixed Width) -->
    <div class="w-[340px] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 flex flex-col shrink-0 z-10 shadow-xl">

        <!-- Customer Section -->
        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800">
            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2 block">Customer</label>
            <div class="flex gap-2">
                <div class="relative flex-1 group">
                    <input
                        wire:model.live.debounce.300ms="customerSearch"
                        type="text"
                        class="block w-full h-9 pl-8 text-xs rounded-md border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800 placeholder:text-zinc-400 focus:ring-1 focus:ring-zinc-900 focus:border-zinc-400 transition"
                        placeholder="Guest..."
                    />
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <x-heroicon-s-user class="h-3.5 w-3.5 text-zinc-400" />
                    </div>

                    <!-- Loading Icon -->
                    <div wire:loading wire:target="customerSearch" class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">
                        <svg class="animate-spin h-3.5 w-3.5 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>

                    <!-- Clear Customer -->
                    @if($customerId)
                        <button wire:click="$set('customerId', null); $set('customerSearch', '')" class="absolute inset-y-0 right-0 pr-2 flex items-center text-zinc-400 hover:text-red-500">
                            <x-heroicon-s-x-mark class="w-3.5 h-3.5" />
                        </button>
                    @endif

                    <!-- Dropdown -->
                    @if(strlen($customerSearch) > 1 && !$customerId)
                        <div class="absolute w-full mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md shadow-lg z-50">
                            @forelse($this->customers as $c)
                                <div wire:click="selectCustomer({{ $c->id }})" class="p-2 hover:bg-zinc-50 cursor-pointer text-xs">
                                    <div class="font-bold">{{ $c->name }}</div>
                                    <div class="text-zinc-500">{{ $c->phone }}</div>
                                </div>
                            @empty
                                <div class="p-2 text-xs text-center text-zinc-400">No results</div>
                            @endforelse
                        </div>
                    @endif
                </div>
                <button wire:click="$set('isCreatingCustomer', true)" class="h-9 w-9 flex items-center justify-center rounded-md border dashed border-zinc-300 hover:bg-zinc-50 text-zinc-500">
                    <x-heroicon-o-plus class="w-4 h-4" />
                </button>
            </div>
            @if($customerId)
                @php $selectedCustomer = \App\Models\Customer::find($customerId); @endphp
                <div class="mt-2 text-xs bg-indigo-50 text-indigo-700 px-2 py-1.5 rounded border border-indigo-100 flex justify-between items-center">
                    <div class="flex flex-col overflow-hidden mr-2">
                        <span class="font-bold truncate">{{ $selectedCustomer->name }}</span>
                        @if($selectedCustomer->phone)
                            <span class="text-[10px] opacity-80 font-mono">{{ $selectedCustomer->phone }}</span>
                        @endif
                    </div>
                    <span class="text-[10px] opacity-75 border border-indigo-200 px-1 rounded bg-white/50">Customer</span>
                </div>
            @endif
        </div>

        <!-- Payment Section -->
        <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scroll">
            <!-- Payment Method -->
            <div>
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2 block">Payment</label>
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="$set('paymentMethod', 'cash')" class="h-9 text-xs font-bold border rounded-md flex items-center justify-center gap-2 {{ $paymentMethod === 'cash' ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-600 border-zinc-200 hover:bg-zinc-50' }}">
                        <x-heroicon-s-banknotes class="w-3.5 h-3.5" /> Cash
                    </button>
                    <button wire:click="$set('paymentMethod', 'transfer')" class="h-9 text-xs font-bold border rounded-md flex items-center justify-center gap-2 {{ $paymentMethod === 'transfer' ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-600 border-zinc-200 hover:bg-zinc-50' }}">
                        <x-heroicon-s-credit-card class="w-3.5 h-3.5" /> Transfer
                    </button>
                </div>
            </div>

            <!-- Notes -->
            <div>
                 <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2 block">Notes</label>
                 <textarea
                    wire:model.blur="notes"
                    rows="1"
                    class="block w-full rounded-md border-zinc-200 dark:border-zinc-800 text-xs focus:ring-zinc-900 dark:bg-zinc-800 dark:text-white resize-none py-1.5"
                    placeholder="Transaction notes..."
                ></textarea>
            </div>

            <!-- Totals -->
            <div class="space-y-2 py-3">
                <div class="space-y-1 px-1">
                    <div class="flex justify-between text-xs font-medium text-zinc-500">
                        <span>Subtotal</span>
                        <span class="text-zinc-700 dark:text-zinc-300 tabular-nums">Rp {{ number_format($this->grossSubtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($this->totalDiscount > 0)
                        <div class="flex justify-between text-xs font-medium text-rose-500">
                            <span>Discount</span>
                            <span class="tabular-nums">- Rp {{ number_format($this->totalDiscount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex justify-between items-end pt-3 border-t border-dashed border-zinc-200 dark:border-zinc-800">
                    <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-1">Total Bill</span>
                    <div class="text-right">
                        <span class="block text-xl font-black text-zinc-900 dark:text-white leading-none tracking-tight">
                            <span class="text-xs font-bold text-zinc-400 mr-1">Rp</span>{{ number_format($this->total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Cash Input -->
            @if($paymentMethod === 'cash')
                <div>
                     <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2 block">Cash Received</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-zinc-400 text-xs font-bold">Rp</span>
                        </div>
                        <input type="number" wire:model.live.debounce.300ms="cashReceived" class="w-full pl-9 pr-3 h-9 rounded-md border-zinc-200 bg-zinc-50 text-sm font-bold text-zinc-900 focus:ring-2 focus:ring-zinc-900 focus:border-zinc-900 tabular-nums">
                    </div>

                    <!-- Quick Amounts -->
                    <div class="grid grid-cols-4 gap-1.5 mt-2">
                        <button wire:click="setCashExact" class="py-1 text-[10px] font-bold bg-zinc-100 hover:bg-zinc-200 rounded border border-zinc-200 text-zinc-600">Exact</button>
                        <button wire:click="addCash(10000)" class="py-1 text-[10px] font-bold bg-white hover:bg-zinc-50 rounded border border-zinc-200 text-zinc-600">+10k</button>
                        <button wire:click="addCash(20000)" class="py-1 text-[10px] font-bold bg-white hover:bg-zinc-50 rounded border border-zinc-200 text-zinc-600">+20k</button>
                        <button wire:click="addCash(50000)" class="py-1 text-[10px] font-bold bg-white hover:bg-zinc-50 rounded border border-zinc-200 text-zinc-600">+50k</button>
                    </div>

                    <!-- Change -->
                    @if($cashReceived > 0)
                        <div class="mt-4 p-3 rounded-md {{ $this->change < 0 ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }} flex justify-between items-center">
                            <span class="text-xs font-bold">{{ $this->change < 0 ? 'Short' : 'Change' }}</span>
                            <span class="font-mono font-bold text-lg">Rp {{ number_format(abs($this->change), 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Footer Action -->
        <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200 dark:border-zinc-800">
             <button
                wire:click="openPaymentConfirmation"
                class="w-full h-11 bg-zinc-900 hover:bg-zinc-800 text-white rounded-md font-bold text-sm shadow-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                @if(empty($cart) || ($paymentMethod === 'cash' && $cashReceived < $this->total)) disabled @endif
            >
                <x-heroicon-s-printer class="w-4 h-4" />
                <span>Pay Rp {{ number_format($this->total, 0, ',', '.') }}</span>
            </button>
        </div>
    </div>

    <!-- Modals -->
    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-[2px] p-4">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-sm rounded-lg shadow-2xl border border-zinc-200 dark:border-zinc-800 p-6 space-y-4">
                <div class="text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                        <x-heroicon-s-check class="h-6 w-6 text-green-600" />
                    </div>
                    <h3 class="mt-4 text-base font-semibold leading-6 text-zinc-900 dark:text-white">Payment Confirmation</h3>
                    <p class="mt-2 text-sm text-zinc-500">Are you sure you want to process this transaction?</p>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800 p-3 rounded-md space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-zinc-500">Total</span> <span class="font-bold">Rp {{ number_format($this->total, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-zinc-500">Cash</span> <span class="font-bold">Rp {{ number_format($cashReceived, 0, ',', '.') }}</span></div>
                    <div class="border-t border-dashed border-zinc-200 my-2 pt-2 flex justify-between"><span class="text-zinc-500">Change</span> <span class="font-bold text-emerald-600">Rp {{ number_format($this->change, 0, ',', '.') }}</span></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button wire:click="$set('showConfirmModal', false)" class="w-full h-9 rounded-md border border-zinc-300 text-sm font-medium hover:bg-zinc-50">Cancel</button>
                    <button wire:click="processPayment" class="w-full h-9 rounded-md bg-zinc-900 text-white text-sm font-bold hover:bg-zinc-800">Yes, Process</button>
                </div>
            </div>
        </div>
    @endif

    {{-- New Customer Modal --}}
    @if($isCreatingCustomer)
        <div class="fixed inset-0 z-[9000]">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-[9010] flex items-center justify-center p-4">
                <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white dark:bg-zinc-800 text-left shadow-2xl ring-1 ring-gray-200 dark:ring-zinc-700">
                    <div class="bg-white dark:bg-zinc-800 px-6 py-5 border-b border-gray-100 dark:border-zinc-700 flex justify-between items-center">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 p-2 rounded-xl">
                                <x-heroicon-s-user-plus class="w-4 h-4" />
                            </span>
                            New Customer
                        </h3>
                        <button wire:click="$set('isCreatingCustomer', false)" class="text-gray-400 hover:text-gray-600">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Full Name *</label>
                            <input type="text" wire:model="newCustomerName" class="block w-full rounded-md border-gray-200 dark:border-zinc-600 text-sm focus:ring-zinc-900 dark:bg-zinc-700 dark:text-white h-9">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Phone / WA</label>
                                <input type="text" wire:model="newCustomerPhone" class="block w-full rounded-md border-gray-200 dark:border-zinc-600 text-sm focus:ring-zinc-900 dark:bg-zinc-700 dark:text-white h-9">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Email</label>
                                <input type="email" wire:model="newCustomerEmail" class="block w-full rounded-md border-gray-200 dark:border-zinc-600 text-sm focus:ring-zinc-900 dark:bg-zinc-700 dark:text-white h-9">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Address</label>
                            <textarea wire:model="newCustomerAddress" rows="2" class="block w-full rounded-md border-gray-200 dark:border-zinc-600 text-sm focus:ring-zinc-900 dark:bg-zinc-700 dark:text-white resize-none"></textarea>
                        </div>
                        <div>
                             <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Notes</label>
                            <textarea wire:model="newCustomerNotes" rows="1" class="block w-full rounded-md border-gray-200 dark:border-zinc-600 text-sm focus:ring-zinc-900 dark:bg-zinc-700 dark:text-white resize-none"></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-zinc-800/50 px-6 py-4 border-t border-gray-100 dark:border-zinc-700 rounded-b-2xl flex gap-3">
                         <button wire:click="$set('isCreatingCustomer', false)" class="w-full h-9 bg-white border border-gray-200 rounded-md font-bold text-zinc-700 hover:bg-gray-50 text-sm">Cancel</button>
                        <button wire:click="saveNewCustomer" class="w-full h-9 bg-zinc-900 text-white rounded-md font-bold hover:bg-zinc-800 text-sm">Save Customer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .spin-hide::-webkit-inner-spin-button, .spin-hide::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        .spin-hide { -moz-appearance: textfield; }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background-color: #e4e4e7; border-radius: 4px; }
    </style>
</div>
