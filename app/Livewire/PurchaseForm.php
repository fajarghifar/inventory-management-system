<?php

namespace App\Livewire;

//use App\Models\Invoice;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PurchaseForm extends Component
{
    //public ?Invoice $invoice = null;
    //public string|null $customer_name = '';

    //public string|null $customer_email = '';

    public int $taxes = 0;

    public array $invoiceProducts = [];

    public Collection $allProducts;

    public function mount(/*Invoice $invoice*/): void
    {
        $this->allProducts = Product::all();

        /*
        if ($invoice->exists) {
            $this->invoice = $invoice;
            $this->customer_name = $invoice->customer_name;
            $this->customer_email = $invoice->customer_email;
            $this->taxes = $invoice->taxes;

            foreach ($this->invoice->products as $product) {
                $this->invoiceProducts[] = [
                    'product_id' => $product->id,
                    'quantity' => $product->pivot->quantity,
                    'is_saved' => true,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                ];
            }
        }
        */
    }

    public function render(): View
    {
        $total = 0;

        foreach ($this->invoiceProducts as $invoiceProduct)
        {
            if ($invoiceProduct['is_saved'] && $invoiceProduct['product_price'] && $invoiceProduct['quantity'])
            {
                $total += $invoiceProduct['product_price'] * $invoiceProduct['quantity'];
            }
        }

        return view('livewire.purchase-form', [
            'subtotal' => $total,
            'total' => $total * (1 + (is_numeric($this->taxes) ? $this->taxes : 0) / 100)
        ]);
    }

    public function addProduct(): void
    {
        foreach ($this->invoiceProducts as $key => $invoiceProduct)
        {
            if (!$invoiceProduct['is_saved'])
            {
                $this->addError('invoiceProducts.' . $key, 'This line must be saved before creating a new one.');
                return;
            }
        }

        $this->invoiceProducts[] = [
            'product_id' => '',
            'quantity' => 1,
            'is_saved' => false,
            'product_name' => '',
            'product_price' => 0
        ];
    }

    public function editProduct($index): void
    {
        foreach ($this->invoiceProducts as $key => $invoiceProduct)
        {
            if (! $invoiceProduct['is_saved'])
            {
                $this->addError('invoiceProducts.' . $key, 'This line must be saved before editing another.');
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

    /*
    public function saveInvoice(): Redirector|RedirectResponse
    {
        $this->validate();

        $products = [];

        foreach ($this->invoiceProducts as $product)
        {
            $products[$product['product_id']] = ['quantity' => $product['quantity']];
        }

        if (is_null($this->invoice))
        {
            $invoice = Invoice::create($this->only(['customer_name', 'customer_email', 'taxes']));
            $invoice->products()->sync($products);
        } else {
            $this->invoice->update($this->only(['customer_name', 'customer_email', 'taxes']));
            $this->invoice->products()->sync($products);
        }

        if ($this->designTemplate === 'tailwind')
        {
            return redirect()->route('invoices.index');
        } else {
            return redirect()->route('invoices.index', ['design' => 'bootstrap']);
        }
    }
    */

    protected function rules(): array
    {
        return [
//            'customer_name'  => 'required|string',
//            'customer_email' => 'required|email',
            'taxes'          => 'required',
        ];
    }

}
