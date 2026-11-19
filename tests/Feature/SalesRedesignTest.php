<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Unit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Category;
use App\Enums\UserRole;
use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use Livewire\Livewire;
use App\Livewire\Sales\SalesCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesRedesignTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Category $category;
    protected Unit $unit;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Owner',
            'username' => 'admin_sales_tester',
            'role' => UserRole::ADMIN,
        ]);

        $this->staff = User::factory()->create([
            'name' => 'Nahom Girma',
            'username' => 'nahom_cashier',
            'role' => UserRole::STAFF,
        ]);

        $this->category = Category::create([
            'name' => 'Beverages & Drinks',
            'slug' => 'beverages-drinks',
        ]);

        $this->unit = Unit::create([
            'name' => 'Piece',
            'symbol' => 'pcs',
        ]);

        $this->product = Product::create([
            'name' => 'Nescafe Gold Classic Coffee 200g',
            'sku' => '9300675001234',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 150.00,
            'selling_price' => 220.00,
            'quantity' => 50,
            'min_stock' => 5,
            'is_active' => true,
        ]);
    }

    public function test_sales_index_page_renders_successfully(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(route('sales.index'));

        $response->assertOk();
        $response->assertSee('Sales & Invoices', false);
        $response->assertSee('Create Sale');
    }

    public function test_sales_catalog_displays_transactions_and_kpis(): void
    {
        $this->actingAs($this->admin);

        $sale = Sale::create([
            'invoice_number' => 'INV-2026-0001',
            'created_by' => $this->staff->id,
            'sale_date' => now(),
            'status' => SaleStatus::COMPLETED,
            'subtotal' => 440.00,
            'global_discount' => 0,
            'total_discount' => 0,
            'total' => 440.00,
            'cash_received' => 500.00,
            'change' => 60.00,
            'payment_method' => PaymentMethod::CASH,
            'notes' => 'First retail sale of the morning',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'cost_price' => 150.00,
            'unit_price' => 220.00,
            'discount' => 0,
            'final_price' => 220.00,
            'subtotal' => 440.00,
        ]);

        Livewire::test(SalesCatalog::class)
            ->assertSee('INV-2026-0001')
            ->assertSee('Nahom Girma')
            ->assertSee('ETB 440.00')
            ->assertSee('Cash');
    }

    public function test_sales_catalog_date_period_filter(): void
    {
        $this->actingAs($this->admin);

        // Sale today
        Sale::create([
            'invoice_number' => 'INV-TODAY',
            'created_by' => $this->staff->id,
            'sale_date' => now(),
            'status' => SaleStatus::COMPLETED,
            'subtotal' => 220.00,
            'global_discount' => 0,
            'total_discount' => 0,
            'total' => 220.00,
            'payment_method' => PaymentMethod::CASH,
        ]);

        // Sale last month
        Sale::create([
            'invoice_number' => 'INV-LAST-MONTH',
            'created_by' => $this->staff->id,
            'sale_date' => now()->subMonths(2),
            'status' => SaleStatus::COMPLETED,
            'subtotal' => 500.00,
            'global_discount' => 0,
            'total_discount' => 0,
            'total' => 500.00,
            'payment_method' => PaymentMethod::CASH,
        ]);

        Livewire::test(SalesCatalog::class)
            ->set('datePeriod', 'today')
            ->assertSee('INV-TODAY')
            ->assertDontSee('INV-LAST-MONTH');
    }

    public function test_sales_catalog_status_and_payment_filtering(): void
    {
        $this->actingAs($this->admin);

        Sale::create([
            'invoice_number' => 'INV-COMPLETED-CASH',
            'created_by' => $this->staff->id,
            'sale_date' => now(),
            'status' => SaleStatus::COMPLETED,
            'subtotal' => 220.00,
            'global_discount' => 0,
            'total_discount' => 0,
            'total' => 220.00,
            'payment_method' => PaymentMethod::CASH,
        ]);

        Sale::create([
            'invoice_number' => 'INV-CANCELLED-TRANSFER',
            'created_by' => $this->staff->id,
            'sale_date' => now(),
            'status' => SaleStatus::CANCELLED,
            'subtotal' => 600.00,
            'global_discount' => 0,
            'total_discount' => 0,
            'total' => 600.00,
            'payment_method' => PaymentMethod::TRANSFER,
        ]);

        Livewire::test(SalesCatalog::class)
            ->set('statusFilter', 'completed')
            ->assertSee('INV-COMPLETED-CASH')
            ->assertDontSee('INV-CANCELLED-TRANSFER')
            ->set('statusFilter', 'cancelled')
            ->set('paymentFilter', 'transfer')
            ->assertSee('INV-CANCELLED-TRANSFER')
            ->assertDontSee('INV-COMPLETED-CASH');
    }

    public function test_sales_catalog_search_filters_correctly(): void
    {
        $this->actingAs($this->admin);

        Sale::create([
            'invoice_number' => 'INV-UNIQUE-FIND-ME',
            'created_by' => $this->staff->id,
            'sale_date' => now(),
            'status' => SaleStatus::COMPLETED,
            'subtotal' => 100.00,
            'global_discount' => 0,
            'total_discount' => 0,
            'total' => 100.00,
            'payment_method' => PaymentMethod::CASH,
        ]);

        Sale::create([
            'invoice_number' => 'INV-OTHER-ROW',
            'created_by' => $this->staff->id,
            'sale_date' => now(),
            'status' => SaleStatus::COMPLETED,
            'subtotal' => 200.00,
            'global_discount' => 0,
            'total_discount' => 0,
            'total' => 200.00,
            'payment_method' => PaymentMethod::CASH,
        ]);

        Livewire::test(SalesCatalog::class)
            ->set('search', 'UNIQUE-FIND')
            ->assertSee('INV-UNIQUE-FIND-ME')
            ->assertDontSee('INV-OTHER-ROW');
    }

    public function test_sales_show_order_details_view_renders_correctly(): void
    {
        $this->actingAs($this->staff);

        $sale = Sale::create([
            'invoice_number' => 'INV-SHOW-TEST',
            'created_by' => $this->staff->id,
            'sale_date' => now(),
            'status' => SaleStatus::COMPLETED,
            'subtotal' => 440.00,
            'global_discount' => 0,
            'total_discount' => 0,
            'total' => 440.00,
            'cash_received' => 500.00,
            'change' => 60.00,
            'payment_method' => PaymentMethod::CASH,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'cost_price' => 150.00,
            'unit_price' => 220.00,
            'discount' => 0,
            'final_price' => 220.00,
            'subtotal' => 440.00,
        ]);

        $response = $this->get(route('sales.show', $sale));

        $response->assertOk();
        $response->assertSee('Order Details');
        $response->assertSee('INV-SHOW-TEST');
        $response->assertSee('Nescafe Gold Classic Coffee 200g');
        $response->assertSee('Thermal Receipt');
        $response->assertSee('A4 Invoice');
        $response->assertSee('Total Price:');
        $response->assertSee('ETB 440.00');
    }

    public function test_admin_can_export_sales_csv(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(SalesCatalog::class)
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_staff_cannot_export_sales_csv(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(SalesCatalog::class)
            ->call('exportCsv')
            ->assertForbidden();
    }
}
