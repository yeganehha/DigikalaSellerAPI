<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class CategoryTreeRequest
{
    public function __construct(public readonly ?int $parentId = null)
    {
    }

    /** @return array<string, int> */
    public function toQuery(): array
    {
        return $this->parentId === null ? [] : ['parent_id' => $this->parentId];
    }
}
