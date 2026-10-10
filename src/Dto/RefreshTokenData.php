<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

final class RefreshTokenData
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $refreshToken,
        public readonly TokenExpiry $accessTokenExpiresAt,
        public readonly TokenExpiry $refreshTokenExpiresAt,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        if (!is_string($data['access_token'] ?? null)
            || !is_string($data['refresh_token'] ?? null)
            || !is_array($data['access_token_expires_at'] ?? null)
            || !is_array($data['refresh_token_expires_at'] ?? null)) {
            throw new UnexpectedValueException('Refresh token response data has an invalid shape.');
        }

        return new self(
            $data['access_token'],
            $data['refresh_token'],
            TokenExpiry::fromArray($data['access_token_expires_at']),
            TokenExpiry::fromArray($data['refresh_token_expires_at']),
        );
    }
}
