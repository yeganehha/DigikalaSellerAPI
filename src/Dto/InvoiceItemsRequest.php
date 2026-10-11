<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class InvoiceItemsRequest
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $size = 50,
        public readonly string $sort = 'id',
        public readonly string $order = 'asc',
        public readonly ?int $invoiceId = null,
        public readonly ?int $financialNotationId = null,
        public readonly ?string $calculationType = null,
    ) {
        if ($page < 1 || $size < 1 || $sort !== 'id' || !in_array($order, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Invalid invoice-items pagination or sort values.');
        }
        if (($invoiceId !== null && $invoiceId < 1)
            || ($financialNotationId !== null && $financialNotationId < 1)) {
            throw new InvalidArgumentException('Invoice item filter IDs must be positive.');
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
            'search[invoice_id]' => $this->invoiceId,
            'search[financial_notation_id]' => $this->financialNotationId,
            'search[calculation_type]' => $this->calculationType,
        ] as $name => $value) {
            if ($value !== null) {
                $query[$name] = $value;
            }
        }

        return $query;
    }
}
