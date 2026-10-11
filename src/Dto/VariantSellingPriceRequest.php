<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantSellingPriceRequest
{
    public function __construct(
        public readonly int $variantId,
        public readonly int $sellingPrice,
        public readonly int $creditIncreasePercentage,
    ) {
        if ($variantId < 1 || $sellingPrice < 0 || $creditIncreasePercentage < 0) {
            throw new InvalidArgumentException('Variant ID must be positive and price values must be non-negative.');
        }
    }

    /** @return array{variant_id: int, selling_price: int, credit_increase_percentage: int} */
    public function toArray(): array
    {
        return [
            'variant_id' => $this->variantId,
            'selling_price' => $this->sellingPrice,
            'credit_increase_percentage' => $this->creditIncreasePercentage,
        ];
    }
}
