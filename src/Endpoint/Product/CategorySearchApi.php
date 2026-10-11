<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\CategoryDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\CategoryKeywordRequest;

final class CategorySearchApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function search(CategoryKeywordRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request(
                'GET',
                '/open-api/v1/product-creation/search/category/v2/' . rawurlencode((string) $request->keyword),
            ),
            [CategoryDto::class, 'fromArray'],
        );
    }
}
