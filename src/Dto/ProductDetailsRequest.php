<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class ProductDetailsRequest
{
    /** @param array<string, mixed> $fields */
    public function __construct(
        public readonly int $categoryId,
        public readonly int $brandId,
        public readonly bool $isIranian,
        public readonly array $fields = [],
    ) {
        if ($this->categoryId < 1 || $this->brandId < 1) {
            throw new InvalidArgumentException('Category and brand IDs must be positive.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_merge($this->fields, [
            'categoryId' => $this->categoryId,
            'brandId' => $this->brandId,
            'isIranian' => $this->isIranian,
        ]);
    }
}
