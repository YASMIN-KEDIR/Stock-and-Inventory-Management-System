<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test guest is redirected to login.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated admin can view dashboard.
     */
    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::first() ?? User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Store Summary');
    }

    /**
     * Test products catalog page renders.
     */
    public function test_authenticated_user_can_view_products(): void
    {
        $user = User::first() ?? User::factory()->create(['role' => 'ADMIN']);

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Product Master Catalog');
    }
}
