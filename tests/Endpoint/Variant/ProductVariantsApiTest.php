<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Endpoint\Variant;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantListRequest;

final class ProductVariantsApiTest extends TestCase
{
    public function testListMapsSharedVariantDtoAndKeepsPagerData(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"pager":{"page":1,"total_rows":1},"items":[{"id":123,"product_id":456,"title":"Variant","active":true}]}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->productVariants()->list(new ProductVariantListRequest(
            filters: ['id' => 123, 'shipping_type' => 'digikala', 'active' => true],
        ));

        self::assertInstanceOf(ProductVariantDto::class, $response->data['items'][0]);
        self::assertSame(123, $response->data['items'][0]->id);
        self::assertSame(1, $response->data['pager']['page']);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/variants', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('123', $query['search']['id']);
        self::assertSame('digikala', $query['search']['shipping_type']);
        self::assertSame('1', $query['search']['active']);
    }

    public function testDetailUsesSameDtoAsListItems(): void
    {
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"id":123,"product_id":456,"title":"Variant","active":true}}'),
        ]));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $variant = $client->productVariantDetails()->get(123);
        self::assertInstanceOf(ProductVariantDto::class, $variant);
        self::assertSame(123, $variant->id);
    }
}
