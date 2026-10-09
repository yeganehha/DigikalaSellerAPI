<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests;

use Yeganehha\DigikalaSellerOpenApi\Client;
use Yeganehha\DigikalaSellerOpenApi\Configuration;
use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;
use Yeganehha\DigikalaSellerOpenApi\Exception\TransportException;
use Yeganehha\DigikalaSellerOpenApi\RequestOptions;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    private function client(array $responses, array &$history): Client
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($history));
        return new Client(new Configuration(accessToken: 'test-token'), new HttpClient(['handler' => $stack]));
    }

    public function testNestedSearchAndPaginationAreEncoded(): void
    {
        $history = [];
        $client = $this->client([new Response(200)], $history);
        $client->request('GET', '/open-api/v1/variants', new RequestOptions(query: [
            'page' => 2,
            'search' => ['active' => true, 'search_term' => 'لباس مردانه'],
        ]));
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('2', $query['page']);
        self::assertSame('1', $query['search']['active']);
        self::assertSame('لباس مردانه', $query['search']['search_term']);
    }

    public function testJsonBodyPreservesFalseAndZero(): void
    {
        $history = [];
        $client = $this->client([new Response(200)], $history);
        $client->request('PUT', '/open-api/v1/variants/{variant_id}', new RequestOptions(
            path: ['variant_id' => 123],
            body: ['activation' => false, 'seller_stock' => 0],
        ));
        self::assertSame(['activation' => false, 'seller_stock' => 0], json_decode(
            (string) $history[0]['request']->getBody(), true, flags: JSON_THROW_ON_ERROR,
        ));
    }

    public function testMultipartHasBoundaryAndUploadedContents(): void
    {
        $history = [];
        $client = $this->client([new Response(200)], $history);
        $client->request('POST', '/open-api/v1/product-creation/images/upload', new RequestOptions(
            multipart: [['name' => 'file', 'contents' => 'image-content', 'filename' => 'product.jpg']],
        ));
        $request = $history[0]['request'];
        self::assertStringStartsWith('multipart/form-data; boundary=', $request->getHeaderLine('Content-Type'));
        self::assertStringContainsString('image-content', (string) $request->getBody());
    }

    public function testMissingRequiredBodyFieldFailsBeforeNetwork(): void
    {
        $history = [];
        $client = $this->client([], $history);
        try {
            $client->authentication()->exchange('');
            self::fail('Empty authorization code should fail.');
        } catch (\InvalidArgumentException) {
            self::assertSame([], $history);
        }
        $this->expectException(\InvalidArgumentException::class);
        $client->request('PATCH', '/open-api/v1/variants/{variant_id}/seller-stock', new RequestOptions(
            path: ['variant_id' => 123], body: [],
        ));
    }

    public static function statusCodes(): iterable
    {
        foreach ([400, 401, 403, 404, 429, 500, 503] as $code) {
            yield (string) $code => [$code];
        }
    }

    #[DataProvider('statusCodes')]
    public function testApiErrorsAreStructuredAndNeverRetried(int $code): void
    {
        $history = [];
        $body = json_encode(['status' => 'error', 'errors' => ['seller_stock' => ['Invalid']]], JSON_THROW_ON_ERROR);
        $client = $this->client([new Response($code, ['Retry-After' => '10'], $body)], $history);
        try {
            $client->request('GET', '/open-api/v1/variants');
            self::fail('API failure should throw.');
        } catch (ApiException $exception) {
            self::assertSame($code, $exception->statusCode());
            self::assertSame(['seller_stock' => ['Invalid']], $exception->errors());
            self::assertSame('10', $exception->response->response->getHeaderLine('Retry-After'));
            self::assertStringNotContainsString('test-token', $exception->getMessage());
            self::assertCount(1, $history);
        }
    }

    public function testConnectionFailureDoesNotExposeRequest(): void
    {
        $history = [];
        $client = $this->client([new ConnectException('private details', new Request('GET', 'https://seller.digikala.com'))], $history);
        try {
            $client->request('GET', '/open-api/v1/variants');
            self::fail('Connection failure should throw.');
        } catch (TransportException $exception) {
            self::assertNull($exception->getPrevious());
            self::assertStringNotContainsString('private details', $exception->getMessage());
        }
    }

    public function testBinaryResponseIsNotForcedToJson(): void
    {
        $history = [];
        $client = $this->client([new Response(200, ['Content-Type' => 'application/octet-stream'], 'binary-file')], $history);
        $response = $client->request('GET', '/open-api/v1/');
        self::assertSame('binary-file', (string) $response->response->getBody());
        $this->expectException(\UnexpectedValueException::class);
        $response->json();
    }

    public function testRefreshAndNewTokenRemainExplicit(): void
    {
        $history = [];
        $client = $this->client([new Response(200)], $history);
        $client->authentication()->refresh('expired-token', 'refresh-token');
        self::assertSame(['access_token' => 'expired-token', 'refresh_token' => 'refresh-token'], json_decode(
            (string) $history[0]['request']->getBody(), true, flags: JSON_THROW_ON_ERROR,
        ));
        self::assertNotSame($client, $client->withAccessToken('replacement-token'));
    }
}
