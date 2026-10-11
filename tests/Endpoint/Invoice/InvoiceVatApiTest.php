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
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceVatRequest;

final class InvoiceVatApiTest extends TestCase
{
    public function testVatUsesPostAndDocumentedJsonBody(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"vat_amount":9000}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $client->invoiceVat()->calculate(new InvoiceVatRequest(123, '2025-10-05', 100000, true));

        self::assertSame('POST', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/invoices/vat', $history[0]['request']->getUri()->getPath());
        self::assertSame(
            ['product_id' => 123, 'date' => '2025-10-05', 'price' => 100000, 'is_vat_included_in_price' => true],
            json_decode((string) $history[0]['request']->getBody(), true),
        );
    }
}
