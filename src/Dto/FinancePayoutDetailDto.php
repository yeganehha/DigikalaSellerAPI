<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class FinancePayoutDetailDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $payoutId,
        public readonly ?string $status,
        public readonly ?int $amount,
        public readonly ?string $trackingCode,
        public readonly array $attributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            is_int($data['payout_id'] ?? null) ? $data['payout_id'] : null,
            is_string($data['status'] ?? null) ? $data['status'] : null,
            is_int($data['amount'] ?? null) ? $data['amount'] : null,
            is_string($data['tracking_code'] ?? null) ? $data['tracking_code'] : null,
            $data,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
