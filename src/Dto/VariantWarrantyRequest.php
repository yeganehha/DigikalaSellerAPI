<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantWarrantyRequest
{
    /** @param array<string, mixed> $fields */
    public function __construct(public readonly array $fields)
    {
        foreach (['product_id', 'warranty_title', 'warranty_period'] as $required) {
            if (!array_key_exists($required, $fields)) {
                throw new InvalidArgumentException(sprintf('Missing required warranty field: %s.', $required));
            }
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->fields;
    }
}
