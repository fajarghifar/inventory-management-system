<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use App\Enums\UserRole;
use App\Enums\StockMovementType;
use App\Enums\StockAdjustmentType;
use App\Enums\StockAdjustmentReason;
use App\Services\StockAdjustmentService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockAndFinancialManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Category $category;
    protected Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'username' => 'admin_test',
            'role' => UserRole::ADMIN,
        ]);

        $this->staff = User::factory()->create([
            'username' => 'staff_test',
            'role' => UserRole::STAFF,
        ]);

        $this->category = Category::create([
            'name' => 'General Electronics',
            'slug' => 'general-electronics',
        ]);

        $this->unit = Unit::create([
            'name' => 'Pieces',
            'symbol' => 'pcs',
        ]);
    }

    public function test_staff_is_forbidden_from_admin_only_routes(): void
    {
        $this->actingAs($this->staff);

        // Finance
        $this->get('/finance/transactions')->assertForbidden();

        // Settings
        $this->get('/settings')->assertForbidden();

        // Users
        $this->get('/users')->assertForbidden();

        // Stock Adjustments
        $this->get('/stock/adjustments')->assertForbidden();

        // Reports
        $this->get('/reports')->assertForbidden();
        $this->get('/reports/profit-loss')->assertForbidden();
    }

    public function test_admin_can_access_management_and_reporting_routes(): void
    {
        $this->actingAs($this->admin);

        $this->get('/finance/transactions')->assertOk();
        $this->get('/settings')->assertOk();
        $this->get('/users')->assertOk();
        $this->get('/stock/adjustments')->assertOk();
        $this->get('/stock/movements')->assertOk();
        $this->get('/reports')->assertOk();
        $this->get('/reports/stock-valuation')->assertOk();
        $this->get('/reports/slow-moving')->assertOk();
        $this->get('/reports/sales')->assertOk();
        $this->get('/reports/profit-loss')->assertOk();
    }

    public function test_stock_adjustment_creates_movement_and_updates_product_quantity(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'TEST-001',
            'name' => 'Test Laptop',
            'purchase_price' => 500,
            'selling_price' => 800,
            'quantity' => 20,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $service = app(StockAdjustmentService::class);

        // Record a reduction of 3 units due to damage
        $adjustment = $service->createAdjustment([
            'product_id' => $product->id,
            'type' => StockAdjustmentType::REDUCTION,
            'reason' => StockAdjustmentReason::DAMAGE,
            'quantity' => 3,
            'notes' => 'Screen cracked during delivery',
        ], $this->admin->id);

        $this->assertDatabaseHas('stock_adjustments', [
            'id' => $adjustment->id,
            'product_id' => $product->id,
            'type' => StockAdjustmentType::REDUCTION->value,
            'reason' => StockAdjustmentReason::DAMAGE->value,
            'quantity' => 3,
        ]);

        // Product quantity should now be 17
        $this->assertEquals(17, $product->fresh()->quantity);

        // Verify stock movement was recorded
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'movement_type' => StockMovementType::ADJUSTMENT_OUT->value,
            'quantity' => -3,
            'quantity_before' => 20,
            'quantity_after' => 17,
        ]);
    }

    public function test_report_service_computes_accurate_valuation_and_margins(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'TEST-100',
            'name' => 'Item A',
            'purchase_price' => 10,
            'selling_price' => 25,
            'quantity' => 100,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $reportService = app(ReportService::class);
        $valuation = $reportService->getStockValuationSummary();

        $this->assertEquals(100, $valuation['total_units']);
        $this->assertEquals(1000.0, $valuation['total_cost_valuation']);
        $this->assertEquals(2500.0, $valuation['total_retail_valuation']);
        $this->assertEquals(1500.0, $valuation['projected_profit']);
        $this->assertEquals(60.0, $valuation['projected_margin']);
    }
}

