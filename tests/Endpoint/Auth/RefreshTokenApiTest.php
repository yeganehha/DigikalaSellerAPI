<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Endpoint\Auth;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\RefreshTokenData;
use Yeganehha\DigikalaSellerOpenApi\Dto\RefreshTokenRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\RefreshTokenResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;

final class RefreshTokenApiTest extends TestCase
{
    public function testRefreshTokenSendsRequestAndMapsNestedDtoResponse(): void
    {
        $payload = [
            'status' => 'ok',
            'data' => [
                'access_token' => 'new-access-token',
                'refresh_token' => 'new-refresh-token',
                'access_token_expires_at' => [
                    'date' => '2025-02-07 00:00:00.000000',
                    'timezone_type' => 3,
                    'timezone' => 'Asia/Tehran',
                ],
                'refresh_token_expires_at' => [
                    'date' => '2025-03-07 00:00:00.000000',
                    'timezone_type' => 3,
                    'timezone' => 'Asia/Tehran',
                ],
            ],
        ];
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($payload, JSON_THROW_ON_ERROR)),
        ]);
        $history = [];
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $handler]));

        $response = $client->refreshToken()->refresh(
            new RefreshTokenRequest('expired-access-token', 'current-refresh-token'),
        );

        self::assertInstanceOf(RefreshTokenResponse::class, $response);
        self::assertInstanceOf(RefreshTokenData::class, $response->data);
        self::assertSame('new-access-token', $response->data->accessToken);
        self::assertSame('new-refresh-token', $response->data->refreshToken);
        self::assertSame('Asia/Tehran', $response->data->accessTokenExpiresAt->timezone);
        $request = $history[0]['request'];
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/open-api/v1/auth/refresh-token', $request->getUri()->getPath());
        self::assertSame('Bearer expired-access-token', $request->getHeaderLine('Authorization'));
        self::assertSame(
            ['access_token' => 'expired-access-token', 'refresh_token' => 'current-refresh-token'],
            json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR),
        );
    }
}
