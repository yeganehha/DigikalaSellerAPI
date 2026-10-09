<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Webhook;

interface IdempotencyStore
{
    /** Atomically claim processing ownership; Busy and Duplicate must not execute handlers. */
    public function claim(string $key): Claim;

    public function complete(string $key): void;

    public function release(string $key): void;
}
