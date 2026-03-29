<?php

declare(strict_types=1);

namespace App\DTO;

class AddProductToCartDTO
{
    /**
     * @param int $productId
     * @param int $quantity
     * @param string $cartKey
     */
    public function __construct(
        public int $productId,
        public int $quantity,
        public string $cartKey,
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'productId' => $this->productId,
            'quantity'  => $this->quantity,
            'cartKey'   => $this->cartKey,
        ];
    }
}
