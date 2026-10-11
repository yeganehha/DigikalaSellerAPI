<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class ProductVariantCreateRequest
{
    /** @param array<string, mixed> $fields */
    public function __construct(public readonly array $fields)
    {
        foreach (['is_active', 'order_limit', 'marketplace_seller_stock', 'warranty_id', 'site', 'shipping_type'] as $required) {
            if (!array_key_exists($required, $fields)) {
                throw new InvalidArgumentException(sprintf('Missing required variant field: %s.', $required));
            }
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->fields;
    }
}
