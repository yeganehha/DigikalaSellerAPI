<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Invoice;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceListRequest;

final class InvoicesApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(InvoiceListRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request('GET', '/open-api/v1/invoices', query: $request->toQuery()),
            [InvoiceDto::class, 'fromArray'],
        );
    }
}
