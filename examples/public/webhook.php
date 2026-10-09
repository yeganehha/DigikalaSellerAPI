<?php

declare(strict_types=1);

use Yeganehha\DigikalaSellerOpenApi\Webhook\EventResolver;
use Yeganehha\DigikalaSellerOpenApi\Webhook\FileIdempotencyStore;
use Yeganehha\DigikalaSellerOpenApi\Webhook\Receiver;
use Yeganehha\DigikalaSellerOpenApi\Webhook\Router;

require dirname(__DIR__) . '/bootstrap.php';

try {
    $gatewayToken = requiredEnvironment('DK_WEBHOOK_GATEWAY_TOKEN');
    if (strlen($gatewayToken) < 32) {
        throw new RuntimeException('Use a gateway token of at least 32 characters.');
    }
    $typeField = requiredEnvironment('DK_WEBHOOK_EVENT_TYPE_FIELD');
    $idField = requiredEnvironment('DK_WEBHOOK_EVENT_ID_FIELD');
    $router = Router::fromFile(dirname(__DIR__) . '/webhook-handlers.php');
    $store = new FileIdempotencyStore(dirname(__DIR__, 2) . '/var/webhooks');
    $receiver = new Receiver(
        $router,
        static function (\Psr\Http\Message\ServerRequestInterface $request, string $body) use ($gatewayToken): bool {
            $provided = $request->getHeaderLine('X-Webhook-Gateway-Token');
            return hash_equals($gatewayToken, $provided);
        },
        EventResolver::jsonFields($typeField, $idField, requiredEnvironment('DK_WEBHOOK_NAMESPACE')),
        $store,
    );
    // Restrict this endpoint to your gateway; it must authenticate Digikala upstream.
    $receiver->serve();
} catch (Throwable) {
    http_response_code(503);
    header('Content-Type: application/json');
    header('Cache-Control: no-store');
    echo '{"status":"configuration_unavailable"}';
}
