<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;

final class VariantProductImagesApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(int $productId): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'GET', '/open-api/v1/variants/creation/images/' . $productId,
        ));
    }
}
