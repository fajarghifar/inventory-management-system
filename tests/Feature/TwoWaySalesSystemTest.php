<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Enums\UserRole;
use Livewire\Livewire;
use App\Livewire\Sales\QuickSaleModal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TwoWaySalesSystemTest extends TestCase
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
            'username' => 'admin_pos',
            'role' => UserRole::ADMIN,
        ]);

        $this->staff = User::factory()->create([
            'username' => 'staff_pos',
            'role' => UserRole::STAFF,
        ]);

        $this->category = Category::create([
            'name' => 'Beverages',
            'slug' => 'beverages',
        ]);

        $this->unit = Unit::create([
            'name' => 'Bottles',
            'symbol' => 'btl',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'sku' => 'BEV-001',
            'name' => 'Mineral Water 600ml',
            'purchase_price' => 3000,
            'selling_price' => 5000,
            'quantity' => 50,
            'min_stock' => 5,
            'is_active' => true,
        ]);
    }

    public function test_thermal_receipt_view_is_accessible_and_renders_correctly(): void
    {
        $this->actingAs($this->staff);

        // Create a completed sale
        $sale = Sale::create([
            'invoice_number' => 'INV-TEST-001',
            'sale_date' => now()->toDateString(),
            'status' => \App\Enums\SaleStatus::COMPLETED,
            'payment_method' => \App\Enums\PaymentMethod::CASH,
            'created_by' => $this->staff->id,
            'subtotal' => 10000,
            'global_discount' => 0,
            'total' => 10000,
            'cash_received' => 15000,
            'change' => 5000,
        ]);

        $sale->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'cost_price' => 3000,
            'unit_price' => 5000,
            'discount' => 0,
            'final_price' => 5000,
            'subtotal' => 10000,
        ]);

        $response = $this->get(route('sales.receipt', $sale));

        $response->assertOk();
        $response->assertSee('INV-TEST-001');
        $response->assertSee('Mineral Water 600ml');
        $response->assertSee('CHANGE DUE:');
        $response->assertSee('THANK YOU FOR YOUR PURCHASE!');
    }

    public function test_quick_sale_modal_executes_sale_decrements_stock_and_records_profit(): void
    {
        $this->actingAs($this->admin);

        $initialStock = $this->product->quantity; // 50
        $qtyToSell = 3;

        Livewire::test(QuickSaleModal::class)
            ->dispatch('open-quick-sale-modal', productId: $this->product->id)
            ->assertSet('productId', $this->product->id)
            ->assertSet('quantity', 1)
            ->set('quantity', $qtyToSell)
            ->set('paymentMethod', 'cash')
            ->set('cashReceived', 20000)
            ->call('submit')
            ->assertSet('isSuccess', true)
            ->assertDispatched('toast')
            ->assertDispatched('pg:eventRefresh-product-table');

        // Verify stock decreased
        $this->product->refresh();
        $this->assertEquals($initialStock - $qtyToSell, $this->product->quantity); // 47

        // Verify Sale and items in database
        $sale = Sale::where('created_by', $this->admin->id)->latest()->first();
        $this->assertNotNull($sale);
        $this->assertEquals(15000, $sale->total); // 3 * 5000
        $this->assertEquals(20000, $sale->cash_received);
        $this->assertEquals(5000, $sale->change);

        // Verify cost price recorded correctly for profit
        $item = $sale->items->first();
        $this->assertEquals(3, $item->quantity);
        $this->assertEquals(3000, $item->cost_price);

        // Verify stock movement was recorded
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'quantity' => -$qtyToSell,
            'movement_type' => \App\Enums\StockMovementType::SALE->value,
            'reference_number' => $sale->invoice_number,
        ]);
    }

    public function test_quick_sale_prevents_selling_more_than_available_stock(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(QuickSaleModal::class)
            ->dispatch('open-quick-sale-modal', productId: $this->product->id)
            ->set('quantity', 100) // Available is only 50
            ->set('paymentMethod', 'cash')
            ->set('cashReceived', 500000)
            ->call('submit')
            ->assertSet('isSuccess', false)
            ->assertDispatched('toast', message: 'Insufficient stock! Only 50 available.');

        // Stock remains unchanged
        $this->product->refresh();
        $this->assertEquals(50, $this->product->quantity);
    }
}
