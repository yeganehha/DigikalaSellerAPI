<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantListRequest;

final class ProductVariantsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(ProductVariantListRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request('GET', '/open-api/v1/variants', query: $request->toQuery()),
            [ProductVariantDto::class, 'fromArray'],
        );
    }
}
