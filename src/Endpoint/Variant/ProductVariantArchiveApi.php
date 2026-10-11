<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantArchiveRequest;

final class ProductVariantArchiveApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function archive(int $variantId, VariantArchiveRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'PUT', '/open-api/v1/variants/' . $variantId . '/archive', $request->toArray(),
        ));
    }
}
