<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class ProductEditAssignRequest
{
    public function __construct(
        public readonly int $productId,
        public readonly int $otherSellerId,
    ) {
        if ($this->productId < 1 || $this->otherSellerId < 1) {
            throw new InvalidArgumentException('Product and seller IDs must be positive.');
        }
    }

    /** @return array{id: int, other_seller_id: int} */
    public function toArray(): array
    {
        return ['id' => $this->productId, 'other_seller_id' => $this->otherSellerId];
    }
}
