<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

final class BuyBoxPriceSuggestionDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $suggestedPrice,
        public readonly array $attributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $price = $data['suggested_price'] ?? null;
        if ($price !== null && !is_int($price)) {
            throw new UnexpectedValueException('BuyBox suggested_price must be an integer or null.');
        }

        return new self($price, $data);
    }
}
