# Digikala Marketplace API Client

A typed PHP client for the Digikala Marketplace Open API. This repository currently implements health-check, refresh-token, category discovery, product search and seller-product workflows described in the attached OpenAPI excerpt. Each implemented operation has a dedicated endpoint class; request DTOs are used where the documented input schema is available. Responses with incomplete schemas remain available through `ApiResponse` without invented fields.

## Requirements

- PHP 8.1 or newer
- Composer

## Installation

```bash
composer require yeganehha/digikala-seller-open-api
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

## Product variants

```php
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantListRequest;


$client = new MarketplaceClient(accessToken: getenv('DIGIKALA_ACCESS_TOKEN') ?: null);
$variants = $client->productVariants()->list(new ProductVariantListRequest(
    page: 1,
    size: 50,
    filters: ['active' => true, 'shipping_type' => 'digikala'],
));
$variant = $client->productVariantDetails()->get(12345);
```

Variant list, detail, creation, and edit responses that represent a variant share `ProductVariantDto`; list metadata such as `pager` and `sort_data` remains available through `ApiResponse`. Responses without a complete variant schema, including activation responses, are preserved in `ApiResponse`. Each documented Variant and Variant Creation operation has its own endpoint class and request DTO.

Run the read-only variant example with `php examples/variant-list.php` after setting `DIGIKALA_ACCESS_TOKEN`.



## Orders
The project includes the health-check, refresh-token, category, and product routes explicitly represented by endpoint classes in `src/Endpoint/`. It also implements the four Order operations and 26 Product Variant/Variant Creation operations whose paths and schemas are documented in the supplied excerpt. Active-order and order-history items share `OrderDto`; the Variant response DTO is shared where the schema represents a variant.
The documented order operations are exposed as separate endpoint classes: active orders, order statistics, order history, and order-item cancellation. Active orders and history items share `OrderDto`; pagination, sorting, and response metadata remain available through `ApiResponse`.

```php
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderHistoryRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderListRequest;

$client = new MarketplaceClient(accessToken: getenv('DIGIKALA_ACCESS_TOKEN') ?: null);
$activeOrders = $client->orders()->list(new OrderListRequest(
    searchTerm: 'کفش سفید',
    createdToday: true,
));
$history = $client->orderHistory()->list(new OrderHistoryRequest(
    orderType: 'processed',
    shippingType: 'digikala',
));
$statistics = $client->orderStatistics()->get();
```

Run the read-only sample with `php examples/order-list.php` after setting `DIGIKALA_ACCESS_TOKEN`. Order cancellation is a destructive operation; call `orderCancellation()->cancel()` only after confirming the order-item ID and cancellation reason.


## Current endpoint coverage

The project includes the health-check, refresh-token, category, and product routes explicitly represented by endpoint classes in `src/Endpoint/`. It also implements all 26 documented Product Variant and Variant Creation operations in the supplied excerpt. Request DTOs and shared response DTOs are in `src/Dto/`; variant list, detail, creation, and edit responses that have a variant schema use the shared `ProductVariantDto`.

The supplied OpenAPI excerpt is partial: it names 275 operations, but does not include every operation's path, request schema, or response shape. This package therefore does not claim complete coverage of all 275 operations. Operations without a complete schema in the supplied excerpt are not inferred.
## License

MIT. See [LICENSE](LICENSE).
