<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Order;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderListRequest;

final class OrdersApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(OrderListRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request('GET', '/open-api/v1/orders', query: $request->toQuery()),
            [OrderDto::class, 'fromArray'],
        );
    }
}
