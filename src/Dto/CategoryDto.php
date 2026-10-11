<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

/** Shared category representation used by category tree and product-creation search endpoints. */
final class CategoryDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $title,
        public readonly ?string $text,
        public readonly ?bool $leaf,
        public readonly array $attributes = [],
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $title = $data['title'] ?? null;
        $text = $data['text'] ?? null;
        $leaf = $data['leaf'] ?? null;

        if (($id !== null && !is_int($id))
            || ($title !== null && !is_string($title))
            || ($text !== null && !is_string($text))
            || ($leaf !== null && !is_bool($leaf))) {
            throw new UnexpectedValueException('Category response item has an invalid shape.');
        }

        return new self($id, $title, $text, $leaf, $data);
    }
}
