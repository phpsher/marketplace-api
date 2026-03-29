<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\OrderServiceInterface;
use App\DTO\StoreOrderDTO;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

readonly final class OrderService implements OrderServiceInterface
{
    /**
     * @var int
     */
    private int $ttl;

    public function __construct()
    {
        $this->ttl = 3600 * 24 * 3;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getOrders(): LengthAwarePaginator
    {
        return Cache::remember('orders:all', $this->ttl * 60, function () {
            return Order::paginate(10);
        });
    }

    /**
     * @param int $orderId
     * @return Order|null
     */
    public function getOrder(int $orderId): ?Order
    {
        return Cache::remember("orders:$orderId", $this->ttl * 60, function () use ($orderId) {
            return Order::with('products')->find($orderId);
        });
    }

    /**
     * @param StoreOrderDTO $DTO
     * @return Order
     */
    public function storeOrder(StoreOrderDTO $DTO): Order
    {
        return DB::transaction(function () use ($DTO) {

            $productsIds = \array_column($DTO->products, 'product_id');
            $products    = Product::whereIn('id', $productsIds)->get()->keyBy('id');

            $totalPrice = 0;
            foreach ($DTO->products as $item) {
                if (isset($products[$item['product_id']])) {
                    $product = $products[$item['product_id']];
                    $totalPrice += $product->price * $item['quantity'];
                }
            }

            $order = Order::create([
                'user_id'     => $DTO->userId,
                'total_price' => $totalPrice,
            ]);

            $attachData = [];
            foreach ($DTO->products as $item) {
                $product                  = $products[$item['product_id']];
                $attachData[$product->id] = [
                    'quantity'    => $item['quantity'],
                    'total_price' => $product->price * $item['quantity'],
                ];
            }
            $order->products()->attach($attachData);

            Cache::put("orders:{$order->id}", $order, $this->ttl * 60);
            Cache::forget('orders:all');

            return $order;
        });
    }
}
