<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class ProductVariantListRequest
{
    private const SORT_COLUMNS = [
        'id', 'product_variant_id', 'selling_stock', 'selling_price',
        'marketplace_seller_stock', 'warehouse_stock', 'created_at',
    ];

    /**
     * @param array<string, int|string|bool|null> $filters
     */
    public function __construct(
        public readonly int $page = 1,
        public readonly int $size = 50,
        public readonly string $sort = 'id',
        public readonly string $order = 'asc',
        public readonly array $filters = [],
    ) {
        if ($page < 1 || $size < 1) {
            throw new InvalidArgumentException('Page and size must be positive integers.');
        }
        if (!in_array($sort, self::SORT_COLUMNS, true)) {
            throw new InvalidArgumentException('Unsupported variant sort column.');
        }
        if (!in_array($order, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Order must be asc or desc.');
        }

        $allowedFilters = [
            'id', 'ids', 'shipping_type', 'active', 'moderation_status', 'category_ids',
            'buy_box_winner', 'in_competition', 'search_term', 'price_terms', 'out_of_stock',
            'archived', 'selling_channel', 'creation_time_from', 'creation_time_to',
            'seller_product_tags', 'nearby_seller_shipment',
        ];
        foreach ($filters as $name => $value) {
            if (!in_array($name, $allowedFilters, true)) {
                throw new InvalidArgumentException(sprintf('Unsupported variant filter: %s.', $name));
            }
            if ($value !== null && !is_int($value) && !is_string($value) && !is_bool($value)) {
                throw new InvalidArgumentException(sprintf('Invalid value for variant filter: %s.', $name));
            }
        }
    }

    /** @return array<string, int|string|bool> */
    public function toQuery(): array
    {
        $query = [
            'page' => $this->page,
            'size' => $this->size,
            'sort' => $this->sort,
            'order' => $this->order,
        ];
        foreach ($this->filters as $name => $value) {
            if ($value !== null) {
                $query['search[' . $name . ']'] = $value;
            }
        }

        return $query;
    }
}
