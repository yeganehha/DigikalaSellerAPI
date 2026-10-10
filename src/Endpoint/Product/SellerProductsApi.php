<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\SellerProductsRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductDto;

final class SellerProductsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(SellerProductsRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request(
                'GET',
                '/open-api/v1/products/seller',
                query: $request->toQuery(),
            ),
            [ProductDto::class, 'fromArray'],
        );
    }
}
