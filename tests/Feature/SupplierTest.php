<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cant_has_access()
    {
        $response = $this->get('suppliers/');

        $response
            ->assertStatus(302)
            ->assertRedirect('login/');
    }

    public function test_logged_user_has_access_to_url()
    {
        $this->withoutExceptionHandling();

        // Create Unit
        $this->createSupplier();
        $this->assertDatabaseCount('suppliers', 1)
            ->assertDatabaseHas('suppliers', ['name' => 'Thomann']);

        $user = $this->createUser();
        $response = $this->actingAs($user)
            ->get('suppliers/');

        $response->assertStatus(200)
            ->assertViewIs('suppliers.index');
    }

    public function test_user_can_use_create_view()
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->get('suppliers/create');

        $response->assertViewIs('suppliers.create');
    }

    public function test_user_can_see_edit_view()
    {
        $user = $this->createUser();
        $supplier = $this->createSupplier();

        $response = $this->actingAs($user)->get('suppliers/'.$supplier->id.'/edit');

        $response
            ->assertStatus(200)
            ->assertViewIs('suppliers.edit');
    }

    public function test_user_can_see_show_view()
    {
        $user = $this->createUser();
        $suppliers = $this->createSupplier();

        $response = $this->actingAs($user)->get('suppliers/'.$suppliers->id);

        $response
            ->assertStatus(200)
            ->assertViewIs('suppliers.show');
    }

    public function test_user_can_delete_category()
    {
        //$this->withoutExceptionHandling();

        $category = $this->createSupplier();

        $this->assertDatabaseHas('suppliers', ['name' => 'Thomann']);
        $this->assertDatabaseCount('suppliers', 1);

        $user = $this->createUser();
        $this->actingAs($user);

        $this->delete('/suppliers/'. $category->id);

        $this->assertDatabaseCount('suppliers', 0);
    }
}
