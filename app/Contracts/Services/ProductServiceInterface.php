<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function getProducts(): LengthAwarePaginator;

    public function getProduct(int $id): ?Product;

    // TODO... public function storeProduct(array $productData): Product;
}
