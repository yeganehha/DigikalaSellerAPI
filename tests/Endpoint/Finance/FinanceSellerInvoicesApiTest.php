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
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceSellerInvoiceDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceSellerInvoicesRequest;

final class FinanceSellerInvoicesApiTest extends TestCase
{
    public function testSellerInvoicesMapDtoAndForwardDocumentedQuery(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"items":[{"id":321,"start_date":"2025-01-01","final_amount":1000,"amount_type":"credit"}]}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->financeSellerInvoices()->list(new FinanceSellerInvoicesRequest(invoiceId: 321));

        self::assertInstanceOf(FinanceSellerInvoiceDto::class, $response->data['items'][0]);
        self::assertSame('/open-api/v1/finance/seller-invoices', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('321', $query['search']['invoice_id']);
    }
}
