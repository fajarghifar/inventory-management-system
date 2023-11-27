<?php

namespace Tests\Feature\Livewire\Tables;

use App\Livewire\Tables\ProductTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ProductTableTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(ProductTable::class)
            ->assertStatus(200);
    }
}
