<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use App\Enums\UserRole;
use Livewire\Livewire;
use App\Livewire\Products\ProductCatalog;
use App\Livewire\Products\ProductForm;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductRedesignTest extends TestCase
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
            'username' => 'admin_catalog_tester',
            'role' => UserRole::ADMIN,
        ]);

        $this->staff = User::factory()->create([
            'username' => 'staff_catalog_tester',
            'role' => UserRole::STAFF,
        ]);

        $this->category = Category::create([
            'name' => 'Spices & Seasonings',
            'slug' => 'spices-seasonings',
        ]);

        $this->unit = Unit::create([
            'name' => 'Kilogram',
            'symbol' => 'kg',
        ]);
    }

    public function test_product_catalog_page_renders_successfully(): void
    {
        $this->actingAs($this->admin);

        Product::create([
            'name' => 'Ethiopian Red Pepper Berbere 1kg',
            'sku' => 'BRB-001',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 350.00,
            'selling_price' => 480.00,
            'quantity' => 25,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertSee('Products');
        $response->assertSee('Ethiopian Red Pepper Berbere 1kg');
    }

    public function test_catalog_livewire_component_calculates_accurate_inventory_stats(): void
    {
        $this->actingAs($this->admin);

        // 1 in stock
        Product::create([
            'name' => 'Item In Stock',
            'sku' => 'SKU-001',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 10,
            'selling_price' => 20,
            'quantity' => 15,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        // 1 low stock
        Product::create([
            'name' => 'Item Low Stock',
            'sku' => 'SKU-002',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 10,
            'selling_price' => 20,
            'quantity' => 3,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        // 1 out of stock
        Product::create([
            'name' => 'Item Out Stock',
            'sku' => 'SKU-003',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 10,
            'selling_price' => 20,
            'quantity' => 0,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        Livewire::test(ProductCatalog::class)
            ->assertSee('Item In Stock')
            ->assertSee('Item Low Stock')
            ->assertSee('Item Out Stock')
            ->set('stockFilter', 'low_stock')
            ->assertSee('Item Low Stock')
            ->assertDontSee('Item In Stock');
    }

    public function test_product_form_creates_product_with_optional_image(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $fakeImage = UploadedFile::fake()->create('coffee.jpg', 50, 'image/jpeg');

        Livewire::test(ProductForm::class)
            ->call('create')
            ->set('name', 'Yirgacheffe Coffee Beans 500g')
            ->set('sku', 'YRG-001')
            ->set('category_id', $this->category->id)
            ->set('unit_id', $this->unit->id)
            ->set('purchase_price', 250)
            ->set('selling_price', 380)
            ->set('quantity', 40)
            ->set('min_stock', 10)
            ->set('photo', $fakeImage)
            ->call('save')
            ->assertDispatched('close-modal', name: 'product-form-modal');

        $this->assertDatabaseHas('products', [
            'name' => 'Yirgacheffe Coffee Beans 500g',
            'sku' => 'YRG-001',
            'quantity' => 40,
        ]);

        $product = Product::where('sku', 'YRG-001')->first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_staff_cannot_create_or_delete_product(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(ProductForm::class)
            ->call('create')
            ->assertForbidden();

        $product = Product::create([
            'name' => 'Staff Test Product',
            'sku' => 'STF-001',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 50,
            'selling_price' => 75,
            'quantity' => 10,
            'min_stock' => 2,
            'is_active' => true,
        ]);

        Livewire::test(ProductCatalog::class)
            ->call('deleteProduct', $product->id)
            ->assertForbidden();
    }

    public function test_product_seeder_creates_authentic_ethiopian_products_with_images_and_prices(): void
    {
        $this->seed(\Database\Seeders\UnitSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ProductSeeder::class);

        $this->assertGreaterThanOrEqual(30, Product::count());

        $ambo = Product::where('name', 'like', '%Ambo%')->first();
        $this->assertNotNull($ambo);
        $this->assertEquals(28.00, (float) $ambo->purchase_price);
        $this->assertEquals(38.00, (float) $ambo->selling_price);
        $this->assertNotNull($ambo->image);
        $this->assertStringContainsString('products/', $ambo->image);
        $this->assertStringContainsString('storage/products/', $ambo->image_url);

        $teff = Product::where('name', 'like', '%Teff%')->first();
        $this->assertNotNull($teff);
        $this->assertNotNull($teff->image);
    }
}
