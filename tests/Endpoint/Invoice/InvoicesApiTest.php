<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Endpoint\Invoice;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceListRequest;

final class InvoicesApiTest extends TestCase
{
    public function testInvoiceListMapsSharedInvoiceDtoAndDocumentedFilters(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"status":"ok","data":{"pager":{"page":1},"items":[{"id":1234,"start_date":"2025-01-01","end_date":"2025-01-31","invoice_total_amount":46144502}]}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->invoices()->list(new InvoiceListRequest(payoutOrderStatus: 2, invoiceStartDate: '2025-01-01'));

        self::assertInstanceOf(ApiResponse::class, $response);
        self::assertInstanceOf(InvoiceDto::class, $response->data['items'][0]);
        self::assertSame(1234, $response->data['items'][0]->id);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/invoices', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('2', $query['search']['payout_order_status']);
        self::assertSame('2025-01-01', $query['search']['invoice_start_date']);
    }
}
