<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class FinanceTransactionDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $uniqueIdentifier,
        public readonly ?int $amount,
        public readonly ?string $aggregationType,
        public readonly ?string $transactionTime,
        public readonly array $attributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) && is_int($data['id']) ? $data['id'] : null,
            isset($data['unique_identifier']) && is_int($data['unique_identifier']) ? $data['unique_identifier'] : null,
            isset($data['amount']) && is_int($data['amount']) ? $data['amount'] : null,
            isset($data['aggregation_type']) && is_string($data['aggregation_type']) ? $data['aggregation_type'] : null,
            isset($data['transaction_time']) && is_string($data['transaction_time']) ? $data['transaction_time'] : null,
            $data,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
