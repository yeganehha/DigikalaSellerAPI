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
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionDetailDto;

final class FinanceTransactionDetailsApiTest extends TestCase
{
    public function testTransactionDetailUsesPathIdAndMapsDto(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{"status":"ok","data":{"transaction_id":123,"final_amount":500}}')]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));
        $detail = $client->financeTransactionDetails()->get(123);

        self::assertInstanceOf(FinanceTransactionDetailDto::class, $detail);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/finance/transactions/123', $history[0]['request']->getUri()->getPath());
    }
}
