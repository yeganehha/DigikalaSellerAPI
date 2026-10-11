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
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionSummaryDto;

final class FinanceTransactionSummaryApiTest extends TestCase
{
    public function testSummaryUsesDocumentedGetPathAndDto(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"status":"ok","data":{"total":{"amount":100,"count":2},"pending":{"amount":40,"count":1}}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));
        $summary = $client->financeTransactionSummary()->get();

        self::assertInstanceOf(FinanceTransactionSummaryDto::class, $summary);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/finance/transactions/summary', $history[0]['request']->getUri()->getPath());
    }
}