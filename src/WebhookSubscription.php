<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi;

use Yeganehha\DigikalaSellerOpenApi\Http\ApiResponse;

final readonly class WebhookSubscription
{
    public function __construct(private Client $client)
    {
    }

    public function eventTypes(): ApiResponse
    {
        return $this->client->request('GET', '/open-api/v1/webhook/event-types');
    }

    /** @param list<string> $eventTypes */
    public function subscribe(array $eventTypes): ApiResponse
    {
        return $this->client->request('POST', '/open-api/v1/webhook/subscription',
            new RequestOptions(body: ['event_types' => $eventTypes]));
    }

    /** @param list<string> $eventTypes */
    public function unsubscribe(array $eventTypes): ApiResponse
    {
        return $this->client->request('POST', '/open-api/v1/webhook/unsubscription',
            new RequestOptions(body: ['event_types' => $eventTypes]));
    }

    public function activate(bool $activation = true): ApiResponse
    {
        return $this->client->request('POST', '/open-api/v1/webhook/subscription/change-activation',
            new RequestOptions(body: ['activation' => $activation]));
    }
}
