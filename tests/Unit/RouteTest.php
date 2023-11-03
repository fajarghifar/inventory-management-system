<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteTest extends TestCase
{

    public function test_login_route(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
    }

    public function test_register_route(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_dashboard_route_redirect_unauthorized_user_to_login_page(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect('/login');
    }
}
