<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantPageRequest
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
            throw new InvalidArgumentException('Invalid page, size, sort, or order for variant creation list.');
        }
        if (($productId !== null && $productId < 1)
            || ($categoryId !== null && $categoryId < 1)
            || ($brandId !== null && $brandId < 1)) {
            throw new InvalidArgumentException('Variant theme query IDs must be positive.');
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
