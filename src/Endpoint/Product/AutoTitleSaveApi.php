<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;

use Yeganehha\DigikalaSellerOpenApi\Dto\AutoTitleSaveRequest;

final class AutoTitleSaveApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function save(AutoTitleSaveRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'POST', '/open-api/v1/product-creation/auto-title/save', $request->toArray(),
        ));
    }
}
