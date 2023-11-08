<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cant_has_access()
    {
        $response = $this->get('customers/');

        $response
            ->assertStatus(302)
            ->assertRedirect('login/');
    }

    public function test_logged_user_has_access_to_url()
    {
        $this->withoutExceptionHandling();

        // Create Unit
        $this->createCustomer();
        $this->assertDatabaseCount('customers', 1)
            ->assertDatabaseHas('customers', ['name' => 'Customer 1']);

        $user = $this->createUser();
        $response = $this->actingAs($user)
            ->get('customers/');

        $response->assertStatus(200)
            ->assertViewIs('customers.index');
    }

    public function test_user_can_use_create_view()
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->get('customers/create');

        $response->assertViewIs('customers.create');
    }

    public function test_user_can_delete_customer()
    {
        $this->withoutExceptionHandling();

        $customer = $this->createCustomer();

        $this->assertDatabaseHas('customers', ['name' => 'Customer 1']);
        $this->assertDatabaseCount('customers', 1);

        $user = $this->createUser();
        $this->actingAs($user);

        $this->delete('/customers/'. $customer->id);

        $this->assertDatabaseCount('customers', 0);

    }

    public function test_user_can_see_show_view()
    {
        $user = $this->createUser();
        $customer = $this->createCustomer();

        $response = $this->actingAs($user)->get('customers/'.$customer->id);

        $response
            ->assertStatus(200)
            ->assertViewIs('customers.show');
    }

    public function test_user_can_see_edit_view()
    {
        $user = $this->createUser();
        $customer = $this->createCustomer();

        $response = $this->actingAs($user)->get('customers/'.$customer->id.'/edit');

        $response
            ->assertStatus(200)
            ->assertViewIs('customers.edit');
    }
}
