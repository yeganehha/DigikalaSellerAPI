<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class InvoiceVatRequest
{
    public function __construct(
        public readonly int $productId,
        public readonly string $date,
        public readonly int $price,
        public readonly bool $isVatIncludedInPrice,
    ) {
        if ($productId < 1 || $price < 0) {
            throw new InvalidArgumentException('Product ID must be positive and price must not be negative.');
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) !== 1) {
            throw new InvalidArgumentException('Date must use YYYY-MM-DD format.');
        }
    }

    /** @return array{product_id: int, date: string, price: int, is_vat_included_in_price: bool} */
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'date' => $this->date,
            'price' => $this->price,
            'is_vat_included_in_price' => $this->isVatIncludedInPrice,
        ];
    }
}
