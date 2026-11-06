<x-modal name="quick-sale-modal" :title="''" maxWidth="lg">
    <div class="p-6">
        @if($product)
            @if($isSuccess)
                <!-- Success Confirmation State -->
                <div class="text-center py-4 space-y-4">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Sale Completed!</h3>
                        <p class="text-sm text-gray-500 mt-1">Invoice <span class="font-mono font-bold text-gray-800">#{{ $completedInvoice }}</span></p>
                    </div>

                    @if($paymentMethod === 'cash' && $completedChange > 0)
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg max-w-xs mx-auto">
                            <span class="text-xs uppercase font-semibold text-emerald-700 tracking-wider">Change Due</span>
                            <div class="text-2xl font-bold text-emerald-800">@money($completedChange)</div>
                        </div>
                    @endif

                    <div class="pt-4 flex flex-col sm:flex-row gap-2 justify-center border-t border-gray-100">
                        <a href="{{ route('sales.receipt', $completedSaleId) }}" target="_blank"
                           class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print Thermal Receipt
                        </a>
                        <a href="{{ route('sales.print', $completedSaleId) }}" target="_blank"
                           class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm font-semibold transition-colors gap-1.5">
                            Full Invoice
                        </a>
                        <button type="button" wire:click="close"
                                class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-semibold transition-colors">
                            Done
                        </button>
                    </div>
                </div>
            @else
                <!-- Active Quick Sale Form -->
                <div>
                    <!-- Header: Product Info & Stock -->
                    <div class="flex items-start justify-between border-b border-gray-100 pb-3 mb-4">
                        <div>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                ⚡ Express Direct Sale
                            </span>
                            <h3 class="text-base font-bold text-gray-900 mt-1 leading-snug">{{ $product->name }}</h3>
                            <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                                <span class="font-mono">{{ $product->sku }}</span>
                                <span>&bull;</span>
                                <span>{{ $product->category->name ?? 'General' }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">Unit Price</div>
                            <div class="text-base font-bold text-indigo-600">@money($product->selling_price)</div>
                            <div class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $product->quantity }} in stock
                            </div>
                        </div>
                    </div>

                    <form wire:submit="submit" class="space-y-4">
                        <!-- Quantity Selector with Stepper & Quick Chips -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Quantity to Sell</label>
                                <span class="text-xs text-gray-400">Max: {{ $product->quantity }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white shadow-sm flex-1">
                                    <button type="button" wire:click="decrement"
                                            class="w-11 h-10 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 font-bold text-lg flex items-center justify-center transition-colors">
                                        -
                                    </button>
                                    <input type="number" wire:model.live="quantity" min="1" max="{{ $product->quantity }}"
                                           class="w-full text-center font-bold text-lg border-0 focus:ring-0 py-1.5 text-gray-900">
                                    <button type="button" wire:click="increment"
                                            class="w-11 h-10 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 font-bold text-lg flex items-center justify-center transition-colors">
                                        +
                                    </button>
                                </div>
                                <!-- Unit Label -->
                                <span class="text-xs font-medium text-gray-500 px-2">{{ $product->unit->symbol ?? 'pcs' }}</span>
                            </div>

                            <!-- Quick Quantity Chips -->
                            <div class="flex items-center gap-1.5 mt-2">
                                @foreach([1, 2, 5, 10] as $q)
                                    @if($q <= $product->quantity)
                                        <button type="button" wire:click="$set('quantity', {{ $q }})"
                                                class="px-2.5 py-1 text-xs font-semibold rounded-md border transition-colors {{ $quantity === $q ? 'bg-indigo-50 border-indigo-300 text-indigo-700 font-bold' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100' }}">
                                            {{ $q }}
                                        </button>
                                    @endif
                                @endforeach
                                <button type="button" wire:click="$set('quantity', {{ $product->quantity }})"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-md border transition-colors {{ $quantity === $product->quantity ? 'bg-indigo-50 border-indigo-300 text-indigo-700 font-bold' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100' }}">
                                    All ({{ $product->quantity }})
                                </button>
                            </div>
                        </div>

                        <!-- Price & Real-Time Profit Card -->
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-1.5">
                            <div class="flex justify-between items-center text-xs text-gray-600">
                                <span>Subtotal ({{ $quantity }} &times; @money($product->selling_price)):</span>
                                <span class="font-medium">@money($this->subtotal)</span>
                            </div>

                            @if($discount > 0)
                                <div class="flex justify-between items-center text-xs text-amber-600">
                                    <span>Discount:</span>
                                    <span>- @money($discount)</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center text-base font-bold text-gray-900 pt-1 border-t border-gray-200">
                                <span>Total Payable:</span>
                                <span class="text-indigo-600">@money($this->total)</span>
                            </div>

                            <!-- Owner Confidential Real-Time Profit Calculation -->
                            @can('admin')
                                <div class="pt-2 border-t border-gray-200/80 flex items-center justify-between text-xs">
                                    <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        Estimated Profit:
                                    </span>
                                    <span class="font-bold text-emerald-800 bg-emerald-100/70 px-2 py-0.5 rounded">
                                        + @money($this->profit) ({{ $this->marginPercent }}% margin)
                                    </span>
                                </div>
                            @endcan
                        </div>

                        <!-- Payment Method Selector -->
                        <div>
                            <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide block mb-1.5">Payment Method</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach(['cash' => 'Cash', 'card' => 'Card', 'transfer' => 'Transfer', 'qris' => 'QRIS'] as $key => $label)
                                    <button type="button" wire:click="$set('paymentMethod', '{{ $key }}')"
                                            class="py-2 text-xs font-semibold rounded-lg border text-center transition-all {{ $paymentMethod === $key ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Cash Tender & Change (Only for Cash) -->
                        @if($paymentMethod === 'cash')
                            <div class="space-y-2 pt-1">
                                <div class="flex justify-between items-center">
                                    <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Cash Tendered</label>
                                    <button type="button" wire:click="setExactCash" class="text-xs text-indigo-600 font-bold hover:underline">
                                        Exact Amount
                                    </button>
                                </div>
                                <input type="number" wire:model.live="cashReceived"
                                       class="w-full h-10 rounded-lg border-gray-300 text-sm font-bold text-gray-900 focus:ring-indigo-500 focus:border-indigo-500">

                                <!-- Quick Tender Chips -->
                                <div class="grid grid-cols-4 gap-1.5">
                                    <button type="button" wire:click="setExactCash" class="py-1 text-[11px] font-bold bg-gray-100 hover:bg-gray-200 rounded border border-gray-200 text-gray-700">
                                        EXACT
                                    </button>
                                    <button type="button" wire:click="addCash(10000)" class="py-1 text-[11px] font-bold bg-gray-100 hover:bg-gray-200 rounded border border-gray-200 text-gray-700">
                                        +10K
                                    </button>
                                    <button type="button" wire:click="addCash(50000)" class="py-1 text-[11px] font-bold bg-gray-100 hover:bg-gray-200 rounded border border-gray-200 text-gray-700">
                                        +50K
                                    </button>
                                    <button type="button" wire:click="addCash(100000)" class="py-1 text-[11px] font-bold bg-gray-100 hover:bg-gray-200 rounded border border-gray-200 text-gray-700">
                                        +100K
                                    </button>
                                </div>

                                <!-- Live Change Display -->
                                <div class="p-2.5 rounded-lg border flex items-center justify-between {{ $this->change >= 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' }}">
                                    <span class="text-xs font-bold uppercase">{{ $this->change >= 0 ? 'Change Due' : 'Shortage' }}</span>
                                    <span class="text-lg font-bold">@money(abs($this->change))</span>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2.5">
                            <button type="button" wire:click="close"
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" wire:loading.attr="disabled"
                                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2 disabled:opacity-50">
                                <span wire:loading.remove wire:target="submit">⚡ Confirm Sale</span>
                                <span wire:loading wire:target="submit">Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @else
            <div class="text-center py-6 text-gray-500">
                Loading product...
            </div>
        @endif
    </div>
</x-modal>

