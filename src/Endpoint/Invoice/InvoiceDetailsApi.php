<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Invoice;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceDto;

final class InvoiceDetailsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(int $invoiceId): InvoiceDto
    {
        if ($invoiceId < 1) {
            throw new \InvalidArgumentException('Invoice ID must be positive.');
        }

        $payload = $this->client->request('GET', '/open-api/v1/invoices/' . $invoiceId . '/details');
        $data = $payload['data'] ?? null;
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Invoice detail response data must be an object.');
        }

        return InvoiceDto::fromArray($data);
    }
}
