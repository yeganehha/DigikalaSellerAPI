<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

/** Shared invoice representation used by invoice lists and invoice details. */
final class InvoiceDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        public readonly array $attributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;
        if ($id !== null && !is_int($id)) {
            throw new UnexpectedValueException('Invoice id must be an integer or null.');
        }
        if ($startDate !== null && !is_string($startDate)) {
            throw new UnexpectedValueException('Invoice start_date must be a string or null.');
        }
        if ($endDate !== null && !is_string($endDate)) {
            throw new UnexpectedValueException('Invoice end_date must be a string or null.');
        }

        return new self($id, $startDate, $endDate, $data);
    }
}
