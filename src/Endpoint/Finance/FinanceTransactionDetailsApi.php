<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionDetailDto;

final class FinanceTransactionDetailsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(int $transactionId): FinanceTransactionDetailDto
    {
        if ($transactionId < 1) {
            throw new \InvalidArgumentException('Transaction ID must be positive.');
        }
        $payload = $this->client->request('GET', '/open-api/v1/finance/transactions/' . $transactionId);
        $data = $payload['data'] ?? null;
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Finance transaction detail data must be an object.');
        }

        return FinanceTransactionDetailDto::fromArray($data);
    }
}
