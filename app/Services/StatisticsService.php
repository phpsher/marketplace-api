<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\StatisticsServiceInterface;
use App\Exceptions\InternalServerErrorException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Throwable;

readonly class StatisticsService implements StatisticsServiceInterface
{
    private int $ttl;

    public function __construct()
    {
        $this->ttl = 3600 * 24 * 3;
    }

    /**
     * @throws InternalServerErrorException
     * @return array{
     *     total: int,
     *     pending: int,
     *     completed: int,
     *     cancelled: int
     * }
     */
    public function getOrderStatistics(): array
    {
        try {
            $orderStatistics = Cache::remember('orders:statistics', $this->ttl, function () {
                return Order::selectRaw(
                    "COUNT(*) as total,
                     SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                     SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                     SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled"
                )->first();
            });

            return [
                'total'     => (int)$orderStatistics->total,
                'pending'   => (int)$orderStatistics->pending,
                'completed' => (int)$orderStatistics->completed,
                'cancelled' => (int)$orderStatistics->cancelled,
            ];
        } catch (Throwable $e) {
            throw new InternalServerErrorException('Failed to get order statistics', 0, $e);
        }
    }

    /**
     * @throws InternalServerErrorException
     * @return array{
     *     total: int
     * }
     */
    public function getProductStatistics(): array
    {
        try {
            $totalProducts = Cache::remember('products:total', $this->ttl, function () {
                return Product::count();
            });

            return [
                'total' => (int)$totalProducts,
            ];
        } catch (Throwable $e) {
            throw new InternalServerErrorException('Failed to get product statistics', 0, $e);
        }
    }
}
