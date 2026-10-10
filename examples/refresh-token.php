<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\HealthCheckRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\RefreshTokenRequest;
use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;

$accessToken = getenv('DIGIKALA_ACCESS_TOKEN');
$refreshToken = getenv('DIGIKALA_REFRESH_TOKEN');
if (!is_string($accessToken) || $accessToken === '' || !is_string($refreshToken) || $refreshToken === '') {
    fwrite(STDERR, "Set DIGIKALA_ACCESS_TOKEN and DIGIKALA_REFRESH_TOKEN in the environment.\n");
    exit(1);
}

$client = new MarketplaceClient();

try {
    $health = $client->healthCheck()->get(new HealthCheckRequest());
    echo 'Health-check response: ' . json_encode($health->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;

    $response = $client->refreshToken()->refresh(new RefreshTokenRequest($accessToken, $refreshToken));
    echo 'Token refresh succeeded; expires at ' . $response->data->accessTokenExpiresAt->date . PHP_EOL;
    echo "Store the returned access and refresh tokens securely; the refresh token may rotate.\n";
} catch (ApiException $exception) {
    fwrite(STDERR, 'Marketplace API request failed (HTTP ' . ($exception->statusCode ?? 'no response') . ').' . PHP_EOL);
    exit(1);
}
