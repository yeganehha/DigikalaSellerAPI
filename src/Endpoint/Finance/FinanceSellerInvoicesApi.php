<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceSellerInvoiceDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceSellerInvoicesRequest;

final class FinanceSellerInvoicesApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(FinanceSellerInvoicesRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request('GET', '/open-api/v1/finance/seller-invoices', query: $request->toQuery()),
            [FinanceSellerInvoiceDto::class, 'fromArray'],
        );
    }
}
