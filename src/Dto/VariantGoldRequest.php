<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantGoldRequest
{
    public function __construct(
        public readonly float $goldWage,
        public readonly float $goldProfit,
        public readonly float $noneGoldWage,
        public readonly float $noneGoldCost,
        public readonly int $orderLimit,
    ) {
        if ($goldWage < 0 || $goldProfit < 0 || $noneGoldWage < 0 || $noneGoldCost < 0 || $orderLimit < 1) {
            throw new InvalidArgumentException('Gold amounts must be non-negative and order limit must be positive.');
        }
    }

    /** @return array{gold_wage: float, gold_profit: float, none_gold_wage: float, none_gold_cost: float, order_limit: int} */
    public function toArray(): array
    {
        return [
            'gold_wage' => $this->goldWage,
            'gold_profit' => $this->goldProfit,
            'none_gold_wage' => $this->noneGoldWage,
            'none_gold_cost' => $this->noneGoldCost,
            'order_limit' => $this->orderLimit,
        ];
    }
}
