<div class="flex flex-col lg:flex-row h-[calc(100vh-80px)] space-y-4 lg:space-y-0 lg:space-x-4 p-2 relative">
    <!-- Left Side: Transaction Details (70%) -->
    <div class="w-full lg:w-[70%] flex flex-col space-y-4 h-full">
        <!-- Search Bar -->
        <div class="relative z-20" x-data="{ open: false }" @click.outside="open = false">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg wire:loading.remove wire:target="search" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
                <svg wire:loading wire:target="search" class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                @focus="open = true"
                type="text"
                placeholder="Search Products (Name or SKU)..."
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-base shadow-sm"
                autofocus
            >

            <!-- Search Results Dropdown -->
            @if(!empty($search) && count($this->products) > 0)
            <div x-show="open"
                 x-transition
                 class="absolute z-50 w-full mt-1 bg-white shadow-xl max-h-96 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                @foreach($this->products as $product)
                <div
                    wire:click="addToCart({{ $product->id }})"
                    @click="open = false"
                    class="cursor-pointer select-none relative py-3 pl-3 pr-9 hover:bg-indigo-50 border-b border-gray-100 last:border-0"
                >
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-gray-900">{{ $product->name }}</span>
                            <span class="text-xs text-gray-500 ml-2">{{ $product->sku }}</span>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-indigo-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                            <div class="text-xs {{ $product->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                Stok: {{ $product->quantity }} {{ $product->unit->symbol ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Cart Table -->
        <div class="flex-1 bg-white rounded-lg shadow border border-gray-200 overflow-hidden flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($cart as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $item['sku'] ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input
                                    type="number"
                                    value="{{ $item['quantity'] }}"
                                    wire:change="updateQuantity({{ $item['id'] }}, $event.target.value)"
                                    class="w-16 text-center border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm py-1"
                                    min="1"
                                >
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $item['unit'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="relative rounded-md shadow-sm w-32 ml-auto">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-xs">Rp</span>
                                    </div>
                                    <input
                                        type="text"
                                        value="{{ number_format($item['discount'], 0, ',', '.') }}"
                                        onchange="let val = this.value.replace(/\./g, '').replace(/[^0-9]/g, ''); @this.updateDiscount({{ $item['id'] }}, val)"
                                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-8 pr-2 sm:text-sm border-gray-300 rounded-md text-right"
                                        placeholder="0"
                                    >
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                Rp {{ number_format(($item['price'] - $item['discount']) * $item['quantity'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button wire:click="removeFromCart({{ $item['id'] }})" class="text-red-500 hover:text-red-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <p class="text-base font-medium">Keranjang masih kosong</p>
                                    <p class="text-sm text-gray-400">Cari produk di atas untuk memulai transaksi</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Side: Payment Details (30%) -->
    <div class="w-full lg:w-[30%] flex flex-col bg-white rounded-lg shadow border border-gray-200 h-full">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Rincian Pembayaran</h2>
        </div>

        <div class="p-4 space-y-6 flex-1 overflow-y-auto">
            <!-- Customer Section -->
            <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100 relative group">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-xs font-bold text-indigo-500 uppercase">Pelanggan</span>
                    <button wire:click="openCustomerModal" class="text-[10px] font-semibold text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-200 bg-white px-2 py-1 rounded transition-colors flex items-center">
                        + Baru (F3)
                    </button>
                </div>

                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <!-- Only show if explicitly requested, but for now we just show selected or search -->
                    @if($customer_id)
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">{{ $customerSearch }}</h3>
                                <!-- Ideally get phone from a computed prop or separate lookup, but simplified for now -->
                                <p class="text-sm text-gray-600">Pelanggan Terdaftar</p>
                            </div>
                            <button wire:click="resetCustomerSearch" class="text-gray-400 hover:text-red-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @else
                        <!-- Customer Search Input -->
                        <div class="relative">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="customerSearch"
                                @focus="open = true"
                                placeholder="Cari Pelanggan..."
                                class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                            >
                            <!-- Customer Dropdown Results -->
                            @if(!empty($customerSearch) && !$customer_id)
                            <div x-show="open" class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-40 rounded-md py-1 text-sm overflow-auto">
                                @forelse($this->filteredCustomers as $cust)
                                    <div wire:click="selectCustomer({{ $cust->id }}, '{{ addslashes($cust->name) }}')" class="px-3 py-2 hover:bg-indigo-50 cursor-pointer">
                                        <div class="font-medium">{{ $cust->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $cust->phone }}</div>
                                    </div>
                                @empty
                                    <div class="px-3 py-2 text-gray-500 italic text-xs">Tidak ditemukan</div>
                                @endforelse
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Totals Section -->
            <div class="space-y-3">
                <div class="flex justify-between text-gray-600 text-sm">
                    <span>Subtotal</span>
                    <span class="font-medium">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                </div>
                <!-- Optional Discount Total if we had it computed -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                    <span class="text-lg font-bold text-gray-800">TOTAL</span>
                    <span class="text-2xl font-extrabold text-blue-600">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment Input -->
            <div class="space-y-4 pt-4 border-t border-gray-200">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            wire:click="$set('payment_method', 'cash')"
                            class="px-4 py-2 text-sm font-medium rounded-md border {{ $payment_method === 'cash' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                        >
                            TUNAI
                        </button>
                        <button
                            wire:click="$set('payment_method', 'credit')"
                            class="px-4 py-2 text-sm font-medium rounded-md border {{ $payment_method === 'credit' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                        >
                            TRANSFER
                        </button>
                    </div>
                </div>

                @if($payment_method === 'cash')
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Uang Diterima</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold">Rp</span>
                        </div>
                        <input
                            type="number"
                            wire:model.live.debounce.500ms="cash_received"
                            class="block w-full pl-10 pr-3 py-3 text-lg font-bold text-gray-900 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="0"
                        >
                    </div>
                </div>

                <div class="bg-green-50 p-3 rounded-md border border-green-100 flex justify-between items-center">
                    <span class="text-sm font-medium text-green-800 uppercase">Kembalian</span>
                    <span class="text-xl font-bold text-green-700">Rp {{ number_format($this->change, 0, ',', '.') }}</span>
                </div>
                @endif

                <div>
                    <input
                        type="text"
                        wire:model="notes"
                        class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400"
                        placeholder="Catatan Transaksi (Opsional)..."
                    >
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 space-y-3">
            <button
                wire:click="submit"
                wire:loading.attr="disabled"
                class="w-full flex justify-center py-4 px-4 border border-transparent rounded-md shadow-sm text-base font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                <div wire:loading wire:target="submit" class="mr-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
                BAYAR (F2)
            </button>
            <button class="w-full py-2 text-sm font-medium text-red-600 hover:text-red-800 bg-transparent border border-transparent hover:border-red-200 rounded flex items-center justify-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                BATAL
            </button>
        </div>
    </div>

    <!-- Create Customer Modal (Reused) -->
    <x-modal name="customer-modal" title="Tambah Pelanggan Baru" focusable>
        <div class="p-6 space-y-4">
            <x-form-input
                name="newCustomer.name"
                label="Nama Lengkap"
                wire:model="newCustomer.name"
                placeholder="Contoh: Budi Santoso"
                required
            />

            <div class="grid grid-cols-2 gap-4">
                <x-form-input
                    name="newCustomer.email"
                    label="Email"
                    type="email"
                    wire:model="newCustomer.email"
                    placeholder="email@contoh.com"
                />

                <x-form-input
                    name="newCustomer.phone"
                    label="No. HP"
                    wire:model="newCustomer.phone"
                    placeholder="0812..."
                />
            </div>

            <div class="space-y-2">
                <x-input-label for="newCustomer.address" value="Alamat" />
                <textarea
                    wire:model="newCustomer.address"
                    id="newCustomer.address"
                    rows="2"
                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm"
                    placeholder="Alamat lengkap..."
                ></textarea>
                <x-input-error :messages="$errors->get('newCustomer.address')" class="mt-2" />
            </div>

            <div class="space-y-2">
                <x-input-label for="newCustomer.notes" value="Catatan" />
                <textarea
                    wire:model="newCustomer.notes"
                    id="newCustomer.notes"
                    rows="2"
                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm"
                    placeholder="Catatan tambahan..."
                ></textarea>
                <x-input-error :messages="$errors->get('newCustomer.notes')" class="mt-2" />
            </div>
        </div>

        <div class="flex justify-end px-6 py-4 bg-gray-50 text-right space-x-2">
            <x-secondary-button x-on:click="$dispatch('close-modal', { name: 'customer-modal' })">
                Batal
            </x-secondary-button>
            <x-primary-button wire:click="saveNewCustomer">
                Simpan
            </x-primary-button>
        </div>
    </x-modal>
</div>
