<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class HealthCheckResponse
{
    /** @param array<string, mixed> $payload */
    public function __construct(public readonly array $payload)
    {
    }

    /** @param array<string, mixed> $payload */
    public static function fromArray(array $payload): self
    {
        return new self($payload);
    }
}
