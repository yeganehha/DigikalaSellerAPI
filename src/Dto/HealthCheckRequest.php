<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class HealthCheckRequest
{
    public function __construct(
        public readonly string $contentType = 'application/json',
    ) {
        if (trim($this->contentType) === '') {
            throw new InvalidArgumentException('Content type must not be empty.');
        }
    }
}
