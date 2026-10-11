<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;

final class ProductVariantB2bPricesDetailsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(int $variantId): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'GET', '/open-api/v1/variants/' . $variantId . '/b2b-prices',
        ));
    }
}
