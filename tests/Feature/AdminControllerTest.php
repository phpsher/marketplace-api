<?php

declare(strict_types=1);
/*
TODO...
namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::factory()->create([
            'role' => 'admin'
        ])->id;

        $this->admin = User::factory()->create([
            'role_id' => $adminRole
        ]);
    }

    public function test_it_returns_all_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/products');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_it_returns_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->getJson("/api/v1/admin/products/$product->id");

        $response->assertOk()
            ->assertJsonFragment(['id' => $product->id]);
    }

    public function test_it_returns_all_orders()
    {
        Order::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/orders');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_it_returns_user_orders()
    {
        $user = User::factory()->create();
        Order::factory()->count(2)->create(['user_id' => $user->id]);
        Order::factory()->create();

        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/admin/user-orders', [
            'user_id' => $user->id,
        ]);

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_it_returns_single_user_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/admin/orders/$order->id", [
            'user_id' => $user->id,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['id' => $order->id]);
    }

    public function test_it_returns_statistics()
    {
        Order::factory()->count(5)->create();
        Product::factory()->count(4)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/statistics');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['order', 'product']
            ]);
    }
}
 */
