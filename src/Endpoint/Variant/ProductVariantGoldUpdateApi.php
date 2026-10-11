<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantGoldRequest;

final class ProductVariantGoldUpdateApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function update(int $variantId, VariantGoldRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'PUT', '/open-api/v1/variants/' . $variantId . '/gold', $request->toArray(),
        ));
    }
}
