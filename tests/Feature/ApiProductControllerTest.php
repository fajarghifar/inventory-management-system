<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_api_url()
    {
        $this->withoutExceptionHandling();

        $this->createProduct();

        $response = $this->get('api/products/');

        $response->assertStatus(200);
        $response->assertSee('Test Product');
        $response->assertDontSee('Test Product 2');
    }

    public function test_product_url_with_query_string()
    {
        $this->createProduct();

        $response = $this->get('api/products?category_id=1');

        $response->assertStatus(200);
        $response->assertSee('Test Product');
        $response->assertDontSee('Test Product 2');
    }

    public function createProduct()
    {
        return Product::factory()->create([
            'name' => 'Test Product',
            'category_id' => $this->createCategory(),
            'unit_id' => $this->createUnit()
        ]);
    }

    public function createCategory()
    {
        return Category::factory()->create([
            'name' => 'Speakers'
        ]);
    }

    public function createUnit()
    {
        return Unit::factory()->create([
            'name' => 'piece'
        ]);
    }


}
