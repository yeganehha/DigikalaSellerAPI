# Digikala Marketplace API Client

A typed PHP client for the Digikala Marketplace Open API. This repository currently implements health-check, refresh-token, category discovery, product search and seller-product workflows described in the attached OpenAPI excerpt. Each implemented operation has a dedicated endpoint class; request DTOs are used where the documented input schema is available. Responses with incomplete schemas remain available through `ApiResponse` without invented fields.

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
## Category and product search

```php
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\CategoryTreeRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductSearchRequest;

$client = new MarketplaceClient(accessToken: getenv('DIGIKALA_ACCESS_TOKEN') ?: null);
$categories = $client->categories()->get(new CategoryTreeRequest(parentId: 12));
$products = $client->productSearch()->search(new ProductSearchRequest(keyword: 'گوشی'));
```

Use the endpoint-specific DTOs for seller-product filters, draft filters, product details, brand requests, and uploads. For file uploads, pass an opened stream and its filename to `UploadImageRequest`; the client sends multipart form data and does not set the multipart boundary manually.


## Current endpoint coverage

The current implementation covers the operations described in the supplied excerpt for health and authentication, category discovery, product search and creation, drafts, seller products, product editing, publishing, image uploads, and brand requests. See the endpoint classes under `src/Endpoint/` and request/response DTOs under `src/Dto/` for the exact method signatures.

The supplied excerpt is partial: it names 275 operations, but does not include every operation's path, schema, or response shape. This release implements only the routes explicitly described in the excerpt and does not claim full coverage of all 275 operations. Incomplete response schemas are preserved in `ApiResponse` rather than guessed.

## License

MIT. See [LICENSE](LICENSE).
