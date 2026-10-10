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
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductUpdateRequest;

final class ProductEditApiTest extends TestCase
{
    public function testUpdateUsesPatchAndProductIdQuery(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{"status":"ok"}') ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->productEdit()->update(new ProductUpdateRequest(321, ['title' => 'Updated']));

        self::assertSame('ok', $response->status);
        self::assertSame('PUT', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/product-edit/321', $history[0]['request']->getUri()->getPath());
        self::assertSame('{"title":"Updated","productId":321}', (string) $history[0]['request']->getBody());
    }

    public function testDetailMapsProductDto(): void
    {
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"status":"ok","data":{"product_data":{"id":321,"title":"Product"}}}'),
        ]));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $product = $client->productEdit()->get(new \Yeganehha\DigikalaSellerOpenApi\Dto\IdRequest(321));

        self::assertInstanceOf(ProductDto::class, $product);
        self::assertSame(321, $product->id);
    }
}
