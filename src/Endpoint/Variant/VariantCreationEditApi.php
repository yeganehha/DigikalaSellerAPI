<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantCreationEditRequest;

final class VariantCreationEditApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function update(
        int $productId,
        int $variantId,
        VariantCreationEditRequest $request,
        ?int $forceMarketplaceSellerId = null,
    ): ProductVariantDto {
        $query = $forceMarketplaceSellerId === null
            ? null
            : ['force_marketplace_seller_id' => $forceMarketplaceSellerId];
        $payload = $this->client->request(
            'PUT',
            '/open-api/v1/variants/creation/' . $productId . '/' . $variantId,
            $request->toArray(),
            query: $query,
        );
        $data = $payload['data'] ?? null;
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Variant edit response data must be an object.');
        }

        return ProductVariantDto::fromArray($data);
    }
}
