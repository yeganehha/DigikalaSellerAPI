# Digikala Marketplace API Client

A typed PHP client for the Digikala Marketplace Open API. The initial release provides the service health-check and OAuth refresh-token endpoints. The codebase is structured to add the remaining documented endpoints as separate endpoint classes with request and response DTOs.

## Requirements

- PHP 8.1 or newer
- Composer

## Installation

```bash
composer require digikala/marketplace-api-client
```

The package is not published yet. For local development, clone this repository and run `composer install`.

## Quick start

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\HealthCheckRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\RefreshTokenRequest;

$client = new MarketplaceClient();

$health = $client->healthCheck()->get(new HealthCheckRequest());
var_dump($health->payload);

// Refresh-token calls require the expired access token and the current refresh token.
$tokens = $client->refreshToken()->refresh(
    new RefreshTokenRequest(
        accessToken: getenv('DIGIKALA_ACCESS_TOKEN') ?: throw new RuntimeException('Set DIGIKALA_ACCESS_TOKEN.'),
        refreshToken: getenv('DIGIKALA_REFRESH_TOKEN') ?: throw new RuntimeException('Set DIGIKALA_REFRESH_TOKEN.'),
    ),
);

// Persist both returned tokens securely; the API may rotate the refresh token.
$nextAccessToken = $tokens->data->accessToken;
$nextRefreshToken = $tokens->data->refreshToken;
```

A runnable sample is available at [`examples/refresh-token.php`](examples/refresh-token.php). Never commit access tokens, refresh tokens, authorization codes, or other credentials.

## Error handling

Network failures, non-2xx responses, invalid JSON, and unexpected response shapes raise exceptions. `ApiException::$statusCode` contains the HTTP status when a response was received. DTO validation errors raise `UnexpectedValueException`; invalid request inputs raise `InvalidArgumentException`.

```php
use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;

try {
    $health = $client->healthCheck()->get(new HealthCheckRequest());
} catch (ApiException $exception) {
    // Log safe diagnostic information; do not log credentials or response bodies.
    error_log('Marketplace API failed; HTTP status: ' . ($exception->statusCode ?? 'no response'));
}
```

## Tests

```bash
composer install
composer test
```

Tests use Guzzle's mock handler and do not call the production service.

## Current endpoint coverage

| Endpoint | Method | Request DTO | Response DTO |
| --- | --- | --- | --- |
| `GET /open-api/v1/` | `healthCheck()->get()` | `HealthCheckRequest` | `HealthCheckResponse` |
| `POST /open-api/v1/auth/refresh-token` | `refreshToken()->refresh()` | `RefreshTokenRequest` | `RefreshTokenResponse` → `RefreshTokenData` → `TokenExpiry` |

The attached OpenAPI document lists 275 operations. Only these two endpoints are implemented in this initial increment; the rest are not yet included.

## License

MIT. See [LICENSE](LICENSE).
