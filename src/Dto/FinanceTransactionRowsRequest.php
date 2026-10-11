<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class FinanceTransactionRowsRequest
{
    /** @param list<string>|string|null $status @param list<int>|list<string>|string|null $notations */
    public function __construct(
        public readonly string|array|null $status = null,
        public readonly string|array|null $notations = null,
        public readonly ?string $search = null,
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
    ) {
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
            'search[notations]' => $this->notations,
            'search[search]' => $this->search,
            'search[start_date]' => $this->startDate,
            'search[end_date]' => $this->endDate,
        ] as $key => $value) {
            if ($value !== null) {
                $query[$key] = is_array($value) ? implode(',', array_map('strval', $value)) : $value;
            }
        }

        return $query;
    }
}
