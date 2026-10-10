<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;

final class DraftProductCountApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'GET',
            '/open-api/v1/product-creation/draft-product/count',
        ));
    }
}
