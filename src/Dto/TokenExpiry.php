<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

final class TokenExpiry
{
    public function __construct(
        public readonly string $date,
        public readonly int $timezoneType,
        public readonly string $timezone,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        if (!is_string($data['date'] ?? null)
            || !is_int($data['timezone_type'] ?? null)
            || !is_string($data['timezone'] ?? null)) {
            throw new UnexpectedValueException('Token expiry response has an invalid shape.');
        }

        return new self($data['date'], $data['timezone_type'], $data['timezone']);
    }
}
