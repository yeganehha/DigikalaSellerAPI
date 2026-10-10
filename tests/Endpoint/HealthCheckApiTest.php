<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Endpoint;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\HealthCheckRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\HealthCheckResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class HealthCheckApiTest extends TestCase
{
    public function testHealthCheckReturnsTypedResponseAndUsesDocumentedRequest(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"service":"marketplace"}}'),
        ]);
        $handler = HandlerStack::create($mock);
        $history = [];
        $handler->push(Middleware::history($history));
        $httpClient = new Client(['handler' => $handler]);
        $client = new MarketplaceClient(httpClient: $httpClient);

        $response = $client->healthCheck()->get(new HealthCheckRequest());

        self::assertInstanceOf(HealthCheckResponse::class, $response);
        self::assertSame('ok', $response->payload['status']);
        $request = $history[0]['request'];
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/open-api/v1/', $request->getUri()->getPath());
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
    }
}
