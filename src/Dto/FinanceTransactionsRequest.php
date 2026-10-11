<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class FinanceTransactionsRequest
{
    /** @param list<string>|string|null $status @param list<string>|string|null $aggregationType */
    public function __construct(
        public readonly string|array|null $status = null,
        public readonly string|array|null $aggregationType = null,
        public readonly ?string $search = null,
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
    ) {
        foreach (['status' => $status, 'aggregation type' => $aggregationType] as $name => $value) {
            if (is_array($value) && array_filter($value, 'is_string') !== $value) {
                throw new InvalidArgumentException(sprintf('Finance %s filters must contain strings only.', $name));
            }
        }
        if ($startDate !== null && $endDate !== null && strtotime($endDate) < strtotime($startDate)) {
            throw new InvalidArgumentException('Finance end date must not precede start date.');
        }
    }

    /** @return array<string, string> */
    public function toQuery(): array
    {
        $query = [];
        foreach ([
            'search[status]' => $this->status,
            'search[aggregation_type]' => $this->aggregationType,
            'search[search]' => $this->search,
            'search[start_date]' => $this->startDate,
            'search[end_date]' => $this->endDate,
        ] as $key => $value) {
            if ($value !== null) {
                $query[$key] = is_array($value) ? implode(',', $value) : $value;
            }
        }

        return $query;
    }
}
