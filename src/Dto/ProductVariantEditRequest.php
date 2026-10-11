<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class ProductVariantEditRequest
{
    /** @param array<string, mixed> $fields */
    public function __construct(public readonly array $fields)
    {
        $allowed = [
            'seller_stock', 'maximum_per_order', 'selling_price', 'price_limit',
            'credit_increase_percentage', 'shipping_type', 'lead_time', 'seller_lead_time',
            'activation', 'three_hour_delivery',
        ];
        foreach ($fields as $name => $value) {
            if (!in_array($name, $allowed, true)) {
                throw new InvalidArgumentException(sprintf('Unsupported variant edit field: %s.', $name));
            }
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->fields;
    }
}
