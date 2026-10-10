<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

/** Shared product representation for product search, seller lists and product views. */
final class ProductDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $title,
        public readonly ?string $status,
        public readonly array $attributes = [],
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? $data['product_id'] ?? $data['productId'] ?? null;
        $title = $data['title'] ?? $data['title_fa'] ?? $data['name'] ?? null;
        $status = $data['status'] ?? $data['status_data'] ?? null;

        if (($id !== null && !is_int($id))
            || ($title !== null && !is_string($title))
            || ($status !== null && !is_string($status))) {
            throw new UnexpectedValueException('Product response item has an invalid shape.');
        }

        return new self($id, $title, $status, $data);
    }
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
