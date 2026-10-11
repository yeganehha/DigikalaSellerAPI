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
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceItemDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceItemsRequest;

final class InvoiceItemsApiTest extends TestCase
{
    public function testItemsUseDocumentedPathAndMapItemDtos(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{"pager":{"page":1},"items":[{"id":12,"credit":50}]}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $response = $client->invoiceItems()->list(9, 7, 'sale', new InvoiceItemsRequest(
            page: 1,
            size: 20,
            invoiceId: 9,
            financialNotationId: 7,
            calculationType: 'sale',
        ));

        self::assertInstanceOf(ApiResponse::class, $response);
        self::assertInstanceOf(InvoiceItemDto::class, $response->data['items'][0]);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/invoices/9/items/7/sale', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('1', $query['page']);
        self::assertSame('9', $query['search']['invoice_id']);
        self::assertSame('7', $query['search']['financial_notation_id']);
        self::assertSame('sale', $query['search']['calculation_type']);
    }
}
