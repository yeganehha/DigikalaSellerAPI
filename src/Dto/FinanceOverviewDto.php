<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

final class FinanceOverviewDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly array $attributes,
        public readonly mixed $period,
        public readonly mixed $sales,
        public readonly mixed $salesReturns,
        public readonly mixed $chart,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        foreach (['period', 'sales', 'sales_returns', 'chart'] as $field) {
            if (isset($data[$field]) && !is_array($data[$field])) {
                throw new UnexpectedValueException(sprintf('Finance overview field %s has an invalid type.', $field));
            }
        }

        return new self($data, $data['period'] ?? null, $data['sales'] ?? null, $data['sales_returns'] ?? null, $data['chart'] ?? null);
    }
}
