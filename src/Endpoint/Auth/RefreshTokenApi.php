<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Auth;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\RefreshTokenRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\RefreshTokenResponse;

final class RefreshTokenApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function refresh(RefreshTokenRequest $request): RefreshTokenResponse
    {
        $payload = $this->client->request(
            'POST',
            '/open-api/v1/auth/refresh-token',
            $request->toArray(),
            $request->accessToken,
        );

        return RefreshTokenResponse::fromArray($payload);
    }
}
