<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Enums\UserRole;
use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportModuleTest extends TestCase
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
            'username' => 'admin_reports',
            'role' => UserRole::ADMIN,
        ]);

        $this->staff = User::factory()->create([
            'username' => 'staff_reports',
            'role' => UserRole::STAFF,
        ]);

        $this->category = Category::create([
            'name' => 'Groceries',
            'slug' => 'groceries',
        ]);

        $this->unit = Unit::create([
            'name' => 'Pieces',
            'symbol' => 'pcs',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'GROC-001',
            'name' => 'Ethiopian Highland Coffee 500g',
            'purchase_price' => 120.00,
            'selling_price' => 200.00,
            'quantity' => 50,
            'min_stock' => 10,
            'is_active' => true,
        ]);

        // Create a completed sale
        $sale = Sale::create([
            'invoice_number' => 'INV-TEST-001',
            'created_by' => $this->admin->id,
            'sale_date' => now(),
            'status' => SaleStatus::COMPLETED,
            'subtotal' => 400.00,
            'global_discount' => 20.00,
            'total_discount' => 20.00,
            'total' => 380.00,
            'cash_received' => 400.00,
            'change' => 20.00,
            'payment_method' => PaymentMethod::CASH,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'cost_price' => 120.00,
            'unit_price' => 200.00,
            'discount' => 20.00,
            'final_price' => 180.00,
            'subtotal' => 380.00,
        ]);
    }

    public function test_staff_cannot_access_any_report_pages_or_exports(): void
    {
        $this->actingAs($this->staff);

        $this->get(route('reports.index'))->assertForbidden();
        $this->get(route('reports.stock-valuation'))->assertForbidden();
        $this->get(route('reports.slow-moving'))->assertForbidden();
        $this->get(route('reports.sales'))->assertForbidden();
        $this->get(route('reports.profit-loss'))->assertForbidden();

        // Print routes
        $this->get(route('reports.print.overview'))->assertForbidden();
        $this->get(route('reports.print.stock-valuation'))->assertForbidden();
        $this->get(route('reports.print.sales'))->assertForbidden();
        $this->get(route('reports.print.profit-loss'))->assertForbidden();
        $this->get(route('reports.print.slow-moving'))->assertForbidden();

        // Export routes
        $this->get(route('reports.export.stock-valuation-csv'))->assertForbidden();
        $this->get(route('reports.export.sales-csv'))->assertForbidden();
        $this->get(route('reports.export.slow-moving-csv'))->assertForbidden();
    }

    public function test_admin_can_access_all_report_pages(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('reports.index'))->assertOk()
            ->assertSee('Reports')
            ->assertSee('Quick Reports');

        $this->get(route('reports.stock-valuation'))->assertOk()
            ->assertSee('Stock Valuation')
            ->assertSee('Money Spent on Stock')
            ->assertSee('Ethiopian Highland Coffee 500g');

        $this->get(route('reports.slow-moving'))->assertOk()
            ->assertSee('Slow-Moving Stock')
            ->assertSee('Tied-Up Working Capital');

        $this->get(route('reports.sales'))->assertOk()
            ->assertSee('Daily Sales Summary')
            ->assertSee('Sales Performance');

        $this->get(route('reports.profit-loss'))->assertOk()
            ->assertSee('Profit &amp; Loss', false)
            ->assertSee('Gross Store Profit');
    }

    public function test_admin_can_access_all_dedicated_print_routes(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('reports.print.overview'))->assertOk()
            ->assertSee('Business Performance Overview');

        $this->get(route('reports.print.stock-valuation'))->assertOk()
            ->assertSee('Stock Valuation Report');

        $this->get(route('reports.print.sales'))->assertOk()
            ->assertSee('Daily Sales Summary');

        $this->get(route('reports.print.profit-loss'))->assertOk()
            ->assertSee('Profit & Loss Statement', false);

        $this->get(route('reports.print.slow-moving'))->assertOk()
            ->assertSee('Slow-Moving and Unsold Stock Report', false);
    }

    public function test_admin_can_download_all_csv_exports(): void
    {
        $this->actingAs($this->admin);

        // Stock Valuation CSV
        $response = $this->get(route('reports.export.stock-valuation-csv'));
        $response->assertOk();
        $this->assertStringContainsString('text/csv', strtolower($response->headers->get('content-type')));
        $this->assertStringContainsString('stock-valuation-', strtolower($response->headers->get('content-disposition')));

        // Sales CSV
        $response = $this->get(route('reports.export.sales-csv'));
        $response->assertOk();
        $this->assertStringContainsString('text/csv', strtolower($response->headers->get('content-type')));
        $this->assertStringContainsString('sales-summary-', strtolower($response->headers->get('content-disposition')));

        // Slow-Moving CSV
        $response = $this->get(route('reports.export.slow-moving-csv', ['days' => 30]));
        $response->assertOk();
        $this->assertStringContainsString('text/csv', strtolower($response->headers->get('content-type')));
        $this->assertStringContainsString('slow-moving-stock-', strtolower($response->headers->get('content-disposition')));
    }

    public function test_report_service_reports_hub_overview_data(): void
    {
        $reportService = app(ReportService::class);
        $overview = $reportService->getReportsHubOverview('this_month');

        $this->assertArrayHasKey('total_sales', $overview);
        $this->assertArrayHasKey('today_sales', $overview);
        $this->assertArrayHasKey('stock_valuation', $overview);
        $this->assertArrayHasKey('low_stock_count', $overview);
        $this->assertArrayHasKey('out_of_stock_count', $overview);
        $this->assertArrayHasKey('estimated_profit', $overview);
        $this->assertArrayHasKey('sales_trend', $overview);
        $this->assertArrayHasKey('top_products', $overview);
        $this->assertArrayHasKey('recent_sales', $overview);

        $this->assertEquals(380.00, $overview['total_sales']);
        $this->assertCount(7, $overview['sales_trend']);
    }
}
