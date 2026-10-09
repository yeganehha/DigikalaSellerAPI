<?php

declare(strict_types=1);

use Yeganehha\DigikalaSellerOpenApi\Webhook\Router;
use Yeganehha\DigikalaSellerOpenApi\Webhook\WebhookEvent;

return static function (Router $router): void {
    $router->on('product_variant_status_change', static function (WebhookEvent $event): void {
        // This sample acknowledges the event without changing business data.
        error_log('Digikala product-variant notification received.');
    });
    $router->on('commission_change', static function (WebhookEvent $event): void {
        error_log('Digikala commission notification received.');
    });
};
