<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Order;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderStatisticsDto;

final class OrderStatisticsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(): OrderStatisticsDto
    {
        $payload = $this->client->request('GET', '/open-api/v1/orders/statistics');
        $data = $payload['data'] ?? null;
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Order statistics response data must be an object.');
        }

        return OrderStatisticsDto::fromArray($data);
    }
}
