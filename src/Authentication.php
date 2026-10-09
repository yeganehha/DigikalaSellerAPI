<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi;

use Yeganehha\DigikalaSellerOpenApi\Http\ApiResponse;

final readonly class Authentication
{
    public function __construct(private Client $client)
    {
    }

    public function scopes(?string $clientCode = null): ApiResponse
    {
        if ($clientCode === null) {
            return $this->client->request('GET', '/open-api/v1/auth/scopes');
        }
        return $this->client->request('GET', '/open-api/v1/auth/scopes/{client_code}',
            new RequestOptions(path: ['client_code' => $clientCode]));
    }

    public function exchange(#[\SensitiveParameter] string $authorizationCode): ApiResponse
    {
        if ($authorizationCode === '') {
            throw new \InvalidArgumentException('Authorization code must not be empty.');
        }
        return $this->client->request('POST', '/open-api/v1/auth/token',
            new RequestOptions(body: ['authorization_code' => $authorizationCode]));
    }

    public function refresh(
        #[\SensitiveParameter] string $accessToken,
        #[\SensitiveParameter] string $refreshToken,
    ): ApiResponse {
        return $this->client->request('POST', '/open-api/v1/auth/refresh-token',
            new RequestOptions(body: ['access_token' => $accessToken, 'refresh_token' => $refreshToken]));
    }

    public function revoke(): ApiResponse
    {
        return $this->client->request('POST', '/open-api/v1/auth/revoke');
    }
}
