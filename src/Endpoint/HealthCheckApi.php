<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\HealthCheckRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\HealthCheckResponse;

final class HealthCheckApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(HealthCheckRequest $request): HealthCheckResponse
    {
        $payload = $this->client->request(
            'GET',
            '/open-api/v1/',
            contentType: $request->contentType,
        );

        return HealthCheckResponse::fromArray($payload);
    }
}
