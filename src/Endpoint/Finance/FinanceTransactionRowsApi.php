<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionRowDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionRowsRequest;

final class FinanceTransactionRowsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(FinanceTransactionRowsRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request('GET', '/open-api/v1/finance/transactions/rows', query: $request->toQuery()),
            [FinanceTransactionRowDto::class, 'fromArray'],
        );
    }
}
