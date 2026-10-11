<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Endpoint\Order;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderHistoryRequest;

final class OrderHistoryApiTest extends TestCase
{
    public function testHistoryUsesSameOrderDtoAndDocumentedFilters(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"pager":{"page":1,"total_rows":1},"meta_data":{"b2b_active":true},"items":[{"product_variant_title":"Laptop","product_id":123,"product_variant_id":5443121,"order_id":112736712,"quantity":5,"total_price":30000000000}]}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->orderHistory()->list(new OrderHistoryRequest(
            orderType: 'processed',
            categoryId: 123,
            shippingType: 'digikala',
            b2bActive: true,
        ));

        self::assertInstanceOf(OrderDto::class, $response->data['items'][0]);
        self::assertSame(5443121, $response->data['items'][0]->productVariantId);
        self::assertSame(true, $response->data['meta_data']['b2b_active']);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/orders/history', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('processed', $query['order_type']);
        self::assertSame('123', $query['category_id']);
        self::assertSame('digikala', $query['shipping_type']);
        self::assertSame('true', $query['b2b_active']);
    }
}
