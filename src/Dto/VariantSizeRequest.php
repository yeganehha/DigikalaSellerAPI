<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantSizeRequest
{
    public function __construct(
        public readonly int $productId,
        public readonly string $title,
        public readonly ?int $themeId = null,
    ) {
        if ($productId < 1 || trim($title) === '' || ($themeId !== null && $themeId < 1)) {
            throw new InvalidArgumentException('Product ID, title, or optional theme ID is invalid.');
        }
    }

    /** @return array{product_id: int, title: string, theme_id?: int} */
    public function toArray(): array
    {
        $data = ['product_id' => $this->productId, 'title' => $this->title];
        if ($this->themeId !== null) {
            $data['theme_id'] = $this->themeId;
        }

        return $data;
    }
}
