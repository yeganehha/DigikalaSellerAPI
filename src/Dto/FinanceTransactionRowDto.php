<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class FinanceTransactionRowDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $uniqueIdentifier,
        public readonly ?int $notationId,
        public readonly ?int $amount,
        public readonly ?string $direction,
        public readonly array $attributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) && is_int($data['id']) ? $data['id'] : null,
            isset($data['unique_identifier']) && is_int($data['unique_identifier']) ? $data['unique_identifier'] : null,
            isset($data['notation_id']) && is_int($data['notation_id']) ? $data['notation_id'] : null,
            isset($data['amount']) && is_int($data['amount']) ? $data['amount'] : null,
            isset($data['direction']) && is_string($data['direction']) ? $data['direction'] : null,
            $data,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
