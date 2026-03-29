<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $role = Role::factory()->create([
            'role' => 'user',
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);
    }

    public function test_can_get_empty_cart()
    {
        $response = $this->getJson('/api/v1/cart');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [],
        ]);
    }

    public function test_can_add_product_to_cart()
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/v1/cart', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Successfully added to cart',
            'data'    => [
                'products' => [
                    [
                        'product_id' => $product->id,
                        'quantity'   => 2,
                    ],
                ],
            ],
        ]);
    }

    public function test_can_add_multiple_products_to_cart()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $this->postJson('/api/v1/cart', [
            'product_id' => $product1->id,
            'quantity'   => 2,
        ]);

        $response = $this->postJson('/api/v1/cart', [
            'product_id' => $product2->id,
            'quantity'   => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.products');
        $response->assertJsonStructure([
            'message',
            'data' => [
                'products' => [
                    '*' => [
                        'product_id',
                        'quantity',
                    ],
                ],
            ],
        ]);
    }

    public function test_can_remove_product_from_cart()
    {
        $product = Product::factory()->create();

        $this->postJson('/api/v1/cart', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $response = $this->deleteJson('/api/v1/cart', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Product removed from cart',
        ]);

        $cartResponse = $this->getJson('/api/v1/cart');
        $cartResponse->assertJson([
            'data' => [],
        ]);
    }
}
