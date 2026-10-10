<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class IdRequest
{
    public function __construct(public readonly int $id)
    {
        if ($this->id < 1) {
            throw new InvalidArgumentException('ID must be a positive integer.');
        }
    }
}
