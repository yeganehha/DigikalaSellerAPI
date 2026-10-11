<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantWarrantyRequest;

final class VariantWarrantyRequestApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function request(VariantWarrantyRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'POST',
            '/open-api/v1/variants/creation/warranty/request',
            $request->toArray(),
        ));
    }
}
