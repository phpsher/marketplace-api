<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\DTO\StoreOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private readonly CartServiceInterface  $cartService,
        private readonly OrderServiceInterface $orderService,
    ) {
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderService->getOrders();

        return $this->success(
            data: $orders
        );
    }

    public function show(int $orderId): JsonResponse
    {
        $order = $this->orderService->getOrder($orderId);

        if (Gate::denies('show', $order)) {
            return $this->error(
                message: 'You do not have permission to view this order.',
                statusCode: Response::HTTP_FORBIDDEN
            );
        }

        return $this->success(
            data: $order
        );
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->storeOrder(
            new StoreOrderDTO(
                userId: Auth::id(),
                products: $request->input('products', []),
            )
        );

        return $this->success(
            message: 'Order created',
            data: [
                'order' => $order,
            ]
        );
    }
}
