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
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderStatisticsDto;

final class OrderStatisticsApiTest extends TestCase
{
    public function testStatisticsUsesGetPathAndMapsResponseDto(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"all_shipped_by_dk":123,"shipped_by_seller_count":456,"seller_nearby_stores_order_count":7}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $statistics = $client->orderStatistics()->get();

        self::assertInstanceOf(OrderStatisticsDto::class, $statistics);
        self::assertSame(123, $statistics->allShippedByDk);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/orders/statistics', $history[0]['request']->getUri()->getPath());
    }
}
