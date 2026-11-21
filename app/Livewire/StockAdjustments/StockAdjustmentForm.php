<?php

namespace App\Livewire\StockAdjustments;

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\On;
use App\Enums\StockAdjustmentType;
use App\Enums\StockAdjustmentReason;
use App\Services\StockAdjustmentService;

class StockAdjustmentForm extends Component
{
    public ?int $product_id = null;
    public ?string $productName = null;
    public int $currentStock = 0;
    public string $type = 'reduction';
    public string $reason = 'damage';
    public int $quantity = 1;
    public ?string $notes = null;

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:addition,reduction'],
            'reason' => ['required', 'in:damage,loss,expiration,correction,internal_use,other'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    #[On('open-stock-adjustment-modal')]
    public function openModal(?int $productId = null): void
    {
        $this->resetValidation();
        $this->reset(['product_id', 'productName', 'currentStock', 'notes']);
        $this->type = 'reduction';
        $this->reason = 'damage';
        $this->quantity = 1;

        if ($productId) {
            $product = Product::find($productId);
            if ($product) {
                $this->product_id = $product->id;
                $this->productName = $product->name . " ({$product->sku})";
                $this->currentStock = $product->quantity;
            }
        }

        $this->dispatch('open-modal', name: 'stock-adjustment-modal');
    }

    public function updatedProductId($value): void
    {
        if ($value) {
            $product = Product::find($value);
            if ($product) {
                $this->productName = $product->name . " ({$product->sku})";
                $this->currentStock = $product->quantity;
            }
        } else {
            $this->productName = null;
            $this->currentStock = 0;
        }
    }

    public function save(StockAdjustmentService $service): void
    {
        $validated = $this->validate();

        // Check if reduction exceeds available stock
        if ($this->type === 'reduction' && $this->quantity > $this->currentStock) {
            $this->addError('quantity', "Cannot reduce by {$this->quantity} units. Only {$this->currentStock} units available in stock.");
            return;
        }

        try {
            $adjustment = $service->createAdjustment([
                'product_id' => $this->product_id,
                'type' => StockAdjustmentType::from($this->type),
                'reason' => StockAdjustmentReason::from($this->reason),
                'quantity' => $this->quantity,
                'notes' => $this->notes,
            ]);

            $this->dispatch('close-modal', name: 'stock-adjustment-modal');
            $this->dispatch('pg:eventRefresh-stock-adjustment-table');
            $this->dispatch('pg:eventRefresh-product-table');
            $this->dispatch('pg:eventRefresh-stock-movement-table');
            $this->dispatch('toast', message: "Stock adjustment {$adjustment->adjustment_number} recorded successfully.", type: 'success');

            $this->reset(['product_id', 'productName', 'currentStock', 'notes', 'quantity']);
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.stock-adjustments.stock-adjustment-form');
    }
}

