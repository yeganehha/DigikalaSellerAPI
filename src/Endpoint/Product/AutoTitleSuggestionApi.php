<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\IdRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\KeywordRequest;
final class AutoTitleSuggestionApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }
    public function get(IdRequest $draftProductId, KeywordRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'GET',
            '/open-api/v1/product-creation/draft-product/' . $draftProductId->id . '/title/suggestion',
            query: $request->toQuery(),
        ));
    }
}
