<?php

declare(strict_types=1);

use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;
use Yeganehha\DigikalaSellerOpenApi\Exception\TransportException;
use Yeganehha\DigikalaSellerOpenApi\RequestOptions;

require __DIR__ . '/bootstrap.php';

try {
    $response = exampleClient()->call('getVariants', new RequestOptions(query: ['page' => 1, 'size' => 10]));
    echo json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL;
} catch (ApiException $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    if ($exception->response->statusCode() === 429) {
        fwrite(STDERR, 'Retry-After: ' . ($exception->retryAfter() ?? 'not supplied') . PHP_EOL);
    }
    exit(1);
} catch (TransportException $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
