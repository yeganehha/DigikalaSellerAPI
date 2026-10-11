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
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceDateRangeRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceOverviewDto;

final class FinanceOverviewApiTest extends TestCase
{
    public function testOverviewUsesDocumentedDatesAndDto(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"status":"ok","data":{"sales":{"total_amount":5}}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->financeOverview()->get(new FinanceDateRangeRequest('2025-01-01', '2025-01-31'));

        self::assertInstanceOf(FinanceOverviewDto::class, $response);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/finance/overview/v2', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('2025-01-01', $query['start_date']);
        self::assertSame('2025-01-31', $query['end_date']);
    }
}
