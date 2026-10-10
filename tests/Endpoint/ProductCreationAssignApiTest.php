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
use Yeganehha\DigikalaSellerOpenApi\Dto\IdRequest;

final class ProductCreationAssignApiTest extends TestCase
{
    public function testCreationAssignUsesPostAndProductIdBody(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"status":"ok"}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $client->productCreationAssign()->assign(new IdRequest(23));

        self::assertSame('POST', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/product-creation/assign', $history[0]['request']->getUri()->getPath());
        self::assertSame('{"productId":23}', (string) $history[0]['request']->getBody());
    }
}
