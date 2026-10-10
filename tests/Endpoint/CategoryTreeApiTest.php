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
use Yeganehha\DigikalaSellerOpenApi\Dto\CategoryDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\CategoryTreeRequest;

final class CategoryTreeApiTest extends TestCase
{
    public function testCategoryTreeMapsItemsAndEncodesParentFilter(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"items":[{"id":123,"title":"موبایل"}]}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->categories()->get(new CategoryTreeRequest(42));

        self::assertInstanceOf(CategoryDto::class, $response->data['items'][0]);
        self::assertSame(123, $response->data['items'][0]->id);
        self::assertSame('parent_id=42', $history[0]['request']->getUri()->getQuery());
    }
}
