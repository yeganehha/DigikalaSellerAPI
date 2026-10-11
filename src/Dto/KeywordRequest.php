<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class KeywordRequest
{
    public function __construct(public readonly ?string $keyword = null)
    {
        if ($keyword !== null && trim($keyword) === '') {
            throw new InvalidArgumentException('Keyword must be null or non-empty.');
        }
    }

    /** @return array<string, string> */
    public function toQuery(): array
    {
        return $this->keyword === null ? [] : ['keyword' => $this->keyword];
    }
}
