<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;
    private User $user;
    private User $user2;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $role = Role::factory()->create([
            'role' => 'user',
        ]);

        $this->user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->user2 = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->actingAs($this->user);
    }

    public function test_can_user_get_own_orders()
    {
        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'total_price',
                    'created_at',
                    'updated_at',
                    'products' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'price',
                            'image',
                            'created_at',
                            'updated_at',
                            'pivot' => [
                                'order_id',
                                'product_id',
                                'quantity',
                                'total_price',
                                'created_at',
                                'updated_at',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_can_user_get_own_order()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/v1/orders/$order->id");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'user_id',
                'total_price',
                'created_at',
                'updated_at',
                'products' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'price',
                        'image',
                        'created_at',
                        'updated_at',
                        'pivot' => [
                            'order_id',
                            'product_id',
                            'quantity',
                            'total_price',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ]],
        ]);
    }

    public function test_can_user_successful_order_creation()
    {
        $products = Product::factory()->count(2)->create();

        $productIds = [];
        foreach ($products as $product) {
            $productIds[] = $product->id;
        }

        $response = $this->postJson('/api/v1/orders', [
            'products' => [
                ['product_id' => $productIds[0], 'quantity' => 2],
                ['product_id' => $productIds[1], 'quantity' => 3],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'order' => [
                        'user_id',
                        'total_price',
                        'updated_at',
                        'created_at',
                        'id',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_cant_get_not_own_order()
    {
        $this->actingAs($this->user2);

        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/orders/$order->id");

        $response->assertStatus(403);
    }
}
