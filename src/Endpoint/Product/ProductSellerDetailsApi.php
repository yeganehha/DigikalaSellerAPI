<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\IdRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductDto;

final class ProductSellerDetailsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(IdRequest $request): ProductDto
    {
        $payload = $this->client->request(
            'GET',
            '/open-api/v1/product-creation/be-seller/' . rawurlencode((string) $request->id),
        );

        return ProductDto::fromArray($payload['data'] ?? $payload);
    }
}
