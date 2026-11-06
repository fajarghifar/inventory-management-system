<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\Product;
use App\DTOs\SaleData;
use Livewire\Component;
use App\Enums\SaleStatus;
use Livewire\Attributes\On;
use App\Services\SaleService;
use App\Exceptions\SaleException;

class QuickSaleModal extends Component
{
    public bool $isOpen = false;
    public ?int $productId = null;
    public ?Product $product = null;

    public int $quantity = 1;
    public string $paymentMethod = 'cash';
    public float|int $cashReceived = 0;
    public float|int $discount = 0;
    public string $notes = 'Quick Direct Sale';

    public bool $isSuccess = false;
    public ?int $completedSaleId = null;
    public ?string $completedInvoice = null;
    public float|int $completedChange = 0;

    protected function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'paymentMethod' => ['required', 'string', 'in:cash,transfer,card,qris'],
            'cashReceived' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    #[On('open-quick-sale-modal')]
    public function open($productId): void
    {
        if (is_array($productId)) {
            $productId = $productId['productId'] ?? ($productId[0] ?? null);
        }
        $productId = (int) $productId;

        $this->product = Product::with(['unit', 'category'])->find($productId);

        if (!$this->product) {
            $this->dispatch('toast', message: 'Product not found.', type: 'error');
            return;
        }

        if ($this->product->quantity <= 0) {
            $this->dispatch('toast', message: "Product '{$this->product->name}' is out of stock!", type: 'error');
            return;
        }

        $this->productId = $productId;
        $this->quantity = 1;
        $this->discount = 0;
        $this->paymentMethod = 'cash';
        $this->cashReceived = $this->product->selling_price;
        $this->notes = 'Quick Direct Sale';
        $this->isSuccess = false;
        $this->completedSaleId = null;
        $this->completedInvoice = null;
        $this->completedChange = 0;

        $this->isOpen = true;
        $this->dispatch('open-modal', name: 'quick-sale-modal');
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->dispatch('close-modal', name: 'quick-sale-modal');
        $this->reset(['productId', 'product', 'quantity', 'discount', 'isSuccess', 'completedSaleId', 'completedInvoice']);
    }

    public function increment(): void
    {
        if ($this->product && $this->quantity < $this->product->quantity) {
            $this->quantity++;
            if ($this->paymentMethod === 'cash') {
                $this->cashReceived = $this->getTotalProperty();
            }
        }
    }

    public function decrement(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
            if ($this->paymentMethod === 'cash') {
                $this->cashReceived = $this->getTotalProperty();
            }
        }
    }

    public function setExactCash(): void
    {
        $this->cashReceived = $this->getTotalProperty();
    }

    public function addCash(float|int $amount): void
    {
        $this->cashReceived = (float) ($this->cashReceived ?? 0) + $amount;
    }

    public function getSubtotalProperty(): float|int
    {
        if (!$this->product) return 0;
        return $this->quantity * (float) $this->product->selling_price;
    }

    public function getTotalProperty(): float|int
    {
        return max(0, $this->getSubtotalProperty() - (float) ($this->discount ?? 0));
    }

    public function getProfitProperty(): float|int
    {
        if (!$this->product) return 0;
        $cost = (float) $this->product->purchase_price * $this->quantity;
        return max(0, $this->getTotalProperty() - $cost);
    }

    public function getMarginPercentProperty(): float|int
    {
        if (!$this->product || $this->product->purchase_price <= 0) return 0;
        $cost = (float) $this->product->purchase_price * $this->quantity;
        return $cost > 0 ? round(($this->getProfitProperty() / $cost) * 100, 1) : 0;
    }

    public function getChangeProperty(): float|int
    {
        if ($this->paymentMethod !== 'cash') return 0;
        return max(0, (float) ($this->cashReceived ?? 0) - $this->getTotalProperty());
    }

    public function submit(SaleService $saleService): void
    {
        $this->validate();

        if (!$this->product) {
            $this->dispatch('toast', message: 'Product not selected.', type: 'error');
            return;
        }

        // Fresh check on stock
        $freshProduct = Product::find($this->productId);
        if (!$freshProduct || $freshProduct->quantity < $this->quantity) {
            $available = $freshProduct ? $freshProduct->quantity : 0;
            $this->dispatch('toast', message: "Insufficient stock! Only {$available} available.", type: 'error');
            return;
        }

        $total = $this->getTotalProperty();
        $change = $this->getChangeProperty();

        if ($this->paymentMethod === 'cash' && $this->cashReceived < $total) {
            $this->dispatch('toast', message: 'Cash received is less than the total amount.', type: 'error');
            return;
        }

        try {
            $saleData = SaleData::fromArray([
                'customer_id' => null, // Walk-in direct sale
                'sale_date' => now()->toDateString(),
                'payment_method' => $this->paymentMethod,
                'status' => SaleStatus::COMPLETED->value,
                'notes' => $this->notes,
                'cash_received' => $this->paymentMethod === 'cash' ? (float) $this->cashReceived : $total,
                'change' => $change,
                'global_discount' => 0,
                'created_by' => auth()->id(),
                'items' => [
                    [
                        'product_id' => $this->productId,
                        'quantity' => $this->quantity,
                        'unit_price' => (float) $this->product->selling_price,
                        'discount' => (float) ($this->discount ?? 0),
                    ]
                ]
            ]);

            $sale = $saleService->createSale($saleData);

            $this->completedSaleId = $sale->id;
            $this->completedInvoice = $sale->invoice_number;
            $this->completedChange = $change;
            $this->isSuccess = true;

            // Refresh table
            $this->dispatch('pg:eventRefresh-product-table');
            $this->dispatch('toast', message: "Sale completed! Invoice #{$sale->invoice_number}. New stock: " . ($freshProduct->quantity - $this->quantity), type: 'success');

        } catch (SaleException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Failed to complete sale: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.sales.quick-sale-modal');
    }
}
