<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Endpoint\Finance;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionRowDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionRowsRequest;

final class FinanceTransactionRowsApiTest extends TestCase
{
    public function testRowsUsesDocumentedPathAndQuery(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"status":"ok","data":{"items":[{"id":7,"amount":8,"direction":"debit"}]}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->financeTransactionRows()->list(new FinanceTransactionRowsRequest(
            status: 'ACCEPTED',
            notations: '9,10',
            search: 'serial',
            startDate: '2025-02-01',
            endDate: '2025-02-28',
        ));

        self::assertInstanceOf(FinanceTransactionRowDto::class, $response->data['items'][0]);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/finance/transactions/rows', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('9,10', $query['search']['notations']);
        self::assertSame('serial', $query['search']['search']);
    }
}
