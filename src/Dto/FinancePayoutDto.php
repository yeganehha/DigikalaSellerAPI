<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class FinancePayoutDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $amount,
        public readonly ?string $status,
        public readonly ?string $trackingCode,
        public readonly ?string $suggestedPayoutAt,
        public readonly ?string $payoutDate,
        public readonly array $attributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            is_int($data['id'] ?? null) ? $data['id'] : null,
            is_int($data['amount'] ?? null) ? $data['amount'] : null,
            is_string($data['status'] ?? null) ? $data['status'] : null,
            is_string($data['tracking_code'] ?? null) ? $data['tracking_code'] : null,
            is_string($data['suggested_payout_at'] ?? null) ? $data['suggested_payout_at'] : null,
            is_string($data['payout_date'] ?? null) ? $data['payout_date'] : null,
            $data,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
