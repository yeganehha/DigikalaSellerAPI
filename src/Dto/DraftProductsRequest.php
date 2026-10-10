<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class DraftProductsRequest
{
    /** @param list<int>|null $brandIds @param list<int>|null $categoryIds */
    public function __construct(
        public readonly ?array $brandIds = null,
        public readonly ?array $categoryIds = null,
        public readonly ?bool $nearExpiration = null,
    ) {
        foreach ([$this->brandIds, $this->categoryIds] as $ids) {
            if ($ids !== null && array_filter($ids, static fn (int $id): bool => $id < 1) !== []) {
                throw new InvalidArgumentException('Filter IDs must be positive integers.');
            }
        }
    }

    /** @return array<string, list<int>|string> */
    public function toQuery(): array
    {
        $query = [];
        if ($this->brandIds !== null) {
            $query['brandId'] = $this->brandIds;
        }
        if ($this->categoryIds !== null) {
            $query['categoryId'] = $this->categoryIds;
        }
        if ($this->nearExpiration !== null) {
            $query['nearExpiration'] = $this->nearExpiration ? 'true' : 'false';
        }
        return $query;
    }
}
