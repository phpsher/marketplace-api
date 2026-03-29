<?php

declare(strict_types=1);

namespace App\DTO;

class StoreOrderDTO
{
    /**
     * @param int $userId
     * @param array|null $products
     * @param string|null $totalPrice
     */
    public function __construct(
        public int  $userId,
        public ?array  $products = null,
        public ?float $totalPrice = null,
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \array_filter([
            'user_id'     => $this->userId,
            'products'    => $this->products,
            'total_price' => $this->totalPrice,
        ], fn ($value) => $value !== null);
    }
}
