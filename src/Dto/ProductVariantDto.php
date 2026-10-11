<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

/** Shared variant representation for list, detail, and mutation responses. */
final class ProductVariantDto
{
    /** @param array<string, mixed> $payload */
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $productId,
        public readonly ?int $productVariantId,
        public readonly ?string $title,
        public readonly ?bool $active,
        public readonly array $payload,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $productId = $data['product_id'] ?? null;
        $productVariantId = $data['product_variant_id'] ?? null;
        $title = $data['title'] ?? null;
        $active = $data['active'] ?? null;

        return new self(
            is_int($id) ? $id : null,
            is_int($productId) ? $productId : null,
            is_int($productVariantId) ? $productVariantId : null,
            is_string($title) ? $title : null,
            is_bool($active) ? $active : null,
            $data,
        );
    }
}
