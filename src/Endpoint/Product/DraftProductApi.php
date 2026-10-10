<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\IdRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\DraftProductDto;

final class DraftProductApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(IdRequest $request): DraftProductDto
    {
        $payload = $this->client->request(
            'GET',
            '/open-api/v1/product-creation/draft-product/' . rawurlencode((string) $request->id),
        );

        return DraftProductDto::fromArray($payload['data'] ?? $payload);
    }
}
