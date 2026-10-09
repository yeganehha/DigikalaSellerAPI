# Digikala Open API for PHP

An **unofficial**, MIT-licensed Composer SDK for the Digikala Seller Marketplace Open API. Every HTTP operation in the bundled `digikala-open-api-document.yml` is callable through one document-backed client. No route allowlist is maintained by hand.

[راهنمای فارسی](README.fa.md) · [API usage](docs/api.md) · [Webhooks](docs/webhooks.md) · [Releasing](docs/publishing.md)

## Requirements

- PHP **8.3+**, Composer 2 and the JSON extension.
- An active Digikala client and seller permissions for production calls.
- HTTPS connectivity to the seller API. Sandbox access is separately configured; no undocumented sandbox URL is assumed.

Runtime dependencies: Guzzle 7 (HTTP/PSR-7, MIT) and Symfony YAML 7.4 (parsing, MIT). Development dependencies: PHPUnit 12 (BSD-3-Clause) and PHP-CS-Fixer 3 (MIT). No framework is required.

## Installation

After this repository has been published on Packagist:

```sh
composer require yeganehha/digikala-seller-open-api
```

Before publication, clone this repository and run `composer install`, or use a Composer `path`/VCS repository in the consuming application's configuration. The package name is metadata, not a claim that a Packagist listing already exists.

## Quick start

```php
use Yeganehha\DigikalaSellerOpenApi\Client;
use Yeganehha\DigikalaSellerOpenApi\Configuration;
use Yeganehha\DigikalaSellerOpenApi\RequestOptions;

$token = getenv('DK_ACCESS_TOKEN');
if (!is_string($token) || $token === '') {
    throw new RuntimeException('DK_ACCESS_TOKEN is required.');
}

$client = new Client(new Configuration(accessToken: $token));
$result = $client->call('getVariants', new RequestOptions(
    query: ['page' => 1, 'size' => 50, 'search[active]' => true],
));
$items = $result->data()['items'] ?? [];
```

Load Composer's `vendor/autoload.php` in the application's entry point. The default production origin matches the bundled server configuration: `https://seller.digikala.com`.

### Every documented API

Use a document operation ID, or an exact HTTP method and path template:

```php
$result = $client->request('PATCH', '/open-api/v1/variants/{variant_id}/seller-stock',
    new RequestOptions(path: ['variant_id' => 12345], body: ['seller_stock' => 10]),
);
```

This example changes stock; use an actual variant owned by the authorized seller. The same API works for GET, POST, PUT, PATCH, DELETE, multipart uploads and non-JSON responses. Path values are encoded, and nested/flat query filters are supported.

List the actual operations and their parameters:

```sh
composer api:list
php bin/api-reference.php --markdown > docs/api-reference.md
```

The supplied description declares 275 operations. The contract test independently enumerates every source method/path, compares that set with the SDK registry, checks unique IDs and sends each operation through a fake transport. The declared count is not assumed to be authoritative; inspect the generated catalogue before stating a verified release count.

### Tokens and subscriptions

```php
$tokens = $client->authentication()->exchange($decryptedAuthorizationCode)->data();
$authorized = $client->withAccessToken($tokens['access_token']);

$renewed = $authorized->authentication()->refresh(
    $tokens['access_token'], $tokens['refresh_token'],
)->data();

$authorized->webhooks()->subscribe(['product_variant_status_change']);
$authorized->webhooks()->activate(true);
```

The document requires a **decrypted** authorization code but does not specify its encryption algorithm or a complete authorization redirect flow. Obtain those details from your registered client's integration agreement. Do not guess cryptography. Store replacement refresh tokens atomically and securely; the SDK neither stores tokens nor automatically replays a failed write request.

## Webhook integration

Call `Receiver::serve()` inside an application's publicly routed PHP file. Load a local handler-registration file with `Router::fromFile()`. The receiver automatically authenticates each incoming request, resolves its event, dispatches the matching handlers and handles duplicates when a store is configured.

See [the complete example](examples/public/webhook.php) and [the webhook guide](docs/webhooks.md). Important boundaries:

- The bundled document defines subscription APIs and allowed event names, **not** the incoming envelope, signature protocol or callback-URL registration API.
- A sender verifier and event resolver are mandatory. Their contract must match your verified delivery configuration.
- The example requires a trusted gateway; `X-Webhook-Gateway-Token` is an **application-specific example**, not a Digikala header.
- Including a PHP file cannot make Digikala discover its public URL. Configure that URL with Digikala separately.
- No handler is loaded implicitly during Composer autoload, and no application directory is scanned or executed.

## Errors and reliability

`ApiException` exposes HTTP status, validation errors, the response and `Retry-After`; exception messages do not include tokens, URLs or response bodies. `TransportException` reports a transport failure without retaining a credential-bearing Guzzle exception. A response with `status: error` is treated as a failure even with HTTP 200.

TLS verification is enabled, redirects and automatic retries are disabled, and positive request/connect timeouts are enforced. Follow the service's rate limits. Retrying mutations requires application-level reconciliation/idempotency. Large/binary responses remain available as PSR-7 responses rather than being forcibly decoded.

## Samples

```sh
php examples/quickstart.php
DK_UPLOAD_FILE=/absolute/path/to/product-image.jpg php examples/upload.php
php examples/authentication.php
DK_WEBHOOK_EVENTS=product_variant_status_change php examples/subscribe.php
```

Configure the variables listed in `.env.example` in your environment. This library does not automatically load dotenv files. Authentication samples never print credentials. Subscription samples change seller subscriptions.

## Development and tests

```sh
composer install
composer validate --strict
composer lint
composer test
composer format:check
composer audit
```

`composer format` applies formatting. Tests use Guzzle MockHandler and temporary local storage, never production. CI runs PHP 8.3, 8.4 and 8.5, checks low and normal dependency resolutions, tests, syntax, style and a Composer security audit.

Generated API coverage does not prove live-server interoperability or request/response semantics. Incoming webhook compatibility must be verified against a real agreed payload before deployment. Checks must be run successfully in your PHP environment before release; creating test files does not mean they have been executed.

## Support and contribution

See [CONTRIBUTING](CONTRIBUTING.md), [SECURITY](SECURITY.md) and [release instructions](docs/publishing.md). Use the repository's private security-reporting channel for vulnerabilities; do not include tokens or seller data in public issues. This SDK is not endorsed by Digikala.

## License

[MIT](LICENSE), copyright (c) 2026 Erfan Ebrahimi. The original document and license are preserved. Confirm that you have redistribution rights for the supplied API document before a public release; this SDK does not grant rights to Digikala trademarks or access to its services.
