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
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceDto;

final class InvoiceDetailsApiTest extends TestCase
{
    public function testDetailUsesDocumentedPathAndInvoiceDto(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"start_date":"2025-01-01","end_date":"2025-01-31","total_income":123}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $invoice = $client->invoiceDetails()->get(123);

        self::assertInstanceOf(InvoiceDto::class, $invoice);
        self::assertSame('2025-01-01', $invoice->startDate);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/invoices/123/details', $history[0]['request']->getUri()->getPath());
    }
}
