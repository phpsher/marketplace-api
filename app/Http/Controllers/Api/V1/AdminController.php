<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Contracts\Services\StatisticsServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShowOrderRequest;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private readonly ProductServiceInterface    $productService,
        private readonly OrderServiceInterface      $orderService,
        private readonly StatisticsServiceInterface $statisticsService
    ) {
    }

    public function dashboard(): JsonResponse
    {
        $allProducts = $this->productService->getProducts();
        $allOrders   = $this->orderService->getOrders();

        return $this->success(
            data: [
                'products' => $allProducts,
                'orders'   => $allOrders,
            ]
        );
    }

    public function showProduct(int $productId): JsonResponse
    {
        $product = $this->productService->getProduct($productId);

        return $this->success(
            data: $product
        );
    }

    public function allUserOrders(ShowOrderRequest $request): JsonResponse
    {
        $orders = $this->orderService->getUserOrders($request->user_id);

        return $this->success(
            data: $orders
        );
    }

    public function showOrder(ShowOrderRequest $request, int $orderId): JsonResponse
    {
        $order = $this->orderService->getOrder($request->user_id, $orderId);

        return $this->success(
            data: $order
        );
    }

    public function statistics(): JsonResponse
    {
        return $this->success(
            data: [
                'order'   => $this->statisticsService->getOrderStatistics(),
                'product' => $this->statisticsService->getProductStatistics(),
            ]
        );
    }
}
