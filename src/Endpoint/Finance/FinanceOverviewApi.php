<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceDateRangeRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceOverviewDto;

final class FinanceOverviewApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(FinanceDateRangeRequest $request): FinanceOverviewDto
    {
        $payload = $this->client->request(
            'GET',
            '/open-api/v1/finance/overview/v2',
            query: $request->toQuery(),
        );
        $data = $payload['data'] ?? null;
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Finance overview response data must be an object.');
        }

        return FinanceOverviewDto::fromArray($data);
    }
}
