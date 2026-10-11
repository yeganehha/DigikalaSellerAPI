<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantActivationRequest;

final class ProductVariantActivationApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function update(int $variantId, VariantActivationRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'PUT', '/open-api/v1/variants/' . $variantId . '/activation', $request->toArray(),
        ));
    }
}
