<?php

namespace Tests\Feature\Livewire\Tables;

use App\Livewire\Tables\OrderTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class OrderTableTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(OrderTable::class)
            ->assertStatus(200);
    }
}
