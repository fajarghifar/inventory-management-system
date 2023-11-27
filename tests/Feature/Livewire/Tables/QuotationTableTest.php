<?php

namespace Tests\Feature\Livewire\Tables;

use App\Livewire\Tables\QuotationTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class QuotationTableTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(QuotationTable::class)
            ->assertStatus(200);
    }
}
