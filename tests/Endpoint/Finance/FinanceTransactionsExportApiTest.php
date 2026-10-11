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
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionsExportRequest;

final class FinanceTransactionsExportApiTest extends TestCase
{
    public function testExcelExportUsesPostAndDocumentedFilters(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"message":"queued"}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $request = new FinanceTransactionsExportRequest(
            startDate: '2025-10-01',
            endDate: '2025-10-31',
            statuses: 'PENDING,ACCEPTED',
            notationIds: '60,120',
            source: 'per_notations',
            body: [
                'start_date' => '2025-10-01',
                'search' => ['status' => 'PENDING'],
            ],
        );
        $client->financeTransactionsExport()->export($request);

        self::assertSame('POST', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/finance/transactions/export/excel', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('2025-10-01', $query['start_date']);
        self::assertSame('PENDING,ACCEPTED', $query['statuses']);
        self::assertSame($request->toBody(), json_decode((string) $history[0]['request']->getBody(), true));
    }
}
