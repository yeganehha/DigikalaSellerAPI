<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\BrandSearchRequest;

final class BrandsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function search(BrandSearchRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'GET',
            '/open-api/v1/product-creation/brand',
            query: $request->toQuery(),
        ));
    }
}
