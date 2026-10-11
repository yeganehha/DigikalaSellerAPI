<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantPriceCalculatorRequest;

final class ProductVariantPriceCalculatorApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function calculate(VariantPriceCalculatorRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'GET',
            '/open-api/v1/variants/' . $request->variantId . '/price-calculator',
            query: $request->toQuery(),
        ));
    }
}
