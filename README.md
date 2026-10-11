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
The documented order operations are exposed as separate endpoint classes: active orders, order statistics, order history, and order-item cancellation. Active-order and history items share `OrderDto`; pagination, sorting, and response metadata remain available through `ApiResponse`.

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


## BuyBox, invoices, and finance

The supplied excerpt includes 2 BuyBox, 4 Invoice, and 9 Finance paths. Each is exposed through its own endpoint class and `MarketplaceClient` factory:

- `GET /open-api/v1/pricing/buybox/price-suggestion/winning-price`
- `GET /open-api/v1/pricing/buybox/price-suggestion/nth-rank-winning-price`
- `GET /open-api/v1/invoices`
- `GET /open-api/v1/invoices/{invoice_id}/details`
- `GET /open-api/v1/invoices/{invoice_id}/items/{financial_notation_id}/{calculation_type}`
- `POST /open-api/v1/invoices/vat`
- `GET /open-api/v1/finance/overview/v2`
- `GET /open-api/v1/finance/transactions`
- `GET /open-api/v1/finance/transactions/rows`
- `GET /open-api/v1/finance/transactions/summary`
- `GET /open-api/v1/finance/transactions/{transaction_id}`
- `GET /open-api/v1/finance/payouts`
- `GET /open-api/v1/finance/payouts/{payout_id}`
- `GET /open-api/v1/finance/seller-invoices`
- `POST /open-api/v1/finance/transactions/export/excel`

Invoice list and detail share `InvoiceDto`. Finance list items have dedicated DTOs; `ApiResponse` retains pagination and metadata. Run the read-only example with the required environment variables set:

```bash
php examples/finance-read.php
```

`DIGIKALA_ACCESS_TOKEN` is required. `FINANCE_START_DATE` and `FINANCE_END_DATE` enable the optional overview request; `BUYBOX_PRODUCT_VARIANT_ID` enables the optional BuyBox suggestion request. Excel export and VAT calculation are not called by the sample.

## Current endpoint coverage

The documented coverage in this repository includes 4 Order operations, 26 Product Variant and Variant Creation operations, and 15 BuyBox, Invoice, and Finance operations, in addition to the other routes represented by endpoint classes under `src/Endpoint/`. Active-order and order-history items share `OrderDto`; invoice list and detail share `InvoiceDto`; list metadata is retained in `ApiResponse`.

The supplied OpenAPI excerpt is partial: it names 275 operations, but does not include every operation's path, request schema, or response shape. This package therefore does not claim complete coverage of all 275 operations. Operations without a complete schema in the supplied excerpt are not inferred.
## License

MIT. See [LICENSE](LICENSE).
