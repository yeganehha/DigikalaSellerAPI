<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class FinanceSellerInvoicesRequest
{
    public function __construct(
        public readonly ?int $month = null,
        public readonly ?int $year = null,
        public readonly ?int $invoiceId = null,
    ) {
        if (($month === null) !== ($year === null)) {
            throw new InvalidArgumentException('Month and year must be provided together.');
        }
        if ($month !== null && ($month < 1 || $month > 12)) {
            throw new InvalidArgumentException('Jalali month must be between 1 and 12.');
        }
        if ($year !== null && $year < 1) {
            throw new InvalidArgumentException('Year must be positive.');
        }
        if ($invoiceId !== null && $invoiceId < 1) {
            throw new InvalidArgumentException('Invoice ID must be positive.');
        }
    }

    /** @return array<string, int> */
    public function toQuery(): array
    {
        $query = [];
        if ($this->month !== null) {
            $query['search[month]'] = $this->month;
            $query['search[year]'] = $this->year;
        }
        if ($this->invoiceId !== null) {
            $query['search[invoice_id]'] = $this->invoiceId;
        }

        return $query;
    }
}
