<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\IdRequest;

final class ProductPublishApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function publish(IdRequest $request, ?int $forceMarketplaceSellerId = null): ApiResponse
    {
        $body = $forceMarketplaceSellerId === null
            ? []
            : ['force_marketplace_seller_id' => $forceMarketplaceSellerId];

        return ApiResponse::fromArray($this->client->request(
            'POST', '/open-api/v1/product-edit/' . $request->id . '/publish', $body,
        ));
    }
}
