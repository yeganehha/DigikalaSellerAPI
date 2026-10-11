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
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderCancellationRequest;

final class OrderCancellationApiTest extends TestCase
{
    public function testCancellationUsesDeletePathAndDocumentedBody(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"message":"order was cancelled successfully","status":"ok","code":200}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $client->orderCancellation()->cancel(123, new OrderCancellationRequest(30, 2));

        self::assertSame('DELETE', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/orders/123', $history[0]['request']->getUri()->getPath());
        self::assertSame(
            ['cancellation_reason_id' => 30, 'count' => 2],
            json_decode((string) $history[0]['request']->getBody(), true),
        );
    }
}
