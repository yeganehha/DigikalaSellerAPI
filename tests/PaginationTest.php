<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests;

use Yeganehha\DigikalaSellerOpenApi\Client;
use Yeganehha\DigikalaSellerOpenApi\Configuration;
use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;
use Yeganehha\DigikalaSellerOpenApi\RequestOptions;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class PaginationTest extends TestCase
{
    private function client(array $responses, array &$history): Client
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($history));

        return new Client(
            new Configuration(accessToken: 'test-token'),
            new HttpClient(['handler' => $stack]),
        );
    }

    private function page(int $total): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'ok',
            'data' => ['pager' => ['total_pages' => $total]],
        ], JSON_THROW_ON_ERROR));
    }

    public function testPaginationIsLazyAndStopsAtTheLastPage(): void
    {
        $history = [];
        $client = $this->client([$this->page(2), $this->page(2)], $history);
        $pages = $client->pages('getVariants', new RequestOptions(query: ['size' => 5]));
        self::assertSame([], $history);

        $results = iterator_to_array($pages);
        self::assertSame([1, 2], array_keys($results));
        self::assertCount(2, $history);
        parse_str($history[1]['request']->getUri()->getQuery(), $query);
        self::assertSame('2', $query['page']);
        self::assertSame('5', $query['size']);
    }

    public function testMissingPaginationMetadataFailsExplicitly(): void
    {
        $history = [];
        $client = $this->client([new Response(200, [], '{"status":"ok","data":{}}')], $history);
        $this->expectException(RuntimeException::class);
        iterator_to_array($client->pages('getVariants'));
    }

    public function testPageLimitDoesNotSilentlyPresentPartialResultsAsComplete(): void
    {
        $history = [];
        $client = $this->client([$this->page(10)], $history);
        $this->expectException(RuntimeException::class);
        iterator_to_array($client->pages('getVariants', maxPages: 1));
    }

    public function testApiErrorEnvelopeFailsEvenWithHttp200(): void
    {
        $history = [];
        $client = $this->client([new Response(200, [], '{"status":"error","errors":{}}')], $history);
        $this->expectException(ApiException::class);
        $client->call('getVariants');
    }
}
