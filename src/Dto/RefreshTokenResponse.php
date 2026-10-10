<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

final class RefreshTokenResponse
{
    public function __construct(
        public readonly string $status,
        public readonly RefreshTokenData $data,
    ) {
    }

    /** @param array<string, mixed> $payload */
    public static function fromArray(array $payload): self
    {
        if (!is_string($payload['status'] ?? null)
            || !is_array($payload['data'] ?? null)) {
            throw new UnexpectedValueException('Refresh token response has an invalid shape.');
        }

        return new self($payload['status'], RefreshTokenData::fromArray($payload['data']));
    }
}
