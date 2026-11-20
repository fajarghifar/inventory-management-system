<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\DTOs\SaleData;
use App\DTOs\SaleItemData;
use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use App\Services\SaleService;
use App\Exceptions\SaleException;
use Carbon\Carbon;
use Livewire\Component;

class RecordSale extends Component
{
    // Workflow Step: 'select' (Screen A), 'review' (Screen B), 'payment' (Screen C), 'completed' (Screen D)
    public string $step = 'select';

    // Screen A: Product Search & Filter
    public string $search = '';
    public ?int $selectedCategory = null;

    // Cart: Keyed by product_id
    public array $cart = [];

    // Screen C: Payment Details
    public string $paymentMethod = 'cash'; // 'cash' or 'transfer'
    public float|int $cashReceived = 0;
    public float|int $globalDiscount = 0;
    public string $notes = '';
    public ?int $customerId = null;
    public bool $submitting = false;

    // Screen D: Sale Confirmation / Completed Info
    public ?int $completedSaleId = null;
    public ?string $completedInvoice = null;
    public float $completedTotal = 0;
    public float $completedPaid = 0;
    public float $completedChange = 0;
    public string $completedMethod = 'cash';
    public int $completedItemsCount = 0;

    protected $listeners = [
        'refresh-record-sale' => '$refresh',
    ];

    public function mount(): void
    {
        $this->cart = [];
        $this->step = 'select';
    }

    public function updatingSearch(): void
    {
        // Keep in select mode if searching
        if ($this->step !== 'select') {
            $this->step = 'select';
        }
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->selectedCategory = $categoryId;
        if ($this->step !== 'select') {
            $this->step = 'select';
        }
    }

    public function addToCart(int $productId): void
    {
        $product = Product::with('unit')->find($productId);

        if (!$product || !$product->is_active) {
            $this->dispatch('toast', message: 'Product is unavailable.', type: 'error');
            return;
        }

        if ($product->quantity <= 0) {
            $this->dispatch('toast', message: "'{$product->name}' is out of stock!", type: 'error');
            return;
        }

        if (isset($this->cart[$productId])) {
            $currentQty = $this->cart[$productId]['quantity'];
            if ($currentQty >= $product->quantity) {
                $this->dispatch('toast', message: "Cannot add more. Only {$product->quantity} in stock.", type: 'warning');
                return;
            }
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku ?? '-',
                'unit' => $product->unit?->symbol ?: $product->unit?->name ?: 'pcs',
                'image_url' => $product->image_url,
                'selling_price' => (float) $product->selling_price,
                'max_quantity' => (int) $product->quantity,
                'quantity' => 1,
            ];
        }

        // Auto-update cash received if it was matching total exactly
        if ($this->paymentMethod === 'cash') {
            $this->cashReceived = $this->total;
        }

        $this->dispatch('toast', message: "Added '{$product->name}' to sale.", type: 'success');
    }

    public function incrementQty(int $productId): void
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        if ($this->cart[$productId]['quantity'] >= $this->cart[$productId]['max_quantity']) {
            $this->dispatch('toast', message: "Cannot exceed available stock ({$this->cart[$productId]['max_quantity']}).", type: 'warning');
            return;
        }

        $this->cart[$productId]['quantity']++;

        if ($this->paymentMethod === 'cash') {
            $this->cashReceived = $this->total;
        }
    }

    public function decrementQty(int $productId): void
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        if ($this->cart[$productId]['quantity'] > 1) {
            $this->cart[$productId]['quantity']--;
            if ($this->paymentMethod === 'cash') {
                $this->cashReceived = $this->total;
            }
        } else {
            $this->removeFromCart($productId);
        }
    }

    public function updateQty(int $productId, $qty): void
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        $qty = (int) $qty;
        if ($qty <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $max = $this->cart[$productId]['max_quantity'];
        if ($qty > $max) {
            $qty = $max;
            $this->dispatch('toast', message: "Cannot exceed available stock ({$max}).", type: 'warning');
        }

        $this->cart[$productId]['quantity'] = $qty;

        if ($this->paymentMethod === 'cash') {
            $this->cashReceived = $this->total;
        }
    }

    public function updateDiscount($amount): void
    {
        $amount = max(0, (float) $amount);
        if ($amount > $this->subtotal) {
            $amount = $this->subtotal;
        }
        $this->globalDiscount = $amount;
        if ($this->paymentMethod === 'cash') {
            $this->cashReceived = $this->total;
        }
    }

    public function handleBarcodeSearch(): void
    {
        $term = trim($this->search);
        if ($term === '') {
            return;
        }

        // 1. Try exact SKU barcode match
        $product = Product::where('is_active', true)
            ->where('sku', $term)
            ->first();

        // 2. If not found by exact SKU, check if single product matches
        if (!$product) {
            $matches = Product::where('is_active', true)
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%");
                })
                ->take(2)
                ->get();

            if ($matches->count() === 1) {
                $product = $matches->first();
            }
        }

        if ($product) {
            $this->addToCart($product->id);
            $this->search = '';
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);

        if (empty($this->cart)) {
            $this->step = 'select';
            $this->cashReceived = 0;
        } elseif ($this->paymentMethod === 'cash') {
            $this->cashReceived = $this->total;
        }
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->step = 'select';
        $this->cashReceived = 0;
        $this->globalDiscount = 0;
    }

    public function goToReview(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', message: 'Please add at least one product to the sale.', type: 'warning');
            return;
        }
        $this->step = 'review';
    }

    public function goToPayment(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', message: 'Your sale is empty.', type: 'warning');
            $this->step = 'select';
            return;
        }

        $this->step = 'payment';

        if ($this->paymentMethod === 'cash' && ($this->cashReceived <= 0 || $this->cashReceived < $this->total)) {
            $this->cashReceived = $this->total;
        }
    }

    public function goToSelect(): void
    {
        $this->step = 'select';
    }

    public function setPaymentMethod(string $method): void
    {
        if (in_array($method, ['cash', 'transfer'])) {
            $this->paymentMethod = $method;
            if ($method === 'cash') {
                $this->cashReceived = $this->total;
            } else {
                $this->cashReceived = $this->total;
            }
        }
    }

    public function setExactAmount(): void
    {
        $this->cashReceived = $this->total;
    }

    public function addCash(int $amount): void
    {
        $current = (float) $this->cashReceived;
        $this->cashReceived = $current + $amount;
    }

    public function getSubtotalProperty(): float
    {
        $sub = 0;
        foreach ($this->cart as $item) {
            $sub += ($item['quantity'] * $item['selling_price']);
        }
        return (float) $sub;
    }

    public function getTotalProperty(): float
    {
        return max(0, (float) ($this->subtotal - $this->globalDiscount));
    }

    public function getItemsCountProperty(): int
    {
        return count($this->cart);
    }

    public function getTotalUnitsProperty(): int
    {
        return (int) array_sum(array_column($this->cart, 'quantity'));
    }

    public function getDifferenceProperty(): float
    {
        return ((float) $this->cashReceived) - $this->total;
    }

    public function getChangeProperty(): float
    {
        return $this->difference > 0 ? $this->difference : 0.0;
    }

    public function getRemainingBalanceProperty(): float
    {
        return $this->difference < 0 ? abs($this->difference) : 0.0;
    }

    public function confirmSale(SaleService $saleService): void
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', message: 'Your sale is empty. Add products to continue.', type: 'error');
            $this->step = 'select';
            return;
        }

        $total = $this->total;

        // Cash validation
        if ($this->paymentMethod === 'cash' && $this->cashReceived < $total) {
            $needed = number_format($this->remainingBalance, 2);
            $this->dispatch('toast', message: "Payment incomplete. Remaining balance: ETB {$needed}", type: 'error');
            return;
        }

        $this->submitting = true;

        try {
            $items = [];
            foreach ($this->cart as $item) {
                $items[] = new SaleItemData(
                    product_id: $item['product_id'],
                    quantity: (int) $item['quantity'],
                    unit_price: (float) $item['selling_price'],
                    discount: 0
                );
            }

            $saleData = new SaleData(
                sale_date: now(),
                payment_method: PaymentMethod::from($this->paymentMethod),
                created_by: auth()->id(),
                items: $items,
                customer_id: $this->customerId,
                status: SaleStatus::COMPLETED,
                notes: !empty($this->notes) ? $this->notes : 'Direct Mobile Sale',
                cash_received: $this->paymentMethod === 'cash' ? (float) $this->cashReceived : $total,
                change: $this->paymentMethod === 'cash' ? $this->change : 0,
                global_discount: (float) $this->globalDiscount,
            );

            $sale = $saleService->createSale($saleData);

            // Record success data
            $this->completedSale = $sale;
            $this->completedSaleId = $sale->id;
            $this->completedInvoice = $sale->invoice_number;
            $this->completedTotal = $sale->total;
            $this->completedPaid = $sale->cash_received;
            $this->completedChange = $sale->change;
            $this->completedMethod = $this->paymentMethod;
            $this->completedItemsCount = count($this->cart);

            // Transition to Screen D (Completed)
            $this->step = 'completed';
            $this->cart = [];
            $this->cashReceived = 0;
            $this->globalDiscount = 0;
            $this->notes = '';

            $this->dispatch('toast', message: "Sale {$sale->invoice_number} recorded successfully!", type: 'success');

        } catch (SaleException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Failed to record sale: ' . $e->getMessage(), type: 'error');
        } finally {
            $this->submitting = false;
        }
    }

    public function startNewSale(): void
    {
        $this->cart = [];
        $this->step = 'select';
        $this->search = '';
        $this->selectedCategory = null;
        $this->cashReceived = 0;
        $this->globalDiscount = 0;
        $this->notes = '';
        $this->completedSale = null;
        $this->completedSaleId = null;
        $this->completedInvoice = null;
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();

        $query = Product::query()
            ->where('is_active', true)
            ->with(['category', 'unit']);

        if (!empty($this->selectedCategory)) {
            $query->where('category_id', $this->selectedCategory);
        }

        if (trim($this->search) !== '') {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('sku', 'like', $term);
            });
        }

        $products = $query->orderBy('name')->take(60)->get();

        return view('livewire.sales.record-sale', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
