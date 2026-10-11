<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class FinanceSellerInvoiceDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        public readonly ?int $finalAmount,
        public readonly array $attributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) && is_int($data['id']) ? $data['id'] : null,
            isset($data['start_date']) && is_string($data['start_date']) ? $data['start_date'] : null,
            isset($data['end_date']) && is_string($data['end_date']) ? $data['end_date'] : null,
            isset($data['final_amount']) && is_int($data['final_amount']) ? $data['final_amount'] : null,
            $data,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
