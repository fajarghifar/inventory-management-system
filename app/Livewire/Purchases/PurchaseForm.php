<?php

namespace App\Livewire\Purchases;

use Carbon\Carbon;
use App\Models\Product;
use Livewire\Component;
use App\Models\Purchase;
use App\Models\Supplier;
use App\DTOs\PurchaseData;
use Illuminate\Validation\Rule;
use App\Services\PurchaseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\PurchaseException;
use Illuminate\Support\Facades\Cache;

class PurchaseForm extends Component
{
    use \Livewire\WithFileUploads;

    public $supplier_id = '';
    public $invoice_number = '';
    public $purchase_date;
    public $due_date;
    public $status = 'draft';
    public $notes = '';
    public $proof_image; // New upload
    public $existing_proof_image; // Existing path

    // Dynamic Items
    public $items = [];

    // Searchable Select Data
    public Collection $suppliers;
    public Collection $products;

    public ?int $purchaseId = null;

    protected function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'invoice_number' => ['nullable', 'string', Rule::unique('purchases', 'invoice_number')->ignore($this->purchaseId)],
            'purchase_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'notes' => ['nullable', 'string'],
            'proof_image' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
            'items.*.selling_price' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function messages(): array
    {
        return [
            'items.required' => 'Please add at least one item to the purchase.',
            'items.min' => 'Please add at least one item to the purchase.',
            'supplier_id.required' => 'The supplier field is required.',
            'purchase_date.required' => 'The purchase date field is required.',
        ];
    }

    public function mount(?Purchase $purchase = null): void
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
            $this->status = $purchase->status->value;
            $this->existing_proof_image = $purchase->proof_image;

            // Map items
            foreach ($purchase->items as $item) {
                $this->items[] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'selling_price' => $item->selling_price,
                    'subtotal' => $item->subtotal,
                ];
            }
        } else {
            $this->purchase_date = date('Y-m-d');
            // Initialize with one empty row
            $this->addItem();
        }

        $this->restoreFromCache();
    }

    public function updated($property): void
    {
        $this->saveToCache();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'selling_price' => 0,
            'subtotal' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index
    }

    public function updatedItems(mixed $value, string $key): void
    {
        // Handle direct Livewire updates for Quantity/Price changes
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $this->calculateSubtotal((int) $index);
        }
        $this->saveToCache();
    }

    #[\Livewire\Attributes\On('option-selected')]
    public function handleOptionSelected($name, $value): void
    {
        // Name format: product_{index} or supplier_id
        if ($name === 'supplier_id') {
            $this->supplier_id = $value;
            $this->saveToCache(); // Persist immediately
            return;
        }

        if (str_starts_with($name, 'product_')) {
            $index = (int) str_replace('product_', '', $name);
            $this->items[$index]['product_id'] = $value;
            $this->updateProductPrice($index, (int) $value);
            $this->saveToCache(); // Persist immediately
        }
    }

    public function updateProductPrice(int $index, int $productId): void
    {
        $product = $this->products->firstWhere('id', $productId);
        if ($product) {
            $this->items[$index]['unit_price'] = $product->purchase_price;
            $this->items[$index]['selling_price'] = $product->selling_price;
        }
        $this->calculateSubtotal($index);
    }

    public function calculateSubtotal(int $index): void
    {
        $qty = (int) ($this->items[$index]['quantity'] ?? 0);
        $price = (int) ($this->items[$index]['unit_price'] ?? 0);
        $this->items[$index]['subtotal'] = $qty * $price;
    }

    public function getTotalProperty(): int
    {
        return (int) collect($this->items)->sum('subtotal');
    }

    public function save(PurchaseService $service)
    {
        $validated = $this->validate();

        try {
            // Handle File Upload
            $proofPath = $this->existing_proof_image;
            if ($this->proof_image) {
                $proofPath = $this->proof_image->store('proofs', 'public');
            }

            $purchaseData = PurchaseData::fromArray([
                'supplier_id' => $this->supplier_id,
                'invoice_number' => $this->invoice_number ?: null,
                'purchase_date' => $this->purchase_date,
                'due_date' => $this->due_date,
                'notes' => $this->notes,
                'status' => $this->status,
                'items' => $this->items,
                'proof_image' => $proofPath,
            ]);

            if ($this->purchaseId) {
                // Update
                $purchase = Purchase::find($this->purchaseId);
                $service->updatePurchase($purchase, $purchaseData);
                session()->flash('success', 'Purchase updated successfully.');
            } else {
                // Create
                $purchase = $service->createPurchase($purchaseData, Auth::id());
                session()->flash('success', 'Purchase created successfully.');
            }

            $this->clearCache();

            return redirect()->route('purchases.show', $purchase);

        } catch (PurchaseException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'An unexpected error occurred: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.purchases.purchase-form');
    }

    protected function getCacheKey(): string
    {
        return 'purchase_form_' . Auth::id() . '_' . ($this->purchaseId ?? 'new');
    }

    protected function saveToCache(): void
    {
        $data = [
            'supplier_id' => $this->supplier_id,
            'invoice_number' => $this->invoice_number,
            'purchase_date' => $this->purchase_date,
            'due_date' => $this->due_date,
            'notes' => $this->notes,
            'status' => $this->status,
            'items' => $this->items,
        ];

        Cache::put($this->getCacheKey(), $data, now()->addHours(2));
    }

    protected function restoreFromCache(): void
    {
        $cached = Cache::get($this->getCacheKey());

        if ($cached) {
            $this->supplier_id = $cached['supplier_id'] ?? $this->supplier_id;
            $this->invoice_number = $cached['invoice_number'] ?? $this->invoice_number;
            $this->purchase_date = $cached['purchase_date'] ?? $this->purchase_date;
            $this->due_date = $cached['due_date'] ?? $this->due_date;
            $this->notes = $cached['notes'] ?? $this->notes;
            $this->status = $cached['status'] ?? $this->status;

            // Only restore items if cache has them, otherwise keep DB/Default
            if (!empty($cached['items'])) {
                $this->items = $cached['items'];
            }

            // Dispatch notification to let user know data was restored
            $this->dispatch('toast', message: 'Restored unsaved draft.', type: 'info');
        }
    }

    protected function clearCache(): void
    {
        Cache::forget($this->getCacheKey());
    }
}
