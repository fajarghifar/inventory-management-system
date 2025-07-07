<?php

namespace App\Livewire;

use App\Models\Product;
use Darryldecode\Cart\Facades\CartFacade as Cart; // ✅ correct facade
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class OrderForm extends Component
{
    public $cart_instance;

    #[Validate('required', message: 'Please select products')]
    public Collection $allProducts;

    #[Validate('required')]
    public int $taxes = 0;

    public array $invoiceProducts = [];

    public function mount($cartInstance): void
    {
        $this->cart_instance = $cartInstance;
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

        $cart_items = Cart::getContent(); // ✅ no instance() in darryldecode/cart

        return view('livewire.order-form', [
            'subtotal' => $total,
            'total' => $total * (1 + (is_numeric($this->taxes) ? $this->taxes : 0) / 100),
            'cart_items' => $cart_items,
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

        if (!$product) {
            $this->addError('invoiceProducts.'.$index, 'Invalid product selected.');
            return;
        }

        $this->invoiceProducts[$index]['product_name'] = $product->name;
        $this->invoiceProducts[$index]['product_price'] = $product->buying_price;
        $this->invoiceProducts[$index]['is_saved'] = true;

        // ✅ Check if item already in cart
        $exists = Cart::getContent()->filter(function ($item) use ($product) {
            return $item->id == $product->id;
        });

        if ($exists->isNotEmpty()) {
            session()->flash('message', 'Product already exists in the cart!');
            return;
        }

        // ✅ Add to cart
        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->buying_price,
            'quantity' => $this->invoiceProducts[$index]['quantity'],
            'attributes' => [
                'code' => $product->code,
            ],
            'associatedModel' => Product::class
        ]);
    }

    public function removeProduct($index): void
    {
        if (isset($this->invoiceProducts[$index])) {
            // Remove from array
            unset($this->invoiceProducts[$index]);
            $this->invoiceProducts = array_values($this->invoiceProducts);
        }

        // Optional: If removing from cart also
        $product = $this->allProducts->find($this->invoiceProducts[$index]['product_id'] ?? null);

        if ($product) {
            Cart::remove($product->id);
        }
    }
}
