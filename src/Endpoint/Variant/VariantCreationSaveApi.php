<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantCreationRequest;

final class VariantCreationSaveApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function create(int $productId, VariantCreationRequest $request): ProductVariantDto
    {
        $payload = $this->client->request(
            'POST',
            '/open-api/v1/variants/creation/' . $productId,
            $request->toArray(),
        );
        $data = $payload['data'] ?? null;
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Variant creation response data must be an object.');
        }

        return ProductVariantDto::fromArray($data);
    }
}
