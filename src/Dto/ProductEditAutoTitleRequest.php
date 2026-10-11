<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class ProductEditAutoTitleRequest
{
    /** @param array<string, mixed> $fields */
    public function __construct(
        public readonly int $brandId,
        public readonly array $fields = [],
    ) {
        if ($this->brandId < 1) {
            throw new InvalidArgumentException('Brand ID must be positive.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_merge($this->fields, ['brand_id' => $this->brandId]);
    }
}
