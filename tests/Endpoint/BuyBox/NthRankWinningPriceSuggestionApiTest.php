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
use Yeganehha\DigikalaSellerOpenApi\Dto\BuyBoxNthRankWinningPriceRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\BuyBoxPriceSuggestionDto;

final class NthRankWinningPriceSuggestionApiTest extends TestCase
{
    public function testNthRankSuggestionUsesDocumentedParameters(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"status":"ok","data":{"suggested_price":2561000}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $result = $client->buyBoxNthRankWinningPriceSuggestion()->get(new BuyBoxNthRankWinningPriceRequest(1231234, 2));

        self::assertInstanceOf(BuyBoxPriceSuggestionDto::class, $result);
        self::assertSame('GET', $history[0]['request']->getMethod());
        self::assertSame('/open-api/v1/pricing/buybox/price-suggestion/nth-rank-winning-price', $history[0]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('1231234', $query['product_variant_id']);
        self::assertSame('2', $query['target_rank']);
    }
}
