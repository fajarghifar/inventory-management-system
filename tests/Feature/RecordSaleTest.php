<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Unit;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Enums\UserRole;
use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use Livewire\Livewire;
use App\Livewire\Sales\RecordSale;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecordSaleTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;
    protected User $admin;
    protected Category $category;
    protected Unit $unit;
    protected Product $waterProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::factory()->create([
            'name' => 'Nahom Cashier',
            'username' => 'nahom_employee',
            'role' => UserRole::STAFF,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Owner Admin',
            'username' => 'owner_admin',
            'role' => UserRole::ADMIN,
        ]);

        $this->category = Category::create([
            'name' => 'Beverages',
            'slug' => 'beverages',
        ]);

        $this->unit = Unit::create([
            'name' => 'Bottle',
            'symbol' => 'btl',
        ]);

        $this->waterProduct = Product::create([
            'name' => 'Mineral Spring Water 600ml',
            'sku' => '8992761001001',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 8.00, // buying price
            'selling_price' => 10.00, // selling price
            'quantity' => 20,
            'min_stock' => 5,
            'is_active' => true,
        ]);
    }

    public function test_record_sale_page_renders_successfully_for_staff(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(route('sales.create'));

        $response->assertOk();
        $response->assertSee('Record Sale');
        $response->assertSee('Quick Sale');
        $response->assertSee('Advanced POS');
    }

    public function test_advanced_pos_page_is_accessible_at_sales_pos(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(route('sales.pos'));

        $response->assertOk();
        $response->assertSee('POS Register');
    }

    public function test_record_sale_can_add_products_to_cart_and_calculate_totals(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(RecordSale::class)
            ->call('addToCart', $this->waterProduct->id)
            ->assertSet('cart.' . $this->waterProduct->id . '.quantity', 1)
            ->assertSet('subtotal', 10.00)
            ->assertSet('total', 10.00)
            ->call('incrementQty', $this->waterProduct->id)
            ->assertSet('cart.' . $this->waterProduct->id . '.quantity', 2)
            ->assertSet('subtotal', 20.00)
            ->assertSet('total', 20.00);
    }

    public function test_record_sale_prevents_adding_more_than_available_stock(): void
    {
        $this->actingAs($this->staff);

        $scarceProduct = Product::create([
            'name' => 'Limited Coffee Pack',
            'sku' => 'LIM-01',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 50.00,
            'selling_price' => 100.00,
            'quantity' => 2,
            'min_stock' => 1,
            'is_active' => true,
        ]);

        Livewire::test(RecordSale::class)
            ->call('addToCart', $scarceProduct->id)
            ->call('incrementQty', $scarceProduct->id)
            ->assertSet('cart.' . $scarceProduct->id . '.quantity', 2)
            ->call('incrementQty', $scarceProduct->id) // Attempting 3rd
            ->assertSet('cart.' . $scarceProduct->id . '.quantity', 2)
            ->assertDispatched('toast');
    }

    public function test_record_sale_guided_mobile_workflow_steps(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(RecordSale::class)
            ->assertSet('step', 'select')
            ->call('addToCart', $this->waterProduct->id)
            ->call('goToReview')
            ->assertSet('step', 'review')
            ->call('goToPayment')
            ->assertSet('step', 'payment')
            ->call('goToSelect')
            ->assertSet('step', 'select');
    }

    public function test_record_sale_change_and_remaining_calculations(): void
    {
        $this->actingAs($this->staff);

        // Selling 1 bottle = ETB 10.00
        Livewire::test(RecordSale::class)
            ->call('addToCart', $this->waterProduct->id)
            ->assertSet('total', 10.00)
            // Customer gives ETB 12.00 (Overpayment)
            ->set('cashReceived', 12.00)
            ->assertSet('difference', 2.00)
            ->assertSet('change', 2.00)
            ->assertSet('remainingBalance', 0.0)
            // Customer gives exact ETB 10.00
            ->call('setExactAmount')
            ->assertSet('cashReceived', 10.00)
            ->assertSet('difference', 0.0)
            ->assertSet('change', 0.0)
            ->assertSet('remainingBalance', 0.0)
            // Customer gives ETB 7.00 (Underpayment)
            ->set('cashReceived', 7.00)
            ->assertSet('difference', -3.00)
            ->assertSet('change', 0.0)
            ->assertSet('remainingBalance', 3.00);
    }

    public function test_record_sale_blocks_confirmation_if_cash_underpaid(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(RecordSale::class)
            ->call('addToCart', $this->waterProduct->id)
            ->set('cashReceived', 5.00) // Needs 10.00
            ->call('confirmSale')
            ->assertDispatched('toast')
            ->assertSet('step', 'select'); // Did not proceed to completed

        $this->assertDatabaseCount('sales', 0);
    }

    public function test_record_sale_confirms_cash_sale_deducts_stock_and_freezes_cost_and_selling_price(): void
    {
        $this->actingAs($this->staff);

        // Water: 20 pcs in stock. Purchase: 8.00, Selling: 10.00.
        // Sell 10 bottles = 100.00 Total. Cost = 80.00. Profit = 20.00.
        // Customer gives 120.00 cash. Change = 20.00.
        Livewire::test(RecordSale::class)
            ->call('addToCart', $this->waterProduct->id)
            ->set('cart.' . $this->waterProduct->id . '.quantity', 10)
            ->set('paymentMethod', 'cash')
            ->set('cashReceived', 120.00)
            ->call('confirmSale')
            ->assertSet('step', 'completed')
            ->assertSet('completedTotal', 100.00)
            ->assertSet('completedPaid', 120.00)
            ->assertSet('completedChange', 20.00);

        // Verify stock deducted
        $this->waterProduct->refresh();
        $this->assertEquals(10, $this->waterProduct->quantity); // 20 - 10 = 10

        // Verify sale in database
        $sale = Sale::latest()->first();
        $this->assertNotNull($sale);
        $this->assertEquals(100.00, $sale->total);
        $this->assertEquals(120.00, $sale->cash_received);
        $this->assertEquals(20.00, $sale->change);
        $this->assertEquals(SaleStatus::COMPLETED, $sale->status);
        $this->assertEquals(PaymentMethod::CASH, $sale->payment_method);

        // Verify sale item froze historical cost and selling prices
        $saleItem = $sale->items->first();
        $this->assertEquals(10, $saleItem->quantity);
        $this->assertEquals(8.00, $saleItem->cost_price);
        $this->assertEquals(10.00, $saleItem->unit_price);
        $this->assertEquals(100.00, $saleItem->subtotal);

        // Verify that if tomorrow the product price changes, the historical sale record remains intact
        $this->waterProduct->update([
            'purchase_price' => 15.00,
            'selling_price' => 25.00,
        ]);

        $saleItem->refresh();
        $this->assertEquals(8.00, $saleItem->cost_price);
        $this->assertEquals(10.00, $saleItem->unit_price);
        $this->assertEquals(100.00, $saleItem->subtotal);
    }

    public function test_record_sale_supports_bank_transfer(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(RecordSale::class)
            ->call('addToCart', $this->waterProduct->id)
            ->set('paymentMethod', 'transfer')
            ->call('confirmSale')
            ->assertSet('step', 'completed');

        $sale = Sale::latest()->first();
        $this->assertNotNull($sale);
        $this->assertEquals(PaymentMethod::TRANSFER, $sale->payment_method);
        $this->assertEquals(10.00, $sale->total);
        $this->assertEquals(0, $sale->change);
    }

    public function test_record_sale_direct_quantity_update_and_limits(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(RecordSale::class)
            ->call('addToCart', $this->waterProduct->id)
            ->assertSet('cart.' . $this->waterProduct->id . '.quantity', 1)
            ->call('updateQty', $this->waterProduct->id, 5)
            ->assertSet('cart.' . $this->waterProduct->id . '.quantity', 5)
            ->assertSet('total', 50.00)
            ->call('updateQty', $this->waterProduct->id, 0)
            ->assertSet('cart', []);
    }

    public function test_record_sale_barcode_scanning_and_discount_update(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(RecordSale::class)
            ->set('search', '8992761001001') // waterProduct SKU
            ->call('handleBarcodeSearch')
            ->assertSet('cart.' . $this->waterProduct->id . '.quantity', 1)
            ->assertSet('search', '')
            ->call('updateDiscount', 2.00)
            ->assertSet('globalDiscount', 2.00)
            ->assertSet('total', 8.00); // 10.00 - 2.00 = 8.00
    }

    public function test_mobile_pwa_viewport_and_safe_area_configuration(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(route('sales.create'));
        $response->assertOk();
        $response->assertSee('viewport-fit=cover', false);
        $response->assertSee('interactive-widget=resizes-content', false);
        $response->assertSee('user-scalable=no', false);
        $response->assertSee('maximum-scale=1.0', false);
    }
}
