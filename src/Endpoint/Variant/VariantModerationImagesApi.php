<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantModerationImagesRequest;

final class VariantModerationImagesApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(VariantModerationImagesRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'GET',
            '/open-api/v1/variants/creation/images/moderation',
            query: $request->toQuery(),
        ));
    }
}
