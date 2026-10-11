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
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantCreationRequest;

final class VariantCreationApiTest extends TestCase
{
    public function testCreateUsesPostAndReturnsSharedVariantDto(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"id":501,"title":"Created variant","active":true}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->variantCreationSave()->create(42, new VariantCreationRequest([
            'is_active' => true,
            'order_limit' => 5,
            'marketplace_seller_stock' => 10,
            'warranty_id' => 272,
            'site' => 'digikala',
            'shipping_type' => 'digikala',
        ]));

        self::assertInstanceOf(ProductVariantDto::class, $response);
        self::assertSame(501, $response->id);
        self::assertSame('POST', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/variants/creation/42', $history[0]['request']->getUri()->getPath());
        self::assertSame(272, json_decode((string) $history[0]['request']->getBody(), true)['warranty_id']);
    }
}
