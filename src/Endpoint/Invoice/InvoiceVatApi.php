<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Invoice;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceVatRequest;

final class InvoiceVatApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function calculate(InvoiceVatRequest $request): ApiResponse
    {
        return ApiResponse::fromArray($this->client->request(
            'POST',
            '/open-api/v1/invoices/vat',
            $request->toArray(),
        ));
    }
}
