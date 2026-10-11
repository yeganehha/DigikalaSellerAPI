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
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductSearchRequest;

final class ProductSearchApiTest extends TestCase
{
    public function testProductSearchMapsProductsAndUsesDocumentedQuery(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"items":[{"id":12345,"title":"کالای نمونه","status":"marketable"}]}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->productSearch()->search(new ProductSearchRequest('کالا'));

        self::assertInstanceOf(ProductDto::class, $response->data['items'][0]);
        self::assertSame(12345, $response->data['items'][0]->id);
    }
}
