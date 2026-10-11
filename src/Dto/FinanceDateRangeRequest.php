<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class FinanceDateRangeRequest
{
    public function __construct(
        public readonly string $startDate,
        public readonly string $endDate,
    ) {
        if (trim($startDate) === '' || trim($endDate) === '') {
            throw new InvalidArgumentException('Finance start and end dates are required.');
        }
        if (strtotime($endDate) < strtotime($startDate)) {
            throw new InvalidArgumentException('Finance end date must not precede start date.');
        }
    }

    /** @return array{start_date: string, end_date: string} */
    public function toQuery(): array
    {
        return ['start_date' => $this->startDate, 'end_date' => $this->endDate];
    }
}
