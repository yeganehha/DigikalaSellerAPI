<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Product;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\UploadImageRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;

final class BrandLogoUploadApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function upload(UploadImageRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'POST',
            '/open-api/v1/product-creation/images/requests/brand-logo/upload',
            multipart: $request->toMultipart(),
        ));
    }
}
