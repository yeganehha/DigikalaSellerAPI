<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantColorRequest
{
    public function __construct(
        public readonly int $productId,
        public readonly string $title,
        public readonly string $productImageId,
        public readonly ?string $hexCode = null,
        public readonly ?int $themeId = null,
    ) {
        if ($productId < 1 || trim($title) === '' || trim($productImageId) === '') {
            throw new InvalidArgumentException('Product ID, title, and product image ID are required.');
        }
        if ($hexCode !== null && preg_match('/^#[0-9A-Fa-f]{6}$/', $hexCode) !== 1) {
            throw new InvalidArgumentException('Color hex code must use the #RRGGBB format.');
        }
        if ($themeId !== null && $themeId < 1) {
            throw new InvalidArgumentException('Theme ID must be positive.');
        }
    }

    /** @return array<string, int|string> */
    public function toArray(): array
    {
        $data = [
            'product_id' => $this->productId,
            'title' => $this->title,
            'product_image_id' => $this->productImageId,
        ];
        if ($this->hexCode !== null) {
            $data['hex_code'] = $this->hexCode;
        }
        if ($this->themeId !== null) {
            $data['theme_id'] = $this->themeId;
        }

        return $data;
    }
}
