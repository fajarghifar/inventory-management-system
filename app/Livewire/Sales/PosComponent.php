<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Product;
use App\Models\Customer;
use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use App\Actions\Sales\CreateSaleAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class PosComponent extends Component
{
    // Search & Filter
    public string $search = '';

    // Cart
    public array $cart = [];

    // Transaction Data
    public ?int $customerId = null;
    public string $saleDate;
    public string $paymentMethod = 'cash';
    public string $status = 'completed';
    public ?string $notes = null;

    // Payment
    public $cashReceived = 0;

    // Customer Handling
    public $customerSearch = '';
    public $isCreatingCustomer = false;
    public $newCustomerName = '';
    public $newCustomerPhone = '';
    public $newCustomerEmail = '';
    public $newCustomerAddress = '';
    public $newCustomerNotes = '';

    public bool $showConfirmModal = false;

    public function mount()
    {
        $this->saleDate = now()->format('Y-m-d');
        $this->paymentMethod = PaymentMethod::CASH->value;
        $this->status = SaleStatus::COMPLETED->value;
    }

    public function addCash($amount)
    {
        $this->cashReceived = (int)$this->cashReceived + $amount;
    }

    public function setCashExact()
    {
        $this->cashReceived = $this->total;
    }

    public function openPaymentConfirmation()
    {
        $this->validate([
            'paymentMethod' => 'required',
            'customerId' => 'nullable|exists:customers,id',
        ]);

        if ($this->paymentMethod === 'cash' && $this->cashReceived < $this->total) {
            $this->dispatch('toast', message: 'Uang diterima kurang!', type: 'error');
            return;
        }

        $this->showConfirmModal = true;
    }

    public function processPayment()
    {
        $this->showConfirmModal = false;
        $this->processSale(app(\App\Services\SaleService::class));
    }


    #[Computed]
    public function products()
    {
        return Product::query()
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->limit(20)
            ->get();
    }

    #[Computed]
    public function customers()
    {
        return Customer::query()
            ->when($this->customerSearch, function ($q) {
                $q->where('name', 'like', '%' . $this->customerSearch . '%')
                  ->orWhere('phone', 'like', '%' . $this->customerSearch . '%');
            })
            ->limit(10)
            ->get();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] + 1 > $product->quantity) {
                $this->dispatch('toast', message: 'Insufficient stock!', type: 'error');
                return;
            }
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->selling_price,
                'quantity' => 1,
                'discount' => 0,
                'max_stock' => $product->quantity
            ];
        }

        $this->reset('search');
    }

    public function updateQuantity($productId, $qty)
    {
        if (!isset($this->cart[$productId])) return;

        // Ensure numeric
        $qty = (int) $qty;

        if ($qty > $this->cart[$productId]['max_stock']) {
            $qty = $this->cart[$productId]['max_stock'];
            $this->dispatch('toast', message: 'Max stock reached.', type: 'warning');
        }

        if ($qty < 1) {
            $qty = 1;
        }

        $this->cart[$productId]['quantity'] = $qty;
    }

    public function updateDiscount($productId, $discount)
    {
        if (!isset($this->cart[$productId])) return;
        $this->cart[$productId]['discount'] = (int) $discount;
    }

    public function removeItem($productId)
    {
        unset($this->cart[$productId]);
    }

    public function selectCustomer($id)
    {
        $this->customerId = $id;
        $customer = Customer::find($id);
        $this->customerSearch = $customer ? $customer->name : '';
        $this->isCreatingCustomer = false;
    }

    public function saveNewCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required|string|max:255',
            'newCustomerPhone' => 'nullable|string|max:20',
            'newCustomerEmail' => 'nullable|email|max:255',
            'newCustomerAddress' => 'nullable|string',
            'newCustomerNotes' => 'nullable|string',
        ]);

        $customer = Customer::create([
            'name' => $this->newCustomerName,
            'phone' => $this->newCustomerPhone,
            'email' => $this->newCustomerEmail,
            'address' => $this->newCustomerAddress,
            'notes' => $this->newCustomerNotes,
        ]);

        $this->customerId = $customer->id;
        $this->customerSearch = $customer->name;
        $this->isCreatingCustomer = false;
        $this->reset(['newCustomerName', 'newCustomerPhone', 'newCustomerEmail', 'newCustomerAddress', 'newCustomerNotes']);

        $this->dispatch('toast', message: 'Customer created!', type: 'success');
    }

    public function getGrossSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function getTotalDiscountProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['discount']);
    }

    public function getSubtotalProperty() # This is actually Total Bill in current logic, but let's separate standard "Subtotal" vs "Total"
    {
        return $this->grossSubtotal - $this->totalDiscount;
    }

    public function getTotalProperty()
    {
        return $this->subtotal;
    }

    public function getChangeProperty()
    {
        if ($this->paymentMethod !== PaymentMethod::CASH->value) {
            return 0;
        }
        return max(0, (float)$this->cashReceived - $this->total);
    }

    public function processSale(\App\Services\SaleService $saleService)
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', message: 'Cart is empty!', type: 'error');
            return;
        }

        if ($this->paymentMethod === PaymentMethod::CASH->value && (float)$this->cashReceived < $this->total) {
            $this->dispatch('toast', message: 'Insufficient cash payment!', type: 'error');
            return;
        }

        try {
            $items = collect($this->cart)->map(function ($item) {
                // Determine unit discount by dividing total discount by quantity
                $unitDiscount = $item['quantity'] > 0 ? (int) ($item['discount'] / $item['quantity']) : 0;

                return new \App\DTOs\SaleItemData(
                    product_id: $item['id'],
                    quantity: $item['quantity'],
                    unit_price: $item['price'],
                    discount: $unitDiscount
                );
            })->values()->toArray();

            $saleData = new \App\DTOs\SaleData(
                sale_date: \Carbon\Carbon::parse($this->saleDate . ' ' . now()->format('H:i:s')),
                payment_method: PaymentMethod::from($this->paymentMethod),
                created_by: Auth::id(),
                items: $items,
                customer_id: $this->customerId ?: null,
                status: SaleStatus::from($this->status),
                notes: $this->notes,
                cash_received: (int) $this->cashReceived,
                change: (int) $this->change,
                cash: (int) $this->cashReceived
            );

            $sale = $saleService->createSale($saleData);

            $this->reset(['cart', 'customerId', 'cashReceived', 'notes', 'search']);
            $this->dispatch('toast', message: 'Sale completed! Invoice: ' . $sale->invoice_number, type: 'success');

            // Auto-print in new tab
            $this->dispatch('open-new-tab', url: route('sales.print', $sale));

            // Optional: redirect after a delay or just stay for next sale.
            // If we redirect immediately, the window.open might be blocked or cancelled if logic is client side.
            // Let's rely on client side JS handling the open first.

            // To ensure UI updates (like empty cart) before redirecting or to just refresh, we can use a small delay or just return match.
            // But the user requested "stay on page" OR "open in new tab".
            // "selain berada di halaman yang sama, ia juga akan otomatis open in new tab"
            // So we can keep the redirect but maybe we should fire the event first.

            return redirect()->route('sales.create')->with('success', 'Sale completed! Invoice: ' . $sale->invoice_number);

        } catch (\App\Exceptions\SaleException $e) {
            $this->dispatch('toast', message: 'Sale Error: ' . $e->getMessage(), type: 'error');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.sales.pos-component');
    }
}
