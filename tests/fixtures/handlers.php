<?php

declare(strict_types=1);

use Yeganehha\DigikalaSellerOpenApi\Webhook\Router;
use Yeganehha\DigikalaSellerOpenApi\Webhook\WebhookEvent;

return static function (Router $router): void {
    $router->on('order_processed', static function (WebhookEvent $event): void {
        if ($event->type !== 'order_processed') {
            throw new LogicException('Unexpected event routing.');
        }
    });
};
