<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Endpoint;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\DraftProductDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\DraftProductsRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\IdRequest;

final class DraftProductsApiTest extends TestCase
{
    public function testDraftListAndDetailShareDraftProductDto(): void
    {
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"status":"ok","data":{"items":[{"id":9,"title_fa":"پیش‌نویس"}]}}'),
            new Response(200, [], '{"status":"ok","data":{"id":9,"title_fa":"پیش‌نویس"}}'),
        ]));
        $client = new MarketplaceClient(httpClient: new Client(['handler' => $stack]));

        $list = $client->draftProducts()->list(new DraftProductsRequest());
        $detail = $client->draftProduct()->get(new IdRequest(9));

        self::assertInstanceOf(DraftProductDto::class, $list->data['items'][0]);
        self::assertInstanceOf(DraftProductDto::class, $detail);
        self::assertSame($list->data['items'][0]->id, $detail->id);
    }
}
