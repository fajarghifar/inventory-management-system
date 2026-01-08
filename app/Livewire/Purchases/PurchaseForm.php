<?php

namespace App\Livewire\Purchases;

use Carbon\Carbon;
use App\Models\Product;
use Livewire\Component;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Livewire\Attributes\Validate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PurchaseForm extends Component
{
    #[Validate('required|exists:suppliers,id', message: 'Please select a supplier')]
    public $supplier_id = '';

    #[Validate('nullable|string|unique:purchases,invoice_number')]
    public $invoice_number = '';

    #[Validate('required|date')]
    public $purchase_date;

    #[Validate('nullable|date|after_or_equal:purchase_date')]
    public $due_date;

    #[Validate('nullable|string')]
    public $notes = '';

    // Dynamic Items
    public $items = [];

    // Searchable Select Data
    public Collection $suppliers;
    public Collection $products;

    public ?int $purchaseId = null;

    public function mount(?Purchase $purchase = null)
    {
        $this->suppliers = Supplier::orderBy('name')->get();
        $this->products = Product::where('is_active', true)->orderBy('name')->get();

        if ($purchase && $purchase->exists) {
            $this->purchaseId = $purchase->id;
            $this->supplier_id = $purchase->supplier_id;
            $this->invoice_number = $purchase->invoice_number;
            $this->purchase_date = Carbon::parse($purchase->purchase_date)->format('Y-m-d');
            $this->due_date = $purchase->due_date ? Carbon::parse($purchase->due_date)->format('Y-m-d') : null;
            $this->notes = $purchase->notes;

            // Map items
            foreach ($purchase->details as $detail) {
                $this->items[] = [
                    'product_id' => $detail->product_id,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'subtotal' => $detail->subtotal,
                ];
            }
        } else {
            $this->purchase_date = date('Y-m-d');
            // Initialize with one empty row
            $this->addItem();
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'subtotal' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index
    }

    public function updatedItems($value, $key)
    {
        // $key is something like "0.quantity" or "0.product_id"
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if ($field === 'product_id') {
                $product = $this->products->firstWhere('id', $value);
                if ($product) {
                    $this->items[$index]['unit_price'] = $product->purchase_price;
                }
            }

            $this->calculateSubtotal($index);
        }
    }

    public function calculateSubtotal($index)
    {
        $qty = (int) ($this->items[$index]['quantity'] ?? 0);
        $price = (int) ($this->items[$index]['unit_price'] ?? 0);
        $this->items[$index]['subtotal'] = $qty * $price;
    }

    public function getTotalProperty()
    {
        return collect($this->items)->sum('subtotal');
    }

    public function save(PurchaseService $service)
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|integer|min:0',
        ], [
            'items.required' => 'Please add at least one item to the purchase.',
            'items.min' => 'Please add at least one item to the purchase.',
            'supplier_id.required' => 'The supplier field is required.',
            'purchase_date.required' => 'The purchase date field is required.',
        ]);

        try {
            $data = [
                'supplier_id' => $this->supplier_id,
                'invoice_number' => $this->invoice_number ?: null,
                'purchase_date' => $this->purchase_date,
                'due_date' => $this->due_date,
                'notes' => $this->notes,
                'status' => 'draft',
            ];

            if ($this->purchaseId) {
                // Update
                $purchase = Purchase::find($this->purchaseId);
                $service->updatePurchase($purchase, $data, $this->items);
                $this->dispatch('toast', message: 'Purchase updated successfully.', type: 'success');
            } else {
                // Create
                $service->createPurchase($data, $this->items, Auth::id());
                $this->dispatch('toast', message: 'Purchase created successfully.', type: 'success');
            }

            return redirect()->route('purchases.index');

        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.purchases.purchase-form');
    }
}
