<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class SellerProductsRequest
{
    private const MODERATION_STATUSES = [
        'draft', 'in_review', 'waiting_for_confirm', 'edit_after_approved',
        'approved', 'in_review_after_approved', 'removed', 'duplicate',
    ];

    public function __construct(
        public readonly int $page = 1,
        public readonly int $size = 50,
        public readonly string $sort = 'id',
        public readonly string $order = 'asc',
        public readonly ?string $multiSearch = null,
        public readonly ?int $categoryId = null,
        public readonly ?int $brandId = null,
        public readonly ?string $moderationStatus = null,
        public readonly ?string $fakeStatus = null,
    ) {
        if ($page < 1 || $size < 1 || $sort !== 'id' || !in_array(strtolower($order), ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Invalid seller product pagination or sorting options.');
        }
        if ($categoryId !== null && $categoryId < 1 || $brandId !== null && $brandId < 1) {
            throw new InvalidArgumentException('Category and brand IDs must be positive.');
        }
        if ($moderationStatus !== null && !in_array($moderationStatus, self::MODERATION_STATUSES, true)) {
            throw new InvalidArgumentException('Unsupported moderation status.');
        }
        if ($fakeStatus !== null && !in_array($fakeStatus, ['fake', 'real'], true)) {
            throw new InvalidArgumentException('Fake status must be fake or real.');
        }
    }

    /** @return array<string, int|string> */
    public function toQuery(): array
    {
        $query = [
            'page' => $this->page,
            'size' => $this->size,
            'sort_by' => $this->sort,
            'order_by' => $this->order,
        ];
        foreach ([
            'search[multi_search]' => $this->multiSearch,
            'search[category_id]' => $this->categoryId,
            'search[brand_id]' => $this->brandId,
            'search[moderation_status]' => $this->moderationStatus,
            'search[fake_status]' => $this->fakeStatus,
        ] as $key => $value) {
            if ($value !== null) {
                $query[$key] = $value;
            }
        }
        return $query;
    }
}
