<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantDto;

final class ProductVariantDetailsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(int $variantId): ProductVariantDto
    {
        $payload = $this->client->request('GET', '/open-api/v1/variants/' . $variantId);
        $data = $payload['data'] ?? null;

        return ProductVariantDto::fromArray(is_array($data) ? $data : $payload);
    }
}
