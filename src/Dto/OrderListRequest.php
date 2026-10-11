<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class OrderListRequest
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $size = 50,
        public readonly string $sort = 'order_created_at',
        public readonly string $order = 'desc',
        public readonly ?string $searchTerm = null,
        public readonly ?bool $createdToday = null,
    ) {
        if ($page < 1 || $size < 1) {
            throw new InvalidArgumentException('Page and size must be positive integers.');
        }
        if ($sort !== 'order_created_at') {
            throw new InvalidArgumentException('Unsupported active-order sort column.');
        }
        if ($order === '') {
            throw new InvalidArgumentException('Order direction must not be empty.');
        }
    }

    /** @return array<string, int|string> */
    public function toQuery(): array
    {
        $query = [
            'page' => $this->page,
            'size' => $this->size,
            'sort' => $this->sort,
            'order' => $this->order,
        ];

        if ($this->searchTerm !== null) {
            $query['search[search_term]'] = $this->searchTerm;
        }
        if ($this->createdToday !== null) {
            $query['search[created_today]'] = $this->createdToday ? 'true' : 'false';
        }

        return $query;
    }
}
