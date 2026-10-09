<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests;

use Yeganehha\DigikalaSellerOpenApi\Client;
use Yeganehha\DigikalaSellerOpenApi\Configuration;
use Yeganehha\DigikalaSellerOpenApi\OpenApi\Document;
use Yeganehha\DigikalaSellerOpenApi\OpenApi\Operation;
use Yeganehha\DigikalaSellerOpenApi\Tests\Support\ContractOptions;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class AllOperationsTest extends TestCase
{
    public static function operations(): iterable
    {
        foreach (Document::bundled()->operations() as $operation) {
            yield $operation->id => [$operation];
        }
    }

    #[DataProvider('operations')]
    public function testEveryDocumentedOperationCanBeSent(Operation $operation): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok","data":{}}'),
        ]));
        $stack->push(Middleware::history($history));
        $client = new Client(new Configuration(accessToken: 'contract-token'), new HttpClient(['handler' => $stack]));
        $response = $client->call($operation->id, ContractOptions::forOperation($operation));
        self::assertSame(200, $response->statusCode());
        self::assertSame('ok', $response->json()['status']);
        self::assertCount(1, $history);
        $request = $history[0]['request'];
        self::assertSame($operation->method, $request->getMethod());
        $expectedPath = preg_replace('/\{[^}]+\}/', '123', $operation->path);
        self::assertSame($expectedPath, $request->getUri()->getPath());
        self::assertSame('seller.digikala.com', $request->getUri()->getHost());
        self::assertSame('Bearer contract-token', $request->getHeaderLine('Authorization'));
        self::assertFalse($history[0]['options']['allow_redirects']);
        self::assertSame(30.0, $history[0]['options']['timeout']);
    }

    public function testRegistryExactlyMatchesAllSourceMethodsAndPaths(): void
    {
        $source = Yaml::parseFile(dirname(__DIR__) . '/digikala-open-api-document.yml');
        $expected = [];
        foreach ($source['paths'] as $path => $item) {
            foreach (['get', 'post', 'put', 'patch', 'delete', 'head', 'options', 'trace'] as $method) {
                if (isset($item[$method])) {
                    $expected[] = strtoupper($method) . ' ' . $path;
                }
            }
        }
        $actual = [];
        $ids = [];
        foreach (Document::bundled()->operations() as $operation) {
            $actual[] = $operation->method . ' ' . $operation->path;
            $ids[] = $operation->id;
            self::assertSame($operation, Document::bundled()->find($operation->method, $operation->path));
        }
        sort($expected);
        sort($actual);
        self::assertNotEmpty($actual);
        self::assertSame($expected, $actual);
        self::assertCount(count($ids), array_unique($ids));
    }

    public function testStableIdentifiersAndDocumentedWebhookEvents(): void
    {
        $document = Document::bundled();
        self::assertSame('/open-api/v1/variants/{variant_id}', $document->operation('getVariantsByVariantId')->path);
        self::assertContains('product_variant_status_change', $document->webhookEventTypes());
        self::assertContains('order_return', $document->webhookEventTypes());
    }
}
