<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class ImageAiRequest
{
    public function __construct(
        public readonly string $imageId,
        public readonly bool $isMain,
    ) {
        if (trim($this->imageId) === '') {
            throw new InvalidArgumentException('Image ID must not be empty.');
        }
    }

    /** @return array{image_id: string, is_main: bool} */
    public function toArray(): array
    {
        return ['image_id' => $this->imageId, 'is_main' => $this->isMain];
    }
}
