<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantPageRequest;

final class VariantThemeValuesApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(int $themeId, VariantPageRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'GET',
            '/open-api/v1/variants/creation/themes/' . $themeId . '/theme-values',
            query: $request->toQuery(),
        ));
    }
}
