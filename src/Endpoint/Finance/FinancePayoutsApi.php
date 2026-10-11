<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinancePayoutDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinancePayoutListRequest;

final class FinancePayoutsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(FinancePayoutListRequest $request): ApiResponse
    {
        return ApiResponse::fromArray(
            $this->client->request('GET', '/open-api/v1/finance/payouts', query: $request->toQuery()),
            [FinancePayoutDto::class, 'fromArray'],
        );
    }
}
