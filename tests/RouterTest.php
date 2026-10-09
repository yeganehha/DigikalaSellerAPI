<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests;

use Yeganehha\DigikalaSellerOpenApi\Webhook\EventResolver;
use Yeganehha\DigikalaSellerOpenApi\Webhook\Router;
use Yeganehha\DigikalaSellerOpenApi\Webhook\WebhookEvent;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testAllHandlersForTheMatchingEventExecuteInRegistrationOrder(): void
    {
        $seen = [];
        $router = new Router();
        $router->on('order_processed', static function () use (&$seen): void {
            $seen[] = 'first';
        });
        $router->on('order_processed', static function () use (&$seen): void {
            $seen[] = 'second';
        });
        $router->on('*', static function () use (&$seen): void {
            $seen[] = 'fallback';
        });

        $router->dispatch(new WebhookEvent('order_processed', [], 'seller:1'));
        self::assertSame(['first', 'second'], $seen);
        $router->dispatch(new WebhookEvent('commission_change', [], 'seller:2'));
        self::assertSame(['first', 'second', 'fallback'], $seen);
    }

    public function testEmptyDeliveryIdentifierIsRejectedBeforeNamespacing(): void
    {
        $resolver = EventResolver::jsonFields('kind', 'identifier', 'seller');
        $this->expectException(InvalidArgumentException::class);
        $resolver(['kind' => 'order_processed', 'identifier' => '']);
    }
}
