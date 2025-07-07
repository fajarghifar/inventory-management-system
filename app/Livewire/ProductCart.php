<?php

namespace App\Livewire;

use App\Models\Product;
use Darryldecode\Cart\Cart;
use Livewire\Component;

class ProductCart extends Component
{
    public $listeners = ['productSelected', 'discountModalRefresh'];

    public $cart_instance;
    public $global_discount;
    public $global_tax;
    public $shipping;
    public $quantity = [];
    public $check_quantity = [];
    public $discount_type = [];
    public $item_discount = [];
    public $unit_price = [];
    public $data;
    private $product;

    public function mount($cartInstance, $data = null): void
    {
        $this->cart_instance = $cartInstance;

        if ($data) {
            $this->data = $data;
            $this->global_discount = $data->discount_percentage;
            $this->global_tax = $data->tax_percentage;
            $this->shipping = $data->shipping_amount;

            $this->updatedGlobalTax();
            $this->updatedGlobalDiscount();

            $cart_items = \Cart::session($this->cart_instance)->getContent();

            foreach ($cart_items as $cart_item) {
                $this->check_quantity[$cart_item->id] = $cart_item->attributes->stock;
                $this->quantity[$cart_item->id] = $cart_item->quantity;
                $this->unit_price[$cart_item->id] = $cart_item->price;
                $this->discount_type[$cart_item->id] = $cart_item->attributes->product_discount_type;

                if ($cart_item->attributes->product_discount_type == 'fixed') {
                    $this->item_discount[$cart_item->id] = $cart_item->attributes->product_discount;
                } elseif ($cart_item->attributes->product_discount_type == 'percentage') {
                    $this->item_discount[$cart_item->id] = round(100 * ($cart_item->attributes->product_discount / $cart_item->price));
                }
            }
        } else {
            $this->global_discount = 0;
            $this->global_tax = 0;
            $this->shipping = 0.00;
        }
    }

    public function render()
    {
        $cart_items = \Cart::session($this->cart_instance)->getContent();
        $cart_total = $this->cartTotal();

        return view('livewire.product-cart', compact('cart_items', 'cart_total'));
    }

    public function cartTotal()
    {
        $cart_total = \Cart::session($this->cart_instance)->getTotal();
        $tax_amount = ($this->global_tax / 100) * $cart_total;
        $discount_amount = ($this->global_discount / 100) * $cart_total;

        return $cart_total + $tax_amount - $discount_amount + (float)$this->shipping;
    }

    public function productSelected($product): void
    {
        $cart = \Cart::session($this->cart_instance);

        $exists = $cart->get($product['id']);
        if ($exists) {
            session()->flash('message', 'Product exists in the cart!');
            return;
        }

        $calculated = $this->calculate($product);

        $cart->add([
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $calculated['price'],
            'quantity' => 1,
            'attributes' => collect([
                'product_discount' => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total' => $calculated['sub_total'],
                'code' => $product['code'],
                'stock' => $product['quantity'],
                'unit' => $product['unit_id'],
                'product_tax' => $calculated['tax'],
                'unit_price' => $calculated['unit_price'],
            ])
        ]);

        $this->check_quantity[$product['id']] = $product['quantity'];
        $this->quantity[$product['id']] = 1;
        $this->discount_type[$product['id']] = 'fixed';
        $this->item_discount[$product['id']] = 0;
    }

    public function calculate($product, $new_price = null): array
    {
        $product_price = $new_price ?? ($this->cart_instance === 'purchase' ? $product['product_cost'] : $product['selling_price']);

        $price = $product_price;
        $unit_price = $product_price;
        $product_tax = 0;
        $sub_total = $product_price;

        if ($product['tax_type'] == 1) {
            $product_tax = $product_price * ($product['tax'] / 100);
            $price += $product_tax;
            $sub_total = $price;
        } elseif ($product['tax_type'] == 2) {
            $product_tax = $product_price * ($product['tax'] / 100);
            $unit_price = $product_price - $product_tax;
        }

        return [
            'price' => $price,
            'unit_price' => $unit_price,
            'tax' => $product_tax,
            'sub_total' => $sub_total,
        ];
    }
}
