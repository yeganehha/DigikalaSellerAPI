<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantPriceCalculatorRequest
{
    /**
     * @param array{gold_wage: int|float, gold_profit: int|float, none_gold_wage: int|float, none_gold_cost: int|float} $query
     */
    public function __construct(
        public readonly int $variantId,
        public readonly array $query,
    ) {
        if ($variantId < 1) {
            throw new InvalidArgumentException('Variant ID must be positive.');
        }

        foreach (['gold_wage', 'gold_profit', 'none_gold_wage', 'none_gold_cost'] as $key) {
            if (!isset($query[$key]) || !is_numeric($query[$key]) || (float) $query[$key] < 0) {
                throw new InvalidArgumentException(sprintf('A non-negative %s value is required.', $key));
            }
        }
    }

    /** @return array{gold_wage: int|float, gold_profit: int|float, none_gold_wage: int|float, none_gold_cost: int|float} */
    public function toQuery(): array
    {
        return $this->query;
    }
}
