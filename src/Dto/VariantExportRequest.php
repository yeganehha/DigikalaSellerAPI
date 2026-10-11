<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

/** Filters for POST /open-api/v1/variants/export. */
final class VariantExportRequest
{
    /** @param array<string, int|string|bool> $filters */
    public function __construct(public readonly array $filters = [])
    {
        $allowed = [
            'id', 'shipping_type', 'active', 'moderation_status', 'category_ids',
            'buy_box_winner', 'in_competition', 'search_term', 'price_terms',
            'out_of_stock', 'archived', 'selling_channel',
        ];
        foreach ($filters as $name => $value) {
            if (!in_array($name, $allowed, true)) {
                throw new \InvalidArgumentException(sprintf('Unsupported export filter: %s.', $name));
            }
            if (!is_int($value) && !is_string($value) && !is_bool($value)) {
                throw new \InvalidArgumentException(sprintf('Invalid export filter: %s.', $name));
            }
        }
    }

    /** @return array<string, int|string|bool> */
    public function toArray(): array
    {
        return $this->filters;
    }
}
