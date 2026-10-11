<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

/** Lossless item DTO for invoice calculation rows. */
final class InvoiceItemDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(public readonly array $attributes)
    {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}
