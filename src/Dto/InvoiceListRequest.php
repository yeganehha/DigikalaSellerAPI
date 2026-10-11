<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class InvoiceListRequest
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $size = 50,
        public readonly string $sort = 'id',
        public readonly string $order = 'desc',
        public readonly ?int $payoutOrderStatus = null,
        public readonly ?string $invoiceStartDate = null,
        public readonly ?string $invoiceEndDate = null,
    ) {
        if ($page < 1 || $size < 1) {
            throw new InvalidArgumentException('Page and size must be positive integers.');
        }
        if ($sort !== 'id') {
            throw new InvalidArgumentException('Invoice list can only be sorted by id.');
        }
        if (!in_array($order, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Order must be asc or desc.');
        }
    }

    /** @return array<string, int|string> */
    public function toQuery(): array
    {
        $query = [
            'page' => $this->page,
            'size' => $this->size,
            'sort' => $this->sort,
            'order' => $this->order,
        ];
        foreach ([
            'search[payout_order_status]' => $this->payoutOrderStatus,
            'search[invoice_start_date]' => $this->invoiceStartDate,
            'search[invoice_end_date]' => $this->invoiceEndDate,
        ] as $name => $value) {
            if ($value !== null) {
                $query[$name] = $value;
            }
        }

        return $query;
    }
}
