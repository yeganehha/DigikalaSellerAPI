<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderHistoryRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderListRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderDto;
use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;

$accessToken = getenv('DIGIKALA_ACCESS_TOKEN');
if (!is_string($accessToken) || $accessToken === '') {
    fwrite(STDERR, "Set DIGIKALA_ACCESS_TOKEN in the environment.\n");
    exit(1);
}

$client = new MarketplaceClient(accessToken: $accessToken);

try {
    $activeOrders = $client->orders()->list(new OrderListRequest(page: 1, size: 50));
    $orderHistory = $client->orderHistory()->list(new OrderHistoryRequest(page: 1, size: 50));

    foreach ([
        'Active orders' => $activeOrders,
        'Order history' => $orderHistory,
    ] as $section => $response) {
        echo $section . PHP_EOL;
        $items = is_array($response->data) && is_array($response->data['items'] ?? null)
            ? $response->data['items']
            : [];

        foreach ($items as $order) {
            if (!$order instanceof OrderDto) {
                continue;
            }

            echo sprintf(
                "order=%s\tvariant=%s\tquantity=%s\ttotal=%s\n",
                $order->orderId ?? 'n/a',
                $order->productVariantId ?? 'n/a',
                $order->quantity ?? 'n/a',
                $order->totalPrice ?? 'n/a',
            );
        }
    }
} catch (ApiException $exception) {
    fwrite(
        STDERR,
        'Marketplace API request failed (HTTP ' . ($exception->statusCode ?? 'no response') . ').' . PHP_EOL,
    );
    exit(1);
}
