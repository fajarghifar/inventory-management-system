<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use App\Services\ProductService;
use App\Exceptions\ProductException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public $selectedCategory = null;

    #[Url(as: 'status')]
    public string $stockFilter = 'all'; // all, in_stock, low_stock, out_of_stock, inactive

    #[Url(as: 'sort')]
    public string $sortBy = 'latest'; // latest, name_asc, name_desc, price_asc, price_desc, qty_asc, qty_desc

    #[Url(as: 'per_page')]
    public int $perPage = 12;

    public string $viewMode = 'adaptive'; // adaptive (table on lg, cards on mobile), table, grid

    protected $listeners = [
        'pg:eventRefresh-product-table' => '$refresh',
        'product-saved' => '$refresh',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatingStockFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedCategory', 'stockFilter', 'sortBy']);
        $this->resetPage();
    }

    public function deleteProduct(int $id, ProductService $service): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Unauthorized.');

        $product = Product::find($id);
        if ($product) {
            try {
                $service->deleteProduct($product);
                $this->dispatch('toast', message: 'Product deleted successfully.', type: 'success');
            } catch (\Exception $e) {
                $message = $e instanceof ProductException
                    ? $e->getMessage()
                    : 'Failed to delete product: ' . $e->getMessage();

                $this->dispatch('toast', message: $message, type: 'error');
            }
        }
    }

    public function exportCsv(): StreamedResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Unauthorized.');
        $fileName = 'products_export_' . now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'SKU', 'Name', 'Category', 'Unit', 'Buying Price', 'Selling Price', 'Margin', 'Quantity', 'Min Stock', 'Status', 'Created At']);

            Product::with(['category', 'unit'])->chunk(250, function ($products) use ($handle) {
                foreach ($products as $p) {
                    $margin = $p->selling_price - $p->purchase_price;
                    fputcsv($handle, [
                        $p->id,
                        $p->sku ?? '-',
                        $p->name,
                        $p->category?->name ?? '-',
                        $p->unit?->symbol ?? '-',
                        $p->purchase_price,
                        $p->selling_price,
                        $margin,
                        $p->quantity,
                        $p->min_stock,
                        $p->is_active ? 'Active' : 'Inactive',
                        $p->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });
            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        // 1. Live Inventory Statistics
        $stats = [
            'total' => Product::count(),
            'in_stock' => Product::where('is_active', true)->whereColumn('quantity', '>', 'min_stock')->count(),
            'low_stock' => Product::where('is_active', true)->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'min_stock')->count(),
            'out_of_stock' => Product::where('is_active', true)->where('quantity', '<=', 0)->count(),
        ];

        // 2. Query Builder
        $query = Product::query()
            ->with(['category', 'unit']);

        // Search
        if (trim($this->search) !== '') {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('sku', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        // Category Filter
        if (!empty($this->selectedCategory)) {
            $query->where('category_id', (int) $this->selectedCategory);
        }

        // Stock Filter
        match ($this->stockFilter) {
            'in_stock' => $query->where('is_active', true)->whereColumn('quantity', '>', 'min_stock'),
            'low_stock' => $query->where('is_active', true)->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'min_stock'),
            'out_of_stock' => $query->where('is_active', true)->where('quantity', '<=', 0),
            'inactive' => $query->where('is_active', false),
            default => null,
        };

        // Sorting
        match ($this->sortBy) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'price_asc' => $query->orderBy('selling_price', 'asc'),
            'price_desc' => $query->orderBy('selling_price', 'desc'),
            'qty_asc' => $query->orderBy('quantity', 'asc'),
            'qty_desc' => $query->orderBy('quantity', 'desc'),
            default => $query->latest('id'),
        };

        $products = $query->paginate($this->perPage);
        $categories = Category::orderBy('name')->get();

        return view('livewire.products.product-catalog', [
            'products' => $products,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }
}
