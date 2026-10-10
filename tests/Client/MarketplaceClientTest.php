<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Client;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class MarketplaceClientTest extends TestCase
{
    public function testNonSuccessfulResponseThrowsApiExceptionWithStatusCode(): void
    {
        $mock = new MockHandler([
            new Response(401, ['Content-Type' => 'application/json'], '{"status":"error"}'),
        ]);
        $client = new MarketplaceClient(httpClient: new Client([
            'handler' => HandlerStack::create($mock),
        ]));

        try {
            $client->healthCheck()->get(new \Yeganehha\DigikalaSellerOpenApi\Dto\HealthCheckRequest());
            self::fail('Expected an API exception for an unauthorized response.');
        } catch (ApiException $exception) {
            self::assertSame(401, $exception->statusCode);
        }
    }

    public function testEmptySuccessfulResponseIsRepresentedAsEmptyPayload(): void
    {
        $mock = new MockHandler([new Response(200, [], '')]);
        $client = new MarketplaceClient(httpClient: new Client([
            'handler' => HandlerStack::create($mock),
        ]));

        $response = $client->healthCheck()->get(new \Yeganehha\DigikalaSellerOpenApi\Dto\HealthCheckRequest());

        self::assertSame([], $response->payload);
    }
}
