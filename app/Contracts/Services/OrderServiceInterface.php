<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTO\StoreOrderDTO;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    public function getOrders(): LengthAwarePaginator;

    public function getOrder(int $orderId): ?Order;

    public function storeOrder(StoreOrderDTO $DTO): Order;
}
