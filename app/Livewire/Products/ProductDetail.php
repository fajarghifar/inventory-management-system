<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;

class ProductDetail extends Component
{
    public ?Product $product = null;

    public function render()
    {
        return view('livewire.products.product-detail');
    }

    #[On('show-product')]
    public function show(Product $product)
    {
        $this->product = $product->load(['category', 'unit']);
        $this->dispatch('open-modal', name: 'product-detail-modal');
    }

    public function edit()
    {
        if ($this->product) {
            $this->dispatch('close-modal', name: 'product-detail-modal');
            $this->dispatch('edit-product', ['product' => $this->product->id]);
        }
    }
}
