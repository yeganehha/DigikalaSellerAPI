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
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantColorRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantPageRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantSizeRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantWarrantyRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantDto;

final class VariantCreationOperationsApiTest extends TestCase
{
    public function testConfigUsesGetAndProductPath(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->variantCreationConfig()->get(42);
        self::assertRequest($history[0]['request'], 'GET', '/open-api/v1/variants/creation/42');
    }

    public function testVariantEditUsesPutAndOptionalSellerQuery(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $response = $client->variantCreationEdit()->update(42, 51, new \Yeganehha\DigikalaSellerOpenApi\Dto\VariantCreationEditRequest([
            'is_active' => true,
            'order_limit' => 5,
            'marketplace_seller_stock' => 10,
            'site' => 'digikala',
            'shipping_type' => 'digikala',
        ]), 7);
        self::assertInstanceOf(ProductVariantDto::class, $response);
        self::assertRequest($history[0]['request'], 'PUT', '/open-api/v1/variants/creation/42/51');
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('7', $query['force_marketplace_seller_id']);
    }

    public function testProductImagesUsesGetAndProductPath(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->variantProductImages()->list(42);
        self::assertRequest($history[0]['request'], 'GET', '/open-api/v1/variants/creation/images/42');
    }

    public function testModerationImagesUsesDocumentedQuery(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->variantModerationImages()->list(new \Yeganehha\DigikalaSellerOpenApi\Dto\VariantModerationImagesRequest(
            productId: 42,
            colorId: 3,
            moderationStatus: 'pending',
        ));
        self::assertRequest($history[0]['request'], 'GET', '/open-api/v1/variants/creation/images/moderation');
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('42', $query['product_id']);
        self::assertSame('3', $query['color_id']);
        self::assertSame('pending', $query['moderation_status']);
    }

    public function testRejectionReasonsUsesGet(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->variantImageRejectionReasons()->list();
        self::assertRequest($history[0]['request'], 'GET', '/open-api/v1/variants/creation/images/rejection-reasons');
    }

    public function testSizeTypesUsesGet(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->variantSizeTypes()->list();
        self::assertRequest($history[0]['request'], 'GET', '/open-api/v1/variants/creation/size/types');
    }

    public function testSizeRequestUsesPostAndDocumentedBody(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->variantSizeRequest()->request(new VariantSizeRequest(42, 'XXL', 1));
        self::assertRequest($history[0]['request'], 'POST', '/open-api/v1/variants/creation/size/request');
        self::assertSame(['product_id' => 42, 'title' => 'XXL', 'theme_id' => 1], json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testColorRequestUsesPostAndDocumentedBody(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->variantColorRequest()->request(new VariantColorRequest(42, 'Navy', 'IMG-1', '#112233', 1));
        self::assertRequest($history[0]['request'], 'POST', '/open-api/v1/variants/creation/color/request');
        self::assertSame(['product_id' => 42, 'title' => 'Navy', 'product_image_id' => 'IMG-1', 'hex_code' => '#112233', 'theme_id' => 1], json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testWarrantyRequestUsesPostAndDocumentedBody(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $request = new VariantWarrantyRequest([
            'product_id' => 42,
            'warranty_title' => 'Warranty',
            'warranty_period' => 18,
            'warranty_image_id_1' => 'IMG-1',
            'has_insurance' => false,
        ]);
        $client->variantWarrantyRequest()->request($request);
        self::assertRequest($history[0]['request'], 'POST', '/open-api/v1/variants/creation/warranty/request');
        self::assertSame($request->toArray(), json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testThemeValuesUsesPathAndPaginationQuery(): void
    {
        [$client, $history] = $this->clientWithHistory();
        $client->variantThemeValues()->list(5, new VariantPageRequest(productId: 42, categoryId: 8, brandId: 9));
        self::assertRequest($history[0]['request'], 'GET', '/open-api/v1/variants/creation/themes/5/theme-values');
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('42', $query['product_id']);
        self::assertSame('8', $query['category_id']);
        self::assertSame('9', $query['brand_id']);
    }

    /** @return array{MarketplaceClient, array<int, array<string, mixed>>} */
    private function clientWithHistory(): array
    {
        $history = new \ArrayObject();
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
