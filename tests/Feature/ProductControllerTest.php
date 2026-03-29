<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::factory()->create([
            'role' => 'user',
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);
    }

    public function test_can_get_all_products()
    {
        Product::factory()
            ->count(5)
            ->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'price',
                    'image',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_can_get_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/v1/products/$product->id");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'price',
                'image',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_returns_404_if_product_is_not_found()
    {
        $this->getJson('/api/v1/products/999')
            ->assertNotFound();
    }
}
