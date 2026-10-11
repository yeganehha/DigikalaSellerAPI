<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantSellingPriceRequest;

final class ProductVariantSellingPriceApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function update(VariantSellingPriceRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'PATCH',
            '/open-api/v1/variants/selling-price',
            $request->toArray(),
        ));
    }
}
