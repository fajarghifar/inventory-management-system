<?php

namespace App\Livewire\Products;

use App\DTOs\ProductData;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use App\Models\Unit;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Services\ProductService;
use App\Exceptions\ProductException;

class ProductForm extends Component
{
    public bool $isEditing = false;
    public ?Product $product = null;

    // Form Fields
    public ?string $sku = null;
    public string $name = '';
    public ?int $category_id = null;
    public ?int $unit_id = null;
    public int $purchase_price = 0;
    public int $selling_price = 0;
    public int $quantity = 0;
    public int $min_stock = 0;
    public bool $is_active = true;
    public string $description = '';
    public string $notes = '';

    // Select Options (Removed for AJAX)
    public ?string $categoryName = null;
    public ?string $unitName = null;

    public function mount()
    {
        // No options to load
    }

    public function render()
    {
        return view('livewire.products.product-form');
    }

    #[On('create-product')]
    public function create(): void
    {
        $this->reset(['sku', 'name', 'category_id', 'unit_id', 'purchase_price', 'selling_price', 'quantity', 'min_stock', 'description', 'notes', 'product', 'isEditing', 'categoryName', 'unitName']);
        $this->is_active = true;

        $this->dispatch('open-modal', name: 'product-form-modal');
    }

    #[On('edit-product')]
    public function edit(Product $product): void
    {
        $this->product = $product;
        $this->sku = $product->sku;
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->unit_id = $product->unit_id;
        $this->purchase_price = $product->purchase_price;
        $this->selling_price = $product->selling_price;
        $this->quantity = $product->quantity;
        $this->min_stock = $product->min_stock;
        $this->is_active = $product->is_active;
        $this->description = $product->description ?? '';
        $this->notes = $product->notes ?? '';

        // Set initial labels for TomSelect
        $this->categoryName = $product->category ? $product->category->name : null;
        $this->unitName = $product->unit ? "{$product->unit->name} ({$product->unit->symbol})" : null;

        $this->isEditing = true;

        $this->dispatch('open-modal', name: 'product-form-modal');
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($this->product?->id)
            ],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'purchase_price' => ['required', 'integer', 'min:0'],
            'selling_price' => ['required', 'integer', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function save(ProductService $service): void
    {
        $validated = $this->validate();

        $data = ProductData::fromArray($validated);

        try {
            if ($this->isEditing && $this->product) {
                $service->updateProduct($this->product, $data);
                $message = 'Product updated successfully.';
            } else {
                $service->createProduct($data);
                $message = 'Product created successfully.';
            }

            $this->dispatch('close-modal', name: 'product-form-modal');
            $this->dispatch('pg:eventRefresh-product-table');
            $this->dispatch('toast', message: $message, type: 'success');
        } catch (ProductException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'An unexpected error occurred.', type: 'error');
        }
    }
}
