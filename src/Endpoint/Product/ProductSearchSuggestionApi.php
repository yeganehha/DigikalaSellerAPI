<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\KeywordRequest;

final class ProductSearchSuggestionApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function suggest(KeywordRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'GET',
            '/open-api/v1/product-creation/search/suggestion/v2',
            query: $request->toQuery(),
        ));
    }
}
