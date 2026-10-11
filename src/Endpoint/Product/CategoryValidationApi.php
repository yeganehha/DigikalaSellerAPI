<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\IdRequest;

final class CategoryValidationApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function validate(IdRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'GET',
            '/open-api/v1/product-creation/category/' . rawurlencode((string) $request->id) . '/validation',
        ));
    }
}
