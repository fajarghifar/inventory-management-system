<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Details') }} #{{ $purchase->id }}
            </h2>
            <div class="flex items-center gap-2">
                <x-button variant="secondary" href="{{ route('purchases.index') }}">
                    &larr; Back to List
                </x-button>
                @if(in_array($purchase->status, [\App\Enums\PurchaseStatus::DRAFT, \App\Enums\PurchaseStatus::ORDERED]))
                    <x-button variant="secondary" href="{{ route('purchases.edit', $purchase) }}">
                        Edit
                    </x-button>
                @endif
            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-full mx-auto space-y-6">
            <!-- Main Info Card -->
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <!-- Header Info -->
                    <div class="flex items-start justify-between border-b border-gray-100 pb-4 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Purchase Information</h3>
                            <p class="text-sm text-gray-500">Details of the purchase transaction</p>
                        </div>
                        <div class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200">
                            ID: #{{ $purchase->id }}
                        </div>
                    </div>

                    <!-- Content Grid -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Supplier -->
                        <x-detail-item label="Supplier" :value="$purchase->supplier->name">
                            <x-heroicon-o-building-storefront class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Invoice -->
                        <x-detail-item label="Invoice Number" :value="$purchase->invoice_number ?? '-'">
                            <x-heroicon-o-document-text class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Purchase Date -->
                        <x-detail-item label="Purchase Date" :value="$purchase->purchase_date->format('d M Y')">
                            <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Due Date -->
                        <x-detail-item label="Due Date" :value="$purchase->due_date ? $purchase->due_date->format('d M Y') : '-'">
                            <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Status -->
                        <div>
                            <label class="text-sm font-medium leading-none text-gray-500">Status</label>
                            <div class="mt-1">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $purchase->status->color() }}">
                                    {{ $purchase->status->label() }}
                                </span>
                            </div>
                        </div>

                        <!-- Total Amount -->
                        <x-detail-item label="Total Amount" :value="'Rp ' . number_format($purchase->total, 0, ',', '.')">
                            <x-heroicon-o-banknotes class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Created By -->
                        <x-detail-item label="Created By" :value="$purchase->creator->name ?? 'Unknown'">
                            <x-heroicon-o-user class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Proof Image -->
                        @if($purchase->proof_image)
                            <div>
                                <label class="text-sm font-medium leading-none text-gray-500">Proof of Receipt</label>
                                <div class="mt-1">
                                    <a href="{{ Storage::url($purchase->proof_image) }}" target="_blank" class="text-indigo-600 hover:underline text-sm flex items-center gap-1">
                                        <x-heroicon-o-paper-clip class="w-4 h-4" />
                                        View Image
                                    </a>
                                </div>
                            </div>
                        @else
                            <x-detail-item label="Proof of Receipt" value="-" />
                        @endif
                    </div>

                    <!-- Notes -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-gray-500">
                                Notes
                            </label>
                            <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                <p class="text-sm text-slate-700 italic leading-relaxed">{{ $purchase->notes ?: 'No additional notes.' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Section -->
                    <div class="mt-6 border-t overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Code</th>
                                    <th class="px-6 py-3">Product</th>
                                    <th class="px-6 py-3">Unit</th>
                                    <th class="px-6 py-3 text-center">Quantity</th>
                                    <th class="px-6 py-3 text-right">Buying Price</th>
                                    <th class="px-6 py-3 text-right">Selling Price</th>
                                    <th class="px-6 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($purchase->items as $item)
                                    <tr class="bg-white hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $item->product->product_code ?? $item->product->sku ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ $item->product->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $item->product->unit->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ number_format($item->quantity) }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-right">Total</td>
                                    <td class="px-6 py-4 text-right text-indigo-600 text-lg">
                                        Rp {{ number_format($purchase->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Workflow -->
            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-4">

                @if($purchase->status === \App\Enums\PurchaseStatus::DRAFT)

                    {{-- Delete Action --}}
                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this draft?');">
                        @csrf
                        @method('DELETE')
                        <x-button variant="danger" type="submit">
                            Delete Draft
                        </x-button>
                    </form>

                    {{-- Order Action --}}
                    <form action="{{ route('purchases.mark-ordered', $purchase) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this purchase as ordered?');">
                        @csrf
                        @method('PATCH')
                        <x-button variant="info">
                            Mark as Ordered &rarr;
                        </x-button>
                    </form>

                @elseif($purchase->status === \App\Enums\PurchaseStatus::ORDERED)

                    {{-- Cancel Action --}}
                    <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                        @csrf
                        @method('PATCH')
                        <x-button variant="secondary" class="border-red-500 text-red-500 hover:bg-red-50">
                            Cancel Order
                        </x-button>
                    </form>

                    {{-- Receive Action Trigger (Modal) --}}
                    <div x-data="{ open: false }">
                        <x-button @click="open = true" variant="success">
                            <x-heroicon-o-check-circle class="w-5 h-5 mr-1" />
                            Receive Items
                        </x-button>

                        <!-- Modal Backdrop -->
                        <div x-show="open"
                             style="display: none;"
                             x-transition.opacity
                             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4">

                            <!-- Modal Content -->
                            <div @click.outside="open = false"
                                 x-transition.scale
                                 class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl">

                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    Receive Purchase #{{ $purchase->invoice_number ?? $purchase->id }}
                                </h3>

                                <form action="{{ route('purchases.mark-received', $purchase) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')

                                    <div class="space-y-4">
                                        @if($purchase->invoice_number && $purchase->proof_image)
                                            <div class="bg-gray-50 p-4 rounded-md border border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                                                <div class="mb-4">
                                                    <span class="block text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Invoice Number</span>
                                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $purchase->invoice_number }}</span>
                                                </div>
                                                <div>
                                                    <span class="block text-xs font-medium text-gray-500 uppercase mb-1 dark:text-gray-400">Proof of Receipt</span>
                                                    <a href="{{ Storage::url($purchase->proof_image) }}" target="_blank" class="text-indigo-600 hover:underline text-sm flex items-center gap-1 dark:text-indigo-400">
                                                        <x-heroicon-o-paper-clip class="w-4 h-4" />
                                                        View Uploaded Image
                                                    </a>
                                                </div>
                                                <p class="text-xs text-green-600 mt-3 font-medium flex items-center dark:text-green-400">
                                                    <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                                                    Data complete. Ready to receive.
                                                </p>
                                            </div>
                                        @else
                                            <!-- Invoice Input -->
                                            <x-form-input
                                                name="invoice_number"
                                                label="Final Invoice Number"
                                                :value="$purchase->invoice_number"
                                                required
                                                placeholder="INV-..."
                                            />

                                            <!-- Proof Image -->
                                            <x-form-input
                                                type="file"
                                                name="proof_image"
                                                label="Upload Proof of Receipt"
                                                required
                                                accept="image/*"
                                                class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                            />
                                            <p class="text-xs text-gray-500 mt-1">Image (JPG, PNG) max 2MB.</p>
                                        @endif
                                    </div>

                                    <div class="mt-6 flex justify-end gap-3">
                                        <x-button type="button" variant="secondary" @click="open = false">
                                            Cancel
                                        </x-button>
                                        <x-button class="bg-green-600 hover:bg-green-700">
                                            Confirm Receipt
                                        </x-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @elseif($purchase->status === \App\Enums\PurchaseStatus::RECEIVED)

                    {{-- Pay Action --}}
                    <form action="{{ route('purchases.mark-paid', $purchase) }}" method="POST" onsubmit="return confirm('Confirm payment for this purchase?');">
                        @csrf
                        @method('PATCH')
                        <x-button variant="success">
                            <x-heroicon-o-currency-dollar class="w-5 h-5 mr-1" />
                            Mark as Paid
                        </x-button>
                    </form>

                @elseif($purchase->status === \App\Enums\PurchaseStatus::CANCELLED)

                     {{-- Restore Action --}}
                     <form action="{{ route('purchases.restore-draft', $purchase) }}" method="POST" onsubmit="return confirm('Restore this purchase to Draft?');">
                        @csrf
                        @method('PATCH')
                        <x-button variant="secondary">
                            Restore to Draft
                        </x-button>
                    </form>

                @endif

            </div>
        </div>
    </div>
</x-app-layout>
