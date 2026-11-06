<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Sale;
use App\Enums\UserRole;
use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class PwaOfflineSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;
    protected User $admin;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'username' => 'admin_pwa',
            'email' => 'admin_pwa@test.com',
        ]);

        $this->staff = User::factory()->create([
            'role' => UserRole::STAFF->value,
            'username' => 'staff_pwa',
            'email' => 'staff_pwa@test.com',
        ]);

        $category = Category::create([
            'name' => 'Beverages',
            'slug' => 'beverages',
        ]);

        $unit = Unit::create([
            'name' => 'Pcs',
            'slug' => 'pcs',
            'symbol' => 'pcs',
        ]);

        $this->product = Product::create([
            'name' => 'Espresso Beans 500g',
            'sku' => 'PWA-COFFEE-01',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 50000,
            'selling_price' => 85000,
            'quantity' => 20,
            'alert_quantity' => 5,
        ]);
    }

    public function test_pwa_manifest_is_accessible_and_valid(): void
    {
        $manifestPath = public_path('manifest.webmanifest');
        $this->assertFileExists($manifestPath);

        $json = json_decode(file_get_contents($manifestPath), true);
        $this->assertIsArray($json);
        $this->assertEquals('Inventory & Financial Management System', $json['name']);
        $this->assertEquals('Inventory PWA', $json['short_name']);
        $this->assertEquals('standalone', $json['display']);
        $this->assertEquals('#0f172a', $json['theme_color']);
        $this->assertNotEmpty($json['icons']);
        $this->assertNotEmpty($json['shortcuts']);
    }

    public function test_pwa_icons_and_service_worker_exist(): void
    {
        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(public_path('images/icons/icon-192x192.png'));
        $this->assertFileExists(public_path('images/icons/icon-512x512.png'));
        $this->assertFileExists(public_path('images/icons/icon-maskable-512x512.png'));
        $this->assertFileExists(public_path('images/icons/apple-touch-icon.png'));
        $this->assertFileExists(public_path('images/icons/favicon-32x32.png'));
        $this->assertFileExists(public_path('js/pwa-db.js'));

        $swContent = file_get_contents(public_path('sw.js'));
        $this->assertStringContainsString('/livewire/', $swContent, 'Service Worker must contain bypass for Livewire');
        $this->assertStringContainsString('/offline', $swContent, 'Service Worker must precache offline fallback');
    }

    public function test_offline_fallback_page_is_publicly_accessible(): void
    {
        $response = $this->get('/offline');
        $response->assertStatus(200);
        $response->assertSee('No Internet Connection');
        $response->assertSee('Open Offline POS');
    }

    public function test_products_catalog_api_returns_active_products_for_offline_cache(): void
    {
        $response = $this->actingAs($this->staff)->getJson(route('ajax.products.catalog'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $this->product->id,
            'name' => 'Espresso Beans 500g',
            'sku' => 'PWA-COFFEE-01',
            'quantity' => 20,
        ]);
    }

    public function test_offline_sale_sync_is_idempotent_using_offline_uuid(): void
    {
        $offlineUuid = (string) Str::uuid();

        $payload = [
            'offline_uuid' => $offlineUuid,
            'customer_id' => null,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 3,
                    'unit_price' => 85000,
                    'discount' => 0,
                ]
            ],
            'payment_method' => PaymentMethod::CASH->value,
            'cash_received' => 300000,
            'notes' => 'PWA Offline Sync Test',
            'global_discount' => 0,
            'status' => SaleStatus::COMPLETED->value,
            'sale_date' => now()->toDateString(),
        ];

        // First sync submission
        $firstResponse = $this->actingAs($this->staff)->postJson(route('sales.store'), $payload);
        $firstResponse->assertStatus(201);
        $firstResponse->assertJson(['success' => true]);

        $createdSale = Sale::where('offline_uuid', $offlineUuid)->first();
        $this->assertNotNull($createdSale);
        $this->assertEquals(17, $this->product->fresh()->quantity, 'Stock should decrease from 20 to 17');

        // Second sync submission with identical offline_uuid (simulating network retry)
        $secondResponse = $this->actingAs($this->staff)->postJson(route('sales.store'), $payload);
        $secondResponse->assertSuccessful();

        // Must still have only 1 sale in database
        $this->assertEquals(1, Sale::where('offline_uuid', $offlineUuid)->count());
        // Stock must NOT be deducted again
        $this->assertEquals(17, $this->product->fresh()->quantity, 'Idempotency failed: stock was deducted twice');
    }

    public function test_offline_sale_sync_handles_stock_conflict_gracefully(): void
    {
        $offlineUuid = (string) Str::uuid();

        // Product only has 20 in stock, offline sale requested 25
        $payload = [
            'offline_uuid' => $offlineUuid,
            'customer_id' => null,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 25,
                    'unit_price' => 85000,
                    'discount' => 0,
                ]
            ],
            'payment_method' => PaymentMethod::CASH->value,
            'cash_received' => 2500000,
            'notes' => 'PWA Conflict Test',
            'global_discount' => 0,
            'status' => SaleStatus::COMPLETED->value,
            'sale_date' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->staff)->postJson(route('sales.store'), $payload);

        // Expect 409 conflict or 400 with descriptive error
        $response->assertStatus(409);
        $response->assertJsonFragment(['conflict' => true]);
        $this->assertStringContainsString('Insufficient stock', $response->json('message'));

        // Confirm no sale record was created and product stock remained untouched
        $this->assertNull(Sale::where('offline_uuid', $offlineUuid)->first());
        $this->assertEquals(20, $this->product->fresh()->quantity);
    }
}

