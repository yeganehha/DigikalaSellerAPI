<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class ProductCreationSaveRequest
{
    /** @param array<string, mixed> $fields */
    public function __construct(public readonly array $fields = [])
    {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->fields;
    }
}
