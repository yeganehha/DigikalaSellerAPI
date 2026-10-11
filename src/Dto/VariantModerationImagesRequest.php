<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantModerationImagesRequest
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $size = 50,
        public readonly string $sort = 'id',
        public readonly string $order = 'asc',
        public readonly ?int $productId = null,
        public readonly ?int $colorId = null,
        public readonly ?string $moderationStatus = null,
    ) {
        if ($page < 1 || $size < 1 || $sort !== 'id' || !in_array($order, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Invalid moderation image pagination or sorting.');
        }
        if ($moderationStatus !== null && !in_array($moderationStatus, ['pending', 'reject', 'accept'], true)) {
            throw new InvalidArgumentException('Unsupported image moderation status.');
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
            'color_id' => $this->colorId,
            'moderation_status' => $this->moderationStatus,
        ], static fn (int|string|null $value): bool => $value !== null);
    }
}
