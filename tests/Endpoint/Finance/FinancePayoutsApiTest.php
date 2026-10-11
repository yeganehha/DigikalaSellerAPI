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
use Yeganehha\DigikalaSellerOpenApi\Dto\FinancePayoutDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinancePayoutListRequest;

final class FinancePayoutsApiTest extends TestCase
{
    public function testPayoutListMapsDtoAndStatusQuery(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{"status":"ok","data":{"items":[{"id":12,"status":"in_queue"}],"pager":{"page":1}}}')]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));
        $response = $client->financePayouts()->list(new FinancePayoutListRequest('in_queue'));

        self::assertInstanceOf(FinancePayoutDto::class, $response->data['items'][0]);
        self::assertSame('/open-api/v1/finance/payouts', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('in_queue', $query['search']['status']);
    }
}
