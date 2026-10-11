<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Endpoint\Variant;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantActivationRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantArchiveRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantSellingPriceRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantStockRequest;

use Yeganehha\DigikalaSellerOpenApi\Dto\VariantGoldRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantIdsRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantPriceCalculatorRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantExportRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantEditRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantB2bPricesRequest;
final class ProductVariantOperationsApiTest extends TestCase
{
    public function testActivationUsesPutAndDocumentedPayload(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $response = $client->productVariantActivation()->update(123, new VariantActivationRequest(true));
        self::assertInstanceOf(\Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse::class, $response);

        self::assertRequest($history[0]['request'], 'PUT', '/open-api/v1/variants/123/activation');
        self::assertSame(['activation' => true], json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testArchiveUsesPutAndDocumentedPayload(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->productVariantArchive()->archive(123, new VariantArchiveRequest(true));

        self::assertRequest($history[0]['request'], 'PUT', '/open-api/v1/variants/123/archive');
        self::assertSame(['archive' => true], json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testStockUpdateUsesPatchAndAbsoluteSellerStock(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->productVariantStockUpdate()->update(123, new VariantStockRequest(10));

        self::assertRequest($history[0]['request'], 'PATCH', '/open-api/v1/variants/123/seller-stock');
        self::assertSame(['seller_stock' => 10], json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testSellingPriceUsesPatchAndBodyVariantId(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->productVariantSellingPrice()->update(new VariantSellingPriceRequest(123, 9000, 25));

        self::assertRequest($history[0]['request'], 'PATCH', '/open-api/v1/variants/selling-price');
        self::assertSame(
            ['variant_id' => 123, 'selling_price' => 9000, 'credit_increase_percentage' => 25],
            json_decode((string) $history[0]['request']->getBody(), true),
        );
    }

    public function testVariantUpdateUsesPutAndDocumentedPath(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $request = new ProductVariantEditRequest([
            'seller_stock' => 10,
            'maximum_per_order' => 5,
            'selling_price' => 9000,
            'price_limit' => 2,
            'credit_increase_percentage' => 25,
            'shipping_type' => 'digikala',
            'lead_time' => 1,
            'three_hour_delivery' => false,
        ]);
        $response = $client->productVariantUpdate()->update(123, $request);

        self::assertInstanceOf(ProductVariantDto::class, $response);

        self::assertRequest($history[0]['request'], 'PUT', '/open-api/v1/variants/123');
        self::assertSame($request->toArray(), json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testStockGetUsesDocumentedPath(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->productVariantStockDetails()->get(123);

        self::assertRequest($history[0]['request'], 'GET', '/open-api/v1/variants/123/seller-stock');
    }
    public function testB2bActivationUsesPutWithVariantIds(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->productVariantB2bActivation()->activate(new VariantIdsRequest([11, 12]));

        self::assertRequest($history[0]['request'], 'PUT', '/open-api/v1/variants/b2b-activation');
        self::assertSame(['variant_ids' => [11, 12]], json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testExportUsesPostWithFilterBody(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->productVariantExport()->export(new VariantExportRequest(['active' => true]));

        self::assertRequest($history[0]['request'], 'POST', '/open-api/v1/variants/export');
        self::assertSame(['active' => true], json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testGoldGetUsesDocumentedPath(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->productVariantGoldDetails()->get(123);

        self::assertRequest($history[0]['request'], 'GET', '/open-api/v1/variants/123/gold');
    }

    public function testGoldUpdateUsesPutWithGoldFields(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $request = new VariantGoldRequest(12.5, 5.0, 1.0, 0.5, 2);
        $client->productVariantGoldUpdate()->update(123, $request);

        self::assertRequest($history[0]['request'], 'PUT', '/open-api/v1/variants/123/gold');
        self::assertSame($request->toArray(), json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testPriceCalculatorUsesRequiredQueryParameters(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $request = new VariantPriceCalculatorRequest(123, [
            'gold_wage' => 12.3,
            'gold_profit' => 5.0,
            'none_gold_wage' => 1.0,
            'none_gold_cost' => 2.0,
        ]);
        $client->productVariantPriceCalculator()->calculate($request);

        self::assertRequest($history[0]['request'], 'GET', '/open-api/v1/variants/123/price-calculator');
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('12.3', $query['gold_wage']);
        self::assertSame('5', $query['gold_profit']);
        self::assertSame('1', $query['none_gold_wage']);
        self::assertSame('2', $query['none_gold_cost']);
    }

    public function testB2bPricesGetUsesDocumentedPath(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->productVariantB2bPricesDetails()->get(123);

        self::assertRequest($history[0]['request'], 'GET', '/open-api/v1/variants/123/b2b-prices');
    }

    public function testB2bPricesUpdateUsesPutWithDocumentedBody(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $request = new VariantB2bPricesRequest(true, [['min' => 1, 'max' => 10, 'selling_price' => 9000]]);
        $client->productVariantB2bPricesUpdate()->update(123, $request);

        self::assertRequest($history[0]['request'], 'PUT', '/open-api/v1/variants/123/b2b-prices');
        self::assertSame($request->toArray(), json_decode((string) $history[0]['request']->getBody(), true));
    }


    /** @return array{MarketplaceClient, array<int, array<string, mixed>>} */
    private function clientWithHistory(): array
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{}}'),
        ]));
        $stack->push(Middleware::history($history));

        return [new MarketplaceClient(httpClient: new Client(['handler' => $stack])), $history];
    }

    private static function assertRequest($request, string $method, string $path): void
    {
        self::assertSame($method, $request->getMethod());
        self::assertSame($path, $request->getUri()->getPath());
    }
}
