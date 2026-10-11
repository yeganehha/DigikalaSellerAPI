<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class OrderHistoryRequest
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $size = 50,
        public readonly string $sort = 'id',
        public readonly string $order = 'asc',
        public readonly ?string $orderType = null,
        public readonly ?int $categoryId = null,
        public readonly ?string $orderCreatedAtTo = null,
        public readonly ?string $orderCreatedAtFrom = null,
        public readonly ?string $exitFromWarehouseDateTo = null,
        public readonly ?string $exitFromWarehouseDateFrom = null,
        public readonly ?string $returnedToWarehouseDateTo = null,
        public readonly ?string $returnedToWarehouseDateFrom = null,
        public readonly ?string $shippingType = null,
        public readonly string|int|null $searchTextAll = null,
        public readonly ?bool $b2bActive = null,
    ) {
        if ($page < 1 || $size < 1) {
            throw new InvalidArgumentException('Page and size must be positive integers.');
        }
        if ($sort !== 'id') {
            throw new InvalidArgumentException('Unsupported order-history sort column.');
        }
        if ($order === '') {
            throw new InvalidArgumentException('Order direction must not be empty.');
        }
        if ($orderType !== null && !in_array($orderType, ['processed', 'returned', 'canceled'], true)) {
            throw new InvalidArgumentException('Unsupported order history type.');
        }
        if ($shippingType !== null && !in_array($shippingType, ['seller', 'digikala', 'nearby'], true)) {
            throw new InvalidArgumentException('Unsupported order-history shipping type.');
        }
        if ($categoryId !== null && $categoryId < 1) {
            throw new InvalidArgumentException('Category ID must be positive.');
        }
    }

    /** @return array<string, bool|int|string> */
    public function toQuery(): array
    {
        $query = [
            'page' => $this->page,
            'size' => $this->size,
            'sort' => $this->sort,
            'order' => $this->order,
        ];
        $optional = [
            'order_type' => $this->orderType,
            'category_id' => $this->categoryId,
            'order_created_at_to' => $this->orderCreatedAtTo,
            'order_created_at_from' => $this->orderCreatedAtFrom,
            'exit_from_warehouse_date_to' => $this->exitFromWarehouseDateTo,
            'exit_from_warehouse_date_from' => $this->exitFromWarehouseDateFrom,
            'returned_to_warehouse_date_to' => $this->returnedToWarehouseDateTo,
            'returned_to_warehouse_date_from' => $this->returnedToWarehouseDateFrom,
            'shipping_type' => $this->shippingType,
            'search_text_all' => $this->searchTextAll,
            'b2b_active' => $this->b2bActive === null ? null : ($this->b2bActive ? 'true' : 'false'),
        ];

        foreach ($optional as $name => $value) {
            if ($value !== null) {
                $query[$name] = $value;
            }
        }

        return $query;
    }
}
