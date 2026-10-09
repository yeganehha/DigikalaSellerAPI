<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Webhook;

use InvalidArgumentException;

final readonly class WebhookEvent
{
    public function __construct(
        public string $type,
        public array $payload,
        public ?string $id = null,
    ) {
        if ($type === '' || strlen($type) > 200 || preg_match('/[\x00-\x1f]/', $type)) {
            throw new InvalidArgumentException('Invalid webhook event type.');
        }
        if ($id !== null && ($id === '' || strlen($id) > 500)) {
            throw new InvalidArgumentException('Invalid webhook event identifier.');
        }
    }
}
