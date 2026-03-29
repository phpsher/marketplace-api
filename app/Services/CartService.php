<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\CartServiceInterface;
use App\DTO\AddProductToCartDTO;
use App\DTO\DestroyProductFromCartDTO;
use App\Exceptions\CacheException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

readonly class CartService implements CartServiceInterface
{
    private int $ttl;

    public function __construct()
    {
        $this->ttl = 3600 * 24 * 3;
    }

    /**
     * @return array
     */
    public function getCart(): array
    {
        $cart = Cache::get($this->getCartKey(), []);

        return $this->formatCart($cart);
    }

    /**
     * @param AddProductToCartDTO $DTO
     * @throws CacheException
     * @return array
     */
    public function addProductToCart(AddProductToCartDTO $DTO): array
    {
        $cartKey   = $this->getCartKey();
        $productId = $DTO->productId;
        $quantity  = (int) $DTO->quantity;

        $cart = Cache::get($cartKey, []);

        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        if (!Cache::put($cartKey, $cart, $this->ttl)) {
            throw new CacheException('Failed to store cart in cache');
        }

        return $this->formatCart($cart);
    }

    /**
     * @param DestroyProductFromCartDTO $DTO
     * @throws CacheException
     * @return void
     */
    public function deleteProductFromCart(DestroyProductFromCartDTO $DTO): void
    {
        $cart = Cache::get($DTO->cartKey, []);

        if (isset($cart[$DTO->productId])) {
            unset($cart[$DTO->productId]);
        }

        if (!Cache::put($DTO->cartKey, $cart, $this->ttl)) {
            throw new CacheException('Error deleting product from cart');
        }
    }

    /**
     * @param array $cart
     * @return array
     */
    private function formatCart(array $cart): array
    {
        $formatted = [];
        foreach ($cart as $productId => $quantity) {
            $formatted[] = [
                'product_id' => (int) $productId,
                'quantity'   => (int) $quantity,
            ];
        }

        return $formatted;
    }

    private function getCartKey(): string
    {
        return 'cart:' . Auth::id();
    }
}
