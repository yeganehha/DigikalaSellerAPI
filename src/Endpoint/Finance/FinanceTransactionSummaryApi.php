<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionSummaryDto;

final class FinanceTransactionSummaryApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(): FinanceTransactionSummaryDto
    {
        $payload = $this->client->request('GET', '/open-api/v1/finance/transactions/summary');
        $data = $payload['data'] ?? null;
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Finance transaction summary data must be an object.');
        }

        return FinanceTransactionSummaryDto::fromArray($data);
    }
}
