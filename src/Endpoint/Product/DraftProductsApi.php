<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\DraftProductsRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\DraftProductDto;

final class DraftProductsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(DraftProductsRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request(
                'GET',
                '/open-api/v1/draft-products/seller',
                query: $request->toQuery(),
            ),
            [DraftProductDto::class, 'fromArray'],
        );
    }

    public function delete(int $draftProductId): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'DELETE',
            '/open-api/v1/draft-products/' . $draftProductId,
        ));
    }
}
