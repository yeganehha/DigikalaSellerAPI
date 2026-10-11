<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantStockRequest;

final class ProductVariantStockUpdateApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function update(int $variantId, VariantStockRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'PATCH',
            '/open-api/v1/variants/' . $variantId . '/seller-stock',
            $request->toArray(),
        ));
    }
}
