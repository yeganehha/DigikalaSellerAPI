<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantB2bPricesRequest;

final class ProductVariantB2bPricesUpdateApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function update(int $variantId, VariantB2bPricesRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'PUT',
            '/open-api/v1/variants/' . $variantId . '/b2b-prices',
            $request->toArray(),
        ));
    }
}
