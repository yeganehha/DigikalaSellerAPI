<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Endpoint;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductAssignRequest;

final class ProductAssignApiTest extends TestCase
{
    public function testAssignSendsDocumentedPostPathAndQuery(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{"status":"ok"}') ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $client->productAssign()->assign(new ProductAssignRequest(123, 456));

        self::assertSame('POST', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/product-assign', $history[0]['request']->getUri()->getPath());
        self::assertStringContainsString('productId=123', $history[0]['request']->getUri()->getQuery());
        self::assertStringContainsString('force_marketplace_seller_id=456', $history[0]['request']->getUri()->getQuery());
    }
}
