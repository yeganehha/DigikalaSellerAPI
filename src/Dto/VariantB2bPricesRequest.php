<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantB2bPricesRequest
{
    /** @param list<array{min: int, max: int, selling_price: int}> $b2bPrices */
    public function __construct(public readonly bool $active, public readonly array $b2bPrices)
    {
        if ($b2bPrices === []) {
            throw new InvalidArgumentException('At least one B2B price range is required.');
        }
        foreach ($b2bPrices as $bucket) {
            if (!isset($bucket['min'], $bucket['max'], $bucket['selling_price'])
                || $bucket['min'] < 1 || $bucket['max'] < $bucket['min'] || $bucket['selling_price'] < 0) {
                throw new InvalidArgumentException('Every B2B price range must have valid min, max, and selling_price values.');
            }
        }
    }

    /** @return array{active: bool, b2b_prices: list<array{min: int, max: int, selling_price: int}>} */
    public function toArray(): array
    {
        return ['active' => $this->active, 'b2b_prices' => $this->b2bPrices];
    }
}
