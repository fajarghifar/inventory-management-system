<?php

namespace Tests\Feature\Livewire\Tables;

use App\Livewire\Tables\CustomerTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerTableTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(CustomerTable::class)
            ->assertStatus(200);
    }
}
