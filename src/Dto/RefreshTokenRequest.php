<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class RefreshTokenRequest
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $refreshToken,
    ) {
        if (trim($this->accessToken) === '') {
            throw new InvalidArgumentException('Access token must not be empty.');
        }

        if (trim($this->refreshToken) === '') {
            throw new InvalidArgumentException('Refresh token must not be empty.');
        }
    }

    /** @return array{access_token: string, refresh_token: string} */
    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
        ];
    }
}
