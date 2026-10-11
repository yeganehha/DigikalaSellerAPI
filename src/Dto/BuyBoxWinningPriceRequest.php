<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class BuyBoxWinningPriceRequest
{
    public function __construct(public readonly int $productVariantId)
    {
        if ($productVariantId < 1) {
            throw new InvalidArgumentException('Product variant ID must be positive.');
        }
    }

    /** @return array{product_variant_id: int} */
    public function toQuery(): array
    {
        return ['product_variant_id' => $this->productVariantId];
    }
}
