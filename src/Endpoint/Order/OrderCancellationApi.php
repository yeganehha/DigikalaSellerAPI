<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Order;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderCancellationRequest;

final class OrderCancellationApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function cancel(int $orderItemId, OrderCancellationRequest $request): ApiResponse
    {
        if ($orderItemId < 1) {
            throw new \InvalidArgumentException('Order item ID must be positive.');
        }
        return ApiResponse::fromArray($this->client->request(
            'DELETE',
            '/open-api/v1/orders/' . $orderItemId,
            $request->toArray(),
        ));
    }
}
