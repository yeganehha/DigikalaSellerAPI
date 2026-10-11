<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class ProductAssignRequest
{
    public function __construct(
        public readonly int $productId,
        public readonly ?int $forceMarketplaceSellerId = null,
    ) {
        if ($this->productId < 1 || ($this->forceMarketplaceSellerId !== null && $this->forceMarketplaceSellerId < 1)) {
            throw new InvalidArgumentException('Product and seller IDs must be positive.');
        }
    }

    /** @return array{productId: int, force_marketplace_seller_id?: int} */
    public function toQuery(): array
    {
        $query = ['productId' => $this->productId];
        if ($this->forceMarketplaceSellerId !== null) {
            $query['force_marketplace_seller_id'] = $this->forceMarketplaceSellerId;
        }

        return $query;
    }
}
