<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\BrandRequest;

final class BrandRequestApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function create(BrandRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'POST',
            '/open-api/v1/product-creation/brand/request',
            $request->toArray(),
        ));
    }
}
