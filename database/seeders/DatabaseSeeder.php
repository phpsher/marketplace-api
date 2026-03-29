<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::factory()->create(['role' => 'user']);
        Role::factory()->create(['role' => 'admin']);
        User::factory()->count(20)->create();
        Product::factory()->count(10)->create();
        Order::factory()->count(10)->create();
    }
}
