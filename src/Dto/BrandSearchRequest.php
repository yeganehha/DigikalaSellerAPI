<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class BrandSearchRequest
{
    public function __construct(
        public readonly ?string $titleFa = null,
        public readonly ?string $titleEn = null,
    ) {
        foreach ([$this->titleFa, $this->titleEn] as $title) {
            if ($title !== null && trim($title) === '') {
                throw new InvalidArgumentException('Brand search terms must not be empty.');
            }
        }
    }

    /** @return array<string, string> */
    public function toQuery(): array
    {
        $query = [];
        if ($this->titleFa !== null) {
            $query['search[title_fa]'] = $this->titleFa;
        }
        if ($this->titleEn !== null) {
            $query['search[title_en]'] = $this->titleEn;
        }
        return $query;
    }
}
