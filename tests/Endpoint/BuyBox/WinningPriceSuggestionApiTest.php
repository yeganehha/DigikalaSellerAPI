<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Endpoint\BuyBox;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\BuyBoxPriceSuggestionDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\BuyBoxWinningPriceRequest;

final class WinningPriceSuggestionApiTest extends TestCase
{
    public function testWinningPriceUsesDocumentedQueryAndResponseDto(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"status":"ok","data":{"suggested_price":2561000}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $result = $client->buyBoxWinningPriceSuggestion()->get(new BuyBoxWinningPriceRequest(1231234));

        self::assertInstanceOf(BuyBoxPriceSuggestionDto::class, $result);
        self::assertSame(2561000, $result->suggestedPrice);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/pricing/buybox/price-suggestion/winning-price', $history[0]['request']->getUri()->getPath());
        self::assertSame('product_variant_id=1231234', $history[0]['request']->getUri()->getQuery());
    }
}
