<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantIdsRequest;

final class ProductVariantB2bActivationApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function activate(VariantIdsRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'PUT',
            '/open-api/v1/variants/b2b-activation',
            $request->toArray(),
        ));
    }
}
