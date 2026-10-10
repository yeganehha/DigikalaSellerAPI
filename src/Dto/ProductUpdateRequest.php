<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

/** @param array<string, mixed> $fields */
final class ProductUpdateRequest
{
    public function __construct(
        public readonly int $productId,
        public readonly array $fields = [],
    ) {
        if ($this->productId < 1) {
            throw new \InvalidArgumentException('Product ID must be positive.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_merge($this->fields, ['productId' => $this->productId]);
    }
}
