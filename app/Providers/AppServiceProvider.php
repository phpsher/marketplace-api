<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Contracts\Services\StatisticsServiceInterface;
use App\Models\Order;
use App\Policies\OrderPolicy;
use App\Services\AuthService;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductServiceInterface::class, ProductService::class);

        $this->app->bind(CartServiceInterface::class, CartService::class);

        $this->app->bind(AuthServiceInterface::class, AuthService::class);

        $this->app->bind(OrderServiceInterface::class, OrderService::class);

        $this->app->bind(StatisticsServiceInterface::class, StatisticsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);
    }
}
