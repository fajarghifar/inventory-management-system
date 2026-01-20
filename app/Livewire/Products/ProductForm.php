<?php

namespace App\Livewire\Products;

use Exception;
use App\Models\Unit;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use App\DTOs\ProductData;
use Livewire\Attributes\On;
use App\Services\ProductService;

class ProductForm extends Component
{
    public ?Product $product = null;

    public int $category_id = 0;

    public int $unit_id = 0;

    public string $sku = '';

    public string $name = '';

    public string $description = '';

    public int $purchase_price = 0;

    public int $selling_price = 0;

    public int $quantity = 0;

    public int $min_stock = 0;

    public bool $is_active = true;

    public bool $isEditing = false;

    // Collections for dropdowns
    public $categories;
    public $units;

    protected function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku,' . ($this->product?->id)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required', 'integer', 'min:0'],
            'selling_price' => ['required', 'integer', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function mount(): void
    {
        $this->categories = collect();
        $this->units = collect();
        $this->loadDependencies();
    }

    public function loadDependencies(): void
    {
        $this->categories = Category::orderBy('name')->get();
        $this->units = Unit::orderBy('name')->get();

        //Set initial values for select if needed or leave empty
        if($this->categories->isNotEmpty()) {
            $this->category_id = $this->categories->first()->id;
        }
        if($this->units->isNotEmpty()) {
            $this->unit_id = $this->units->first()->id;
        }
    }

    public function render()
    {
        return view('livewire.products.product-form');
    }

    #[On('create-product')]
    public function create(): void
    {
        $this->reset(['product', 'sku', 'name', 'description', 'purchase_price', 'selling_price', 'quantity', 'min_stock']);
        // Reset IDs to first available or 0?
        // Best to reset to first available if exists to have a valid state or explicitly select "Select one"
        $this->loadDependencies();

        $this->is_active = true;
        $this->isEditing = false;
        $this->dispatch('open-modal', name: 'product-modal');
    }

    #[On('edit-product')]
    public function edit(Product $product): void
    {
        $this->resetValidation();
        $this->product = $product;
        $this->category_id = $product->category_id;
        $this->unit_id = $product->unit_id;
        $this->sku = $product->sku;
        $this->name = $product->name;
        $this->description = $product->description ?? '';
        $this->purchase_price = $product->purchase_price;
        $this->selling_price = $product->selling_price;
        $this->quantity = $product->quantity;
        $this->min_stock = $product->min_stock;
        $this->is_active = $product->is_active;

        $this->loadDependencies(); // Ensure lists are fresh

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'product-modal');
    }

    public function save(ProductService $service): void
    {
        $validated = $this->validate($this->rules());

        try {
            $productData = ProductData::fromArray($validated);

            if ($this->isEditing && $this->product) {
                $service->updateProduct($this->product, $productData);
                $message = 'Product updated successfully.';
            } else {
                $service->createProduct($productData);
                $message = 'Product created successfully.';
            }

            $this->dispatch('close-modal', name: 'product-modal');
            $this->dispatch('pg:eventRefresh-default');
            $this->dispatch('pg:eventRefresh-product-table');
            $this->dispatch('toast', message: $message, type: 'success');
            // Don't fully reset here to keep dependencies loaded, or call loadDependencies again if reset.
            $this->reset(['product', 'sku', 'name', 'description', 'purchase_price', 'selling_price', 'quantity', 'min_stock']);
            $this->isEditing = false;

        } catch (Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }
}
