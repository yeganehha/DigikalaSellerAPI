<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductSearchRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductDto;

final class ProductSearchApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function search(ProductSearchRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request(
                'GET',
                '/open-api/v1/product-creation/search/v2',
                query: $request->toQuery(),
            ),
            [ProductDto::class, 'fromArray'],
        );
    }
}
