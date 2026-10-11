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
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderListRequest;

final class OrdersApiTest extends TestCase
{
    public function testActiveOrdersMapSharedOrderDtoAndPreservePagination(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"pager":{"page":1,"total_rows":1},"sort_data":{"sort_column":"order_created_at","sort_order":"desc"},"items":[{"product_variant_id":1234,"order_id":12345,"quantity":1,"selling_price":645800}]}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->orders()->list(new OrderListRequest(
            searchTerm: 'کفش سفید',
            createdToday: true,
        ));

        self::assertInstanceOf(OrderDto::class, $response->data['items'][0]);
        self::assertSame(12345, $response->data['items'][0]->orderId);
        self::assertSame(1, $response->data['pager']['page']);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/orders', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('کفش سفید', $query['search']['search_term']);
        self::assertSame('true', $query['search']['created_today']);
    }
}
