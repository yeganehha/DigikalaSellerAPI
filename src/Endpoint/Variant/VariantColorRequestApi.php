<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantColorRequest;

final class VariantColorRequestApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function request(VariantColorRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'POST',
            '/open-api/v1/variants/creation/color/request',
            $request->toArray(),
        ));
    }
}
