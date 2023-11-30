<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cant_has_access()
    {
        $response = $this->get('categories/');

        $response
            ->assertStatus(302)
            ->assertRedirect('login/');
    }

    public function test_logged_user_has_access_to_url()
    {
        $this->withoutExceptionHandling();

        // Create Unit
        $this->createCategory();
        $this->assertDatabaseCount('categories', 1)
            ->assertDatabaseHas('categories', ['name' => 'Speakers']);

        $user = $this->createUser();
        $response = $this->actingAs($user)
            ->get('categories/');

        $response->assertStatus(200)
            ->assertViewIs('categories.index');
    }

    public function test_user_can_use_create_view()
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->get('categories/create');

        $response->assertViewIs('categories.create');
    }

    public function test_user_can_see_edit_view()
    {
        $user = $this->createUser();
        $category = $this->createCategory();

        $response = $this->actingAs($user)->get('categories/'.$category->slug.'/edit');

        $response
            ->assertStatus(200)
            ->assertViewIs('categories.edit');
    }

    public function test_user_can_see_show_view()
    {
        $user = $this->createUser();
        $category = $this->createCategory();

        $response = $this->actingAs($user)->get('categories/'.$category->slug);

        $response
            ->assertStatus(200)
            ->assertViewIs('categories.show');
    }

    public function test_user_can_delete_category()
    {
        //$this->withoutExceptionHandling();

        $category = $this->createCategory();

        $this->assertDatabaseHas('categories', ['name' => 'Speakers']);
        $this->assertDatabaseCount('categories', 1);

        $user = $this->createUser();
        $this->actingAs($user);

        $this->delete('/categories/'. $category->slug);

        $this->assertDatabaseCount('categories', 0);

    }
}
