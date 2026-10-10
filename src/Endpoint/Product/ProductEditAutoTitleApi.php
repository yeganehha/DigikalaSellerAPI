<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductEditAutoTitleRequest;

final class ProductEditAutoTitleApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }
    public function suggest(int $categoryId, ProductEditAutoTitleRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'POST',
            '/open-api/v1/product-edit/' . $categoryId . '/auto-title',
            $request->toArray(),
        ));
    }
}
