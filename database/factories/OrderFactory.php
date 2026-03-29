<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id'     => $this->faker->numberBetween(1, 10),
            'total_price' => $this->faker->numberBetween(1, 10),
            'status'      => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }
}
