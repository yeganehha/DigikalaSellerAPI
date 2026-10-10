<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductDetailsRequest;

final class ProductDetailsValidationApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function validate(ProductDetailsRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'POST',
            '/open-api/v1/product-creation/product/detail/validation',
            $request->toArray(),
        ));
    }
}
