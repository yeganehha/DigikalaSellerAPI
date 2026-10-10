<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Category;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\CategoryTreeRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\CategoryDto;

final class CategoryTreeApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(CategoryTreeRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request(
                'GET',
                '/open-api/v1/categories/tree',
                query: $request->toQuery(),
            ),
            [CategoryDto::class, 'fromArray'],
        );
    }
}
