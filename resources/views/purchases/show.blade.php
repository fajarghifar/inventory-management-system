<x-app-layout title="Purchase Details">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Purchase Details') }} #{{ $purchase->invoice_number ?: $purchase->id }}
            </h2>
            <div class="flex items-center gap-2">
                <x-secondary-button href="{{ route('purchases.index') }}">
                    &larr; {{ __('Back to List') }}
                </x-secondary-button>
                @if(in_array($purchase->status, [\App\Enums\PurchaseStatus::DRAFT, \App\Enums\PurchaseStatus::ORDERED]))
                    <x-secondary-button href="{{ route('purchases.edit', $purchase) }}">
                        {{ __('Edit') }}
                    </x-secondary-button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Main Info Card -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden border border-gray-200">
                <div class="p-6">
                    <!-- Header Info -->
                    <div class="flex items-start justify-between border-b border-gray-100 pb-4 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Purchase Information') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('Details of the purchase transaction') }}</p>
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
                                            {{ $item->product->unit->symbol ?? $item->product->unit->name ?? '-' }}
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
            <div x-data="{
                actionUrl: '',
                actionMethod: '',
                modalTitle: '',
                modalMessage: '',
                confirmButtonText: '',
                confirmButtonClass: '',

                confirmAction(url, method, title, message, btnText, btnClass) {
                    this.actionUrl = url;
                    this.actionMethod = method;
                    this.modalTitle = title;
                    this.modalMessage = message;
                    this.confirmButtonText = btnText;
                    this.confirmButtonClass = btnClass;
                    $dispatch('open-modal', { name: 'confirmation-modal' });
                }
            }" class="flex flex-col sm:flex-row justify-end gap-4">

                @if($purchase->status === \App\Enums\PurchaseStatus::DRAFT)

                    {{-- Delete Action --}}
                    <x-danger-button
                        @click="confirmAction('{{ route('purchases.destroy', $purchase) }}', 'DELETE', 'Delete Draft', 'Are you sure you want to delete this draft? This action cannot be undone.', 'Delete Draft', '!bg-red-600 hover:!bg-red-700 focus:!ring-red-500')"
                    >
                        {{ __('Delete Draft') }}
                    </x-danger-button>

                    {{-- Order Action --}}
                    <x-primary-button
                        class="!bg-sky-600 hover:!bg-sky-700 focus:!ring-sky-500"
                        @click="confirmAction('{{ route('purchases.mark-ordered', $purchase) }}', 'PATCH', 'Mark as Ordered', 'Are you sure you want to mark this purchase as ordered? The stock will not be updated until items are received.', 'Mark as Ordered', '!bg-sky-600 hover:!bg-sky-700 focus:!ring-sky-500')"
                    >
                        {{ __('Mark as Ordered') }}
                    </x-primary-button>

                @elseif($purchase->status === \App\Enums\PurchaseStatus::ORDERED)

                    {{-- Cancel Action --}}
                    <x-secondary-button
                        class="text-red-600 hover:bg-red-50 border-red-200"
                        @click="confirmAction('{{ route('purchases.cancel', $purchase) }}', 'PATCH', 'Cancel Order', 'Are you sure you want to cancel this order?', 'Cancel Order', '!bg-red-600 hover:!bg-red-700 focus:!ring-red-500')"
                    >
                        {{ __('Cancel Order') }}
                    </x-secondary-button>

                    {{-- Receive Action Trigger (Modal) --}}
                    <div x-data="{ open: false }">
                        <x-primary-button @click="open = true" class="!bg-green-600 hover:!bg-green-700 focus:!ring-green-500">
                            <x-heroicon-o-check-circle class="w-5 h-5 mr-1" />
                            {{ __('Receive Items') }}
                        </x-primary-button>

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
                                            <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                                                <div class="mb-4">
                                                    <span class="block text-xs font-medium text-gray-500 uppercase">Invoice Number</span>
                                                    <span class="text-sm font-semibold text-gray-900">{{ $purchase->invoice_number }}</span>
                                                </div>
                                                <div>
                                                    <span class="block text-xs font-medium text-gray-500 uppercase mb-1">Proof of Receipt</span>
                                                    <a href="{{ Storage::url($purchase->proof_image) }}" target="_blank" class="text-indigo-600 hover:underline text-sm flex items-center gap-1">
                                                        <x-heroicon-o-paper-clip class="w-4 h-4" />
                                                        View Uploaded Image
                                                    </a>
                                                </div>
                                                <p class="text-xs text-green-600 mt-3 font-medium flex items-center">
                                                    <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                                                    Data complete. Ready to receive.
                                                </p>
                                            </div>
                                        @else
                                            <!-- Invoice Input -->
                                            <div class="space-y-2">
                                                <x-input-label for="invoice_number" :value="__('Final Invoice Number')" required />
                                                <x-text-input
                                                    id="invoice_number"
                                                    name="invoice_number"
                                                    :value="$purchase->invoice_number"
                                                    required
                                                    placeholder="INV-..."
                                                />
                                            </div>

                                            <!-- Proof Image -->
                                            <div class="space-y-2">
                                                <x-input-label for="proof_image" :value="__('Upload Proof of Receipt')" />
                                                <input
                                                    id="proof_image"
                                                    type="file"
                                                    name="proof_image"
                                                    accept="image/*"
                                                    required
                                                    class="block w-full text-sm text-gray-500
                                                        file:mr-4 file:py-2 file:px-4
                                                        file:rounded-md file:border-0
                                                        file:text-sm file:font-semibold
                                                        file:bg-indigo-50 file:text-indigo-700
                                                        hover:file:bg-indigo-100"
                                                />
                                                <p class="text-xs text-gray-500">Image (JPG, PNG) max 2MB.</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-6 flex justify-end gap-3">
                                        <x-secondary-button type="button" @click="open = false">
                                            Cancel
                                        </x-secondary-button>
                                        <x-primary-button class="!bg-green-600 hover:!bg-green-700 focus:!ring-green-500">
                                            Confirm Receipt
                                        </x-primary-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @elseif($purchase->status === \App\Enums\PurchaseStatus::RECEIVED)

                    {{-- Pay Action --}}
                    <x-primary-button
                        class="!bg-emerald-600 hover:!bg-emerald-700 focus:!ring-emerald-500"
                        @click="confirmAction('{{ route('purchases.mark-paid', $purchase) }}', 'PATCH', 'Mark as Paid', 'Are you sure you want to mark this purchase as paid? This assumes the full amount has been paid.', 'Mark as Paid', '!bg-emerald-600 hover:!bg-emerald-700 focus:!ring-emerald-500')"
                    >
                        <x-heroicon-o-currency-dollar class="w-5 h-5 mr-1" />
                        {{ __('Mark as Paid') }}
                    </x-primary-button>

                @elseif($purchase->status === \App\Enums\PurchaseStatus::CANCELLED)

                    {{-- Restore Action --}}
                    <x-secondary-button
                        @click="confirmAction('{{ route('purchases.restore-draft', $purchase) }}', 'PATCH', 'Restore to Draft', 'Restore this purchase to Draft status? You can edit it again.', 'Restore to Draft', '!bg-gray-800 hover:!bg-gray-700 text-white')"
                    >
                        {{ __('Restore to Draft') }}
                    </x-secondary-button>

                @endif

                <!-- Shared Confirmation Modal -->
                <x-modal name="confirmation-modal">
                    <div class="p-6" x-data="{ submitting: false }">
                        <h2 class="text-lg font-medium text-gray-900" x-text="modalTitle"></h2>

                        <p class="mt-1 text-sm text-gray-600" x-text="modalMessage"></p>

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close-modal', { name: 'confirmation-modal' })" x-bind:disabled="submitting">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <form :action="actionUrl" method="POST" class="ml-3" @submit="submitting = true">
                                @csrf
                                <input type="hidden" name="_method" :value="actionMethod">

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 text-white shadow-sm bg-primary"
                                    x-bind:class="confirmButtonClass + (submitting ? ' opacity-75 cursor-not-allowed' : '')"
                                    x-bind:disabled="submitting"
                                >
                                    <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="confirmButtonText"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </x-modal>

            </div>
        </div>
    </div>
</x-app-layout>
