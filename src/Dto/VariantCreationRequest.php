<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantCreationRequest
{
    /** @param array<string, mixed> $data */
    public function __construct(public readonly array $data)
    {
        foreach (['is_active', 'order_limit', 'marketplace_seller_stock', 'warranty_id', 'site', 'shipping_type'] as $field) {
            if (!array_key_exists($field, $data)) {
                throw new InvalidArgumentException(sprintf('Missing required field: %s.', $field));
            }
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->data;
    }
}
