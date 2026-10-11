<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\Invoice;

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceItemDto;
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceItemsRequest;

final class InvoiceItemsApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function list(
        int $invoiceId,
        int $financialNotationId,
        string $calculationType,
        InvoiceItemsRequest $request,
    ): ApiResponse {
        if ($invoiceId < 1 || $financialNotationId < 1 || trim($calculationType) === '') {
            throw new \InvalidArgumentException('Invoice path parameters must be valid.');
        }

        $query = $request->toQuery();

        return ApiResponse::fromArray(
            $this->client->request(
                'GET',
                sprintf('/open-api/v1/invoices/%d/items/%d/%s', $invoiceId, $financialNotationId, rawurlencode($calculationType)),
                query: $query,
            ),
            [InvoiceItemDto::class, 'fromArray'],
        );
    }
}
