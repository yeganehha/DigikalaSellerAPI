<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class ProductSearchRequest
{
    /** @param list<int>|null $categories @param list<int>|null $brands @param list<int>|null $types @param list<int>|null $colorPalettes @param list<string>|null $statuses */
    public function __construct(
        public readonly string $keyword,
        public readonly ?array $categories = null,
        public readonly ?array $brands = null,
        public readonly ?array $types = null,
        public readonly ?array $colorPalettes = null,
        public readonly ?array $statuses = null,
        public readonly ?bool $fake = null,
    ) {
        if (trim($keyword) === '') {
            throw new InvalidArgumentException('Search keyword must not be empty.');
        }
    }

    /** @return array<string, mixed> */
    public function toQuery(): array
    {
        $query = ['search[keyword]' => $this->keyword];
        foreach ([
            'search[categories]' => $this->categories,
            'search[brands]' => $this->brands,
            'search[types]' => $this->types,
            'search[colorPalettes]' => $this->colorPalettes,
            'search[statuses]' => $this->statuses,
            'search[fake]' => $this->fake,
        ] as $key => $value) {
            if ($value !== null) {
                $query[$key] = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            }
        }
        return $query;
    }
}
