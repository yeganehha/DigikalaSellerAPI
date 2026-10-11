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
use Yeganehha\DigikalaSellerOpenApi\Dto\FinancePayoutDetailDto;

final class FinancePayoutDetailsApiTest extends TestCase
{
    public function testPayoutDetailsUseDocumentedPathAndDto(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"payout_id":123,"final_credit":100,"final_debit":0}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $payout = $client->financePayoutDetails()->get(123);

        self::assertInstanceOf(FinancePayoutDetailDto::class, $payout);
        self::assertSame(123, $payout->payoutId);
        self::assertSame('/open-api/v1/finance/payouts/123', $history[0]['request']->getUri()->getPath());
    }
}
