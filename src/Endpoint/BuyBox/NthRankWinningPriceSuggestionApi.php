<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Endpoint\BuyBox;

use UnexpectedValueException;
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\BuyBoxNthRankWinningPriceRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\BuyBoxPriceSuggestionDto;

final class NthRankWinningPriceSuggestionApi
{
    public function __construct(private readonly MarketplaceClient $client)
    {
    }

    public function get(BuyBoxNthRankWinningPriceRequest $request): BuyBoxPriceSuggestionDto
    {
        $payload = $this->client->request(
            'GET',
            '/open-api/v1/pricing/buybox/price-suggestion/nth-rank-winning-price',
            query: $request->toQuery(),
        );
        $data = $payload['data'] ?? null;
        if (!is_array($data)) {
            throw new UnexpectedValueException('BuyBox suggestion response data must be an object.');
        }

        return BuyBoxPriceSuggestionDto::fromArray($data);
    }
}
