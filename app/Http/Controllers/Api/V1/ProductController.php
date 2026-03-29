<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\ProductServiceInterface;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private readonly ProductServiceInterface $productService,
    ) {
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getProducts();

        return $this->success(
            data: $products
        );
    }

    public function show(int $productId): JsonResponse
    {
        $product = $this->productService->getProduct($productId);

        return $this->success(
            data: $product
        );
    }
}
