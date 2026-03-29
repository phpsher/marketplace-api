<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\ProductServiceInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

readonly class ProductService implements ProductServiceInterface
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
    public function getProducts(): LengthAwarePaginator
    {
        return Cache::remember('products:all', $this->ttl, function () {
            return Product::paginate(10);
        });
    }

    /**
     * @param int $id
     * @return Product|null
     */
    public function getProduct(int $id): ?Product
    {
        return Cache::remember("product:$id", $this->ttl, function () use ($id) {
            return Product::findOrFail($id);
        });
    }

    /**
     * @param array $ids
     * @return Collection<int, Product>
     */
    public function getProductsByIds(array $ids): Collection
    {
        $cacheKey = 'products:by:ids:' . \implode(':', $ids);

        return Cache::remember($cacheKey, $this->ttl, function () use ($ids) {
            return Product::whereIn('id', $ids)->get()->keyBy('id');
        });
    }

    /**
     * @param array $productData
     * @return Product
     */
    public function storeProduct(array $productData): Product
    {
        if (isset($productData['image'])) {
            $relativePath              = $productData['image']->store('public/products');
            $productData['image_path'] = $relativePath;
            $productData['image']      = Storage::url($relativePath);
        }

        $product = Product::create($productData);

        Cache::forget('products:all');

        return $product;
    }
}
