<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantStockRequest
{
    public function __construct(public readonly int $sellerStock)
    {
        if ($sellerStock < 0) {
            throw new InvalidArgumentException('Seller stock cannot be negative.');
        }
    }

    /** @return array{seller_stock: int} */
    public function toArray(): array
    {
        return ['seller_stock' => $this->sellerStock];
    }
}
