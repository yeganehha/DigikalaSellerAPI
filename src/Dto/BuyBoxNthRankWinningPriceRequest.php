<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class BuyBoxNthRankWinningPriceRequest
{
    public function __construct(
        public readonly int $productVariantId,
        public readonly int $targetRank,
    ) {
        if ($productVariantId < 1 || $targetRank < 1) {
            throw new InvalidArgumentException('Product variant ID and target rank must be positive.');
        }
    }

    /** @return array{product_variant_id: int, target_rank: int} */
    public function toQuery(): array
    {
        return [
            'product_variant_id' => $this->productVariantId,
            'target_rank' => $this->targetRank,
        ];
    }
}
