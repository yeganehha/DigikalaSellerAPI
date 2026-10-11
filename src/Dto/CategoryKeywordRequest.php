<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class CategoryKeywordRequest
{
    public function __construct(public readonly string|int $keyword)
    {
        if (is_string($this->keyword) && trim($this->keyword) === '') {
            throw new InvalidArgumentException('Keyword must not be empty.');
        }
        if (is_int($this->keyword) && $this->keyword < 1) {
            throw new InvalidArgumentException('Category ID must be positive.');
        }
    }
}
