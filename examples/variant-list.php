<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantListRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantDto;
use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;

$accessToken = getenv('DIGIKALA_ACCESS_TOKEN');
if (!is_string($accessToken) || $accessToken === '') {
    fwrite(STDERR, "Set DIGIKALA_ACCESS_TOKEN in the environment.\n");
    exit(1);
}

$client = new MarketplaceClient(accessToken: $accessToken);

try {
    $response = $client->productVariants()->list(new ProductVariantListRequest(
        page: 1,
        size: 50,
        filters: ['active' => true],
    ));
    $items = is_array($response->data) && is_array($response->data['items'] ?? null)
        ? $response->data['items']
        : [];
    foreach ($items as $variant) {
        if (!$variant instanceof ProductVariantDto) {
            continue;
        }

        echo sprintf("%s\t%s\n", $variant->id ?? 'n/a', $variant->title ?? '');
    }
} catch (ApiException $exception) {
    fwrite(STDERR, 'Marketplace API request failed (HTTP ' . ($exception->statusCode ?? 'no response') . ').' . PHP_EOL);
    exit(1);
}
