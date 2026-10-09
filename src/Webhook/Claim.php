<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Webhook;

enum Claim
{
    case Acquired;
    case Duplicate;
    case Busy;
}
