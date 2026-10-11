<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

final class FinanceTransactionSummaryDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly array $attributes,
        public readonly mixed $total,
        public readonly mixed $pending,
        public readonly mixed $accepted,
        public readonly mixed $released,
        public readonly mixed $paid,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        foreach (['total', 'pending', 'accepted', 'released', 'paid'] as $field) {
            if (isset($data[$field]) && !is_array($data[$field])) {
                throw new UnexpectedValueException(sprintf('Finance summary field %s has an invalid type.', $field));
            }
        }

        return new self($data, $data['total'] ?? null, $data['pending'] ?? null, $data['accepted'] ?? null, $data['released'] ?? null, $data['paid'] ?? null);
    }
}
