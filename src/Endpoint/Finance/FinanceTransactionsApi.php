<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionsRequest;

final class FinanceTransactionsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(FinanceTransactionsRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request('GET', '/open-api/v1/finance/transactions', query: $request->toQuery()),
            [FinanceTransactionDto::class, 'fromArray'],
        );
    }
}
