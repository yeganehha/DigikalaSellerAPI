<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests;

use Yeganehha\DigikalaSellerOpenApi\Webhook\Claim;
use Yeganehha\DigikalaSellerOpenApi\Webhook\EventResolver;
use Yeganehha\DigikalaSellerOpenApi\Webhook\IdempotencyStore;
use Yeganehha\DigikalaSellerOpenApi\Webhook\Receiver;
use Yeganehha\DigikalaSellerOpenApi\Webhook\Router;
use Yeganehha\DigikalaSellerOpenApi\Webhook\WebhookEvent;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ReceiverTest extends TestCase
{
    private function request(string $body = '{"kind":"order_processed","identifier":"1"}'): ServerRequest
    {
        return new ServerRequest('POST', 'https://receiver.example.test/', [
            'Content-Type' => 'application/json',
        ], $body);
    }

    private function receiver(Router $router, ?IdempotencyStore $store = null): Receiver
    {
        return new Receiver($router, static fn ($request): bool => true, EventResolver::jsonFields('kind', 'identifier', 'test'), $store);
    }

    public function testAuthenticatesBeforeCallingHandler(): void
    {
        $called = false;
        $router = new Router();
        $router->on('order_processed', static function () use (&$called): void {
            $called = true;
        });
        $receiver = new Receiver($router, static fn ($request): bool => false, EventResolver::jsonFields('kind', 'identifier', 'test'));
        self::assertSame(401, $receiver->handle($this->request())->getStatusCode());
        self::assertFalse($called);
    }

    public function testAutomaticallyDispatchesResolvedEvent(): void
    {
        $seen = [];
        $router = new Router();
        $router->on('order_processed', static function (WebhookEvent $event) use (&$seen): void {
            $seen[] = $event->id;
        });
        self::assertSame(200, $this->receiver($router)->handle($this->request())->getStatusCode());
        self::assertSame(['test:order_processed:1'], $seen);
    }

    public function testRejectsMalformedOrNonObjectJson(): void
    {
        $receiver = $this->receiver(new Router());
        foreach (['{', 'null', '[]', '"text"', '{}', '{"kind":1}'] as $body) {
            self::assertSame(400, $receiver->handle($this->request($body))->getStatusCode());
        }
    }

    public function testMethodMediaTypeAndSizeAreEnforced(): void
    {
        $receiver = new Receiver(new Router(), static fn ($request): bool => true, EventResolver::jsonFields('kind', 'identifier', 'test'), maxBytes: 8);
        self::assertSame(405, $receiver->handle($this->request()->withMethod('GET'))->getStatusCode());
        self::assertSame(415, $receiver->handle($this->request()->withoutHeader('Content-Type'))->getStatusCode());
        self::assertSame(413, $receiver->handle($this->request())->getStatusCode());
    }

    public function testUnknownEventsAreNotAcknowledgedAsHandled(): void
    {
        self::assertSame(422, $this->receiver(new Router())->handle($this->request())->getStatusCode());
    }

    public function testHandlerFailureReturnsRetryableErrorWithoutLeakingDetails(): void
    {
        $router = new Router();
        $router->on('order_processed', static function (): void {
            throw new RuntimeException('private credentials');
        });
        $response = $this->receiver($router)->handle($this->request());
        self::assertSame(500, $response->getStatusCode());
        self::assertStringNotContainsString('private credentials', (string) $response->getBody());
    }

    public function testDuplicateEventIsAcknowledgedWithoutExecution(): void
    {
        $store = new class implements IdempotencyStore {
            public function claim(string $key): Claim
            {
                return Claim::Duplicate;
            }

            public function complete(string $key): void
            {
                throw new RuntimeException('Must not complete a duplicate.');
            }

            public function release(string $key): void
            {
                throw new RuntimeException('Must not release a duplicate.');
            }
        };
        $called = false;
        $router = new Router();
        $router->on('order_processed', static function () use (&$called): void {
            $called = true;
        });
        self::assertSame(200, $this->receiver($router, $store)->handle($this->request())->getStatusCode());
        self::assertFalse($called);
    }

    public function testLoadsHandlersFromAnExplicitFile(): void
    {
        $router = Router::fromFile(__DIR__ . '/fixtures/handlers.php');
        self::assertTrue($router->supports('order_processed'));
        self::assertSame(200, $this->receiver($router)->handle($this->request())->getStatusCode());
    }

    public function testBusyEventReceivesRetryAfter(): void
    {
        $store = new class implements IdempotencyStore {
            public function claim(string $key): Claim
            {
                return Claim::Busy;
            }

            public function complete(string $key): void
            {
            }

            public function release(string $key): void
            {
            }
        };
        $router = new Router();
        $router->on('order_processed', static function (): void {
        });
        $response = $this->receiver($router, $store)->handle($this->request());
        self::assertSame(503, $response->getStatusCode());
        self::assertSame('5', $response->getHeaderLine('Retry-After'));
    }
}
