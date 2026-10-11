<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\IdRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductUpdateRequest;

final class ProductEditApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(IdRequest $request): ProductDto
    {
        $payload = $this->client->request(
            'GET', '/open-api/v1/product-edit/' . $request->id,
        );

        return ProductDto::fromArray($payload['data']['product_data'] ?? $payload['data'] ?? $payload);
    }

    public function update(ProductUpdateRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'PUT',
            '/open-api/v1/product-edit/'.$request->productId,
            $request->toArray(),
        ));
    }
}
