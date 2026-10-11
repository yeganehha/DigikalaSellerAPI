<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

final class OrderStatisticsDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $allShippedByDk,
        public readonly ?int $shippedBySellerCount,
        public readonly ?int $sellerNearbyStoresOrderCount,
        public readonly array $attributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $fields = [
            'all_shipped_by_dk' => $data['all_shipped_by_dk'] ?? null,
            'shipped_by_seller_count' => $data['shipped_by_seller_count'] ?? null,
            'seller_nearby_stores_order_count' => $data['seller_nearby_stores_order_count'] ?? null,
        ];
        foreach ($fields as $name => $value) {
            if ($value !== null && !is_int($value)) {
                throw new UnexpectedValueException(sprintf('Order statistics field %s has an invalid type.', $name));
            }
        }

        return new self($fields['all_shipped_by_dk'], $fields['shipped_by_seller_count'], $fields['seller_nearby_stores_order_count'], $data);
    }
}
