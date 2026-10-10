<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Exception;

use RuntimeException;
use Throwable;

final class ApiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
