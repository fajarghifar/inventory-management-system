<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PurchaseForm extends Component
{
    #[Validate('Required')]
    public int $taxes = 0;

    public array $invoiceProducts = [];

    #[Validate('required', message: 'Please select products')]
    public Collection $allProducts;

    public function mount(): void
    {
        $this->allProducts = Product::all();
    }

    public function render(): View
    {
        $total = 0;

        foreach ($this->invoiceProducts as $invoiceProduct) {
            if ($invoiceProduct['is_saved'] && $invoiceProduct['product_price'] && $invoiceProduct['quantity']) {
                $total += $invoiceProduct['product_price'] * $invoiceProduct['quantity'];
            }
        }

        return view('livewire.purchase-form', [
            'subtotal' => $total,
            'total' => $total * (1 + (is_numeric($this->taxes) ? $this->taxes : 0) / 100),
        ]);
    }

    public function addProduct(): void
    {
        foreach ($this->invoiceProducts as $key => $invoiceProduct) {
            if (! $invoiceProduct['is_saved']) {
                $this->addError('invoiceProducts.'.$key, 'This line must be saved before creating a new one.');

                return;
            }
        }

        $this->invoiceProducts[] = [
            'product_id' => '',
            'quantity' => 1,
            'is_saved' => false,
            'product_name' => '',
            'product_price' => 0,
        ];
    }

    public function editProduct($index): void
    {
        foreach ($this->invoiceProducts as $key => $invoiceProduct) {
            if (! $invoiceProduct['is_saved']) {
                $this->addError('invoiceProducts.'.$key, 'This line must be saved before editing another.');

                return;
            }
        }

        $this->invoiceProducts[$index]['is_saved'] = false;
    }

    public function saveProduct($index): void
    {
        $this->resetErrorBag();

        $product = $this->allProducts->find($this->invoiceProducts[$index]['product_id']);

        $this->invoiceProducts[$index]['product_name'] = $product->name;
        $this->invoiceProducts[$index]['product_price'] = $product->buying_price;
        $this->invoiceProducts[$index]['is_saved'] = true;
    }

    public function removeProduct($index): void
    {
        unset($this->invoiceProducts[$index]);

        $this->invoiceProducts = array_values($this->invoiceProducts);
    }
}
