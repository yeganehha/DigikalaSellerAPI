<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

/** Shared draft product representation for list and detail endpoints. */
final class DraftProductDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $categoryId,
        public readonly ?int $brandId,
        public readonly ?string $titleFa,
        public readonly array $attributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $categoryId = $data['category_id'] ?? null;
        $brandId = $data['brand_id'] ?? null;
        $titleFa = $data['title_fa'] ?? null;
        if (($id !== null && !is_int($id))
            || ($categoryId !== null && !is_int($categoryId))
            || ($brandId !== null && !is_int($brandId))
            || ($titleFa !== null && !is_string($titleFa))) {
            throw new UnexpectedValueException('Draft product response item has an invalid shape.');
        }

        return new self($id, $categoryId, $brandId, $titleFa, $data);
    }
}
