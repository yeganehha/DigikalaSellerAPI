<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantExportRequest;

final class ProductVariantExportApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function export(VariantExportRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'POST', '/open-api/v1/variants/export', $request->toArray(),
        ));
    }
}
