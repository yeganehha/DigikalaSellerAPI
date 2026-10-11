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
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionsRequest;

final class FinanceTransactionsApiTest extends TestCase
{
    public function testTransactionsMapItemsAndSendDocumentedSearchFilters(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"status":"ok","data":{"items":[{"id":12,"amount":450,"direction":"credit"}]}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->financeTransactions()->list(new FinanceTransactionsRequest(
            status: 'PENDING,ACCEPTED',
            aggregationType: 'sale,return',
            search: '123',
            startDate: '2025-01-01',
            endDate: '2025-01-31',
        ));

        self::assertInstanceOf(ApiResponse::class, $response);
        self::assertInstanceOf(FinanceTransactionDto::class, $response->data['items'][0]);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/finance/transactions', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('PENDING,ACCEPTED', $query['search']['status']);
        self::assertSame('sale,return', $query['search']['aggregation_type']);
        self::assertSame('123', $query['search']['search']);
        self::assertSame('2025-01-01', $query['search']['start_date']);
        self::assertSame('2025-01-31', $query['search']['end_date']);
    }
}
