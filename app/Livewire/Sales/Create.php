<?php

namespace App\Livewire\Sales;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use App\DTOs\SaleData;
use App\DTOs\SaleItemData;
use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use App\Services\SaleService;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\SaleException;

class Create extends Component
{
    // Search & Filter
    public $search = '';
    public $category_id = '';

    // Cart
    public $cart = []; // [product_id => [id, name, price, quantity, discount, stock, unit]]

    // Payment & Customer
    public $customer_id = null;
    public $customerSearch = ''; // Search input for customer
    public $payment_method = 'cash';
    public $sale_date;
    public $notes = '';
    public $cash_received = 0; // Input for cash payment

    // New Customer Modal State
    public $newCustomer = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'address' => '',
        'notes' => '',
    ];

    // State
    public $isProcessing = false;

    public function mount()
    {
        $this->sale_date = now()->format('Y-m-d');
        // Default empty, user can type to search
        $this->customerSearch = '';
    }

    // --- Computed Properties ---

    #[Computed]
    public function filteredCustomers()
    {
        $query = Customer::query()->orderBy('name');

        if (!empty($this->customerSearch)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->customerSearch . '%')
                  ->orWhere('phone', 'like', '%' . $this->customerSearch . '%');
            });
        } else {
            // Return nothing if search is empty
            return collect();
        }

        return $query->limit(10)->get();
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->with(['category', 'unit'])
            ->where('quantity', '>', 0) // Only available products
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->limit(20)
            ->get();
    }

    #[Computed]
    public function subtotal()
    {
        return collect($this->cart)->sum(fn($item) => ($item['price'] - $item['discount']) * $item['quantity']);
    }

    #[Computed]
    public function total()
    {
        return $this->subtotal; // Add tax here if needed later
    }

    #[Computed]
    public function change()
    {
        if ($this->payment_method !== 'cash') return 0;
        return max(0, $this->cash_received - $this->total);
    }

    #[Computed]
    public function isValidPayment()
    {
        if ($this->payment_method === 'cash') {
            return $this->cash_received >= $this->total;
        }
        return true; // Non-cash assumed paid or pending
    }

    // --- Actions ---

    public function selectCustomer($id, $name)
    {
        $this->customer_id = $id;
        $this->customerSearch = $name;
    }

    public function resetCustomerSearch()
    {
        $this->customer_id = null;
        $this->customerSearch = '';
    }

    public function openCustomerModal()
    {
        $this->resetNewCustomer();
        $this->dispatch('open-modal', name: 'customer-modal');
    }

    public function closeCustomerModal()
    {
        $this->dispatch('close-modal', name: 'customer-modal');
        $this->resetNewCustomer();
    }

    public function resetNewCustomer()
    {
        $this->newCustomer = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'notes' => '',
        ];
        $this->resetErrorBag();
    }

    public function saveNewCustomer()
    {
        $this->validate([
            'newCustomer.name' => 'required|string|max:255',
            'newCustomer.email' => 'nullable|email|max:255',
            'newCustomer.phone' => 'nullable|string|max:20',
            'newCustomer.address' => 'nullable|string|max:500',
            'newCustomer.notes' => 'nullable|string|max:500',
        ]);

        try {
            $customer = Customer::create($this->newCustomer);

            // Should we use a service? Ideally yes, but for now this is direct.
            // Assuming CustomerService usage is preferred but user just gave Model code.
            // I'll stick to direct model creation as per the snippet context unless forced.

            $this->selectCustomer($customer->id, $customer->name);
            $this->dispatch('close-modal', name: 'customer-modal');
            $this->dispatch('toast', message: 'Customer created successfully!', type: 'success');

        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error creating customer: ' . $e->getMessage(), type: 'error');
        }
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        if (max(0, $product->quantity) <= 0) {
            $this->dispatch('toast', message: 'Stok habis!', type: 'error');
            return;
        }

        if (isset($this->cart[$productId])) {
            // Increment existing
            if ($this->cart[$productId]['quantity'] + 1 > $product->quantity) {
                 $this->dispatch('toast', message: 'Stok tidak cukup!', type: 'error');
                 return;
            }
            $this->cart[$productId]['quantity']++;
        } else {
            // Add new
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->selling_price,
                'quantity' => 1,
                'discount' => 0,
                'stock' => $product->quantity, // Track max stock
                'unit' => $product->unit->symbol ?? '',
                'sku' => $product->sku
            ];
        }
        $this->resetSearch();
    }

    public function resetSearch()
    {
        $this->search = '';
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
    }

    public function updateQuantity($productId, $qty)
    {
        if (!isset($this->cart[$productId])) return;

        $qty = (int) $qty;
        $stock = $this->cart[$productId]['stock'];

        if ($qty <= 0) {
            $qty = 1;
        }

        if ($qty > $stock) {
            $qty = $stock;
            $this->dispatch('toast', message: 'Maksimum stok tercapai', type: 'warning');
        }

        $this->cart[$productId]['quantity'] = $qty;
    }

    public function updateDiscount($productId, $discount)
    {
        if (!isset($this->cart[$productId])) return;

        $price = $this->cart[$productId]['price'];
        // Ensure discount doesn't exceed price
        if ($discount > $price) $discount = $price;
        if ($discount < 0) $discount = 0;

        $this->cart[$productId]['discount'] = (int) $discount;
    }

    public function submit(SaleService $saleService)
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', message: 'Keranjang kosong!', type: 'error');
            return;
        }

        if (!$this->isValidPayment) {
             $this->dispatch('toast', message: 'Pembayaran kurang!', type: 'error');
             return;
        }

        $this->isProcessing = true;

        try {
            // Map items to DTO
            $itemsData = [];
            foreach ($this->cart as $item) {
                $itemsData[] = [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'discount' => $item['discount'],
                ];
            }

            // Determine status
            $status = SaleStatus::COMPLETED;
            // Logic: if cash and paid fully -> COMPLETED. If Pending/Credit -> PENDING.
            // For this simple POS, we assume immediate completion if paid.
            // But if user selects "Pending" status explicitly?
            // For now, let's auto-detect: if cash >= total -> completed.

            // Create DTO
            $saleData = SaleData::fromRequest([
                'sale_date' => $this->sale_date,
                'payment_method' => $this->payment_method,
                'created_by' => Auth::id(),
                'items' => $itemsData,
                'customer_id' => $this->customer_id,
                'status' => $status->value,
                'notes' => $this->notes,
                'cash_received' => $this->cash_received,
                'change' => $this->change,
            ]);

            $sale = $saleService->createSale($saleData);

            // Redirect to Print or Show
            return redirect()->route('sales.show', $sale)
                ->with('success', 'Penjualan berhasil disimpan!');

        } catch (SaleException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Terjadi kesalahan: ' . $e->getMessage(), type: 'error');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.sales.create');
    }
}
