<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantThemeValuesRequest
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $size = 50,
        public readonly string $sort = 'id',
        public readonly string $order = 'asc',
        public readonly ?int $productId = null,
        public readonly ?int $categoryId = null,
        public readonly ?int $brandId = null,
    ) {
        if ($page < 1 || $size < 1 || $sort !== 'id' || !in_array($order, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Invalid theme values pagination or sorting.');
        }
        if ($productId === null && $categoryId === null) {
            throw new InvalidArgumentException('Either productId or categoryId is required.');
        }
    }

    /** @return array<string, int|string> */
    public function toQuery(): array
    {
        return array_filter([
            'page' => $this->page,
            'size' => $this->size,
            'sort' => $this->sort,
            'order' => $this->order,
            'product_id' => $this->productId,
            'category_id' => $this->categoryId,
            'brand_id' => $this->brandId,
        ], static fn (int|string|null $value): bool => $value !== null);
    }
}
