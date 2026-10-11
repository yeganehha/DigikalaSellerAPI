<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantEditRequest;

final class ProductVariantUpdateApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function update(int $variantId, ProductVariantEditRequest $request): ProductVariantDto
    {
        $payload = $this->client->request(
            'PUT',
            '/open-api/v1/variants/' . $variantId,
            $request->toArray(),
        );
        $data = $payload['data'] ?? null;
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Variant update response data must be an object.');
        }

        return ProductVariantDto::fromArray($data);
    }
}
