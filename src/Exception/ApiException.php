<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Exception;

use Yeganehha\DigikalaSellerOpenApi\Http\ApiResponse;
use RuntimeException;

final class ApiException extends RuntimeException
{
    public function __construct(public readonly ApiResponse $response)
    {
        parent::__construct('Digikala API returned HTTP ' . $response->statusCode() . '.', $response->statusCode());
    }
    public function statusCode(): int
    {
        return $this->response->statusCode();
    }

    public function status(): int
    {
        return $this->statusCode();
    }

    public function errors(): array
    {
        return $this->validationErrors();
    }

    public function response(): ApiResponse
    {
        return $this->response;
    }

    public function validationErrors(): array
    {
        try {
            $errors = $this->response->json()['errors'] ?? [];
            return is_array($errors) ? $errors : [];
        } catch (RuntimeException) {
            return [];
        }
    }

    public function retryAfter(): ?string
    {
        $value = $this->response->header('Retry-After');
        return $value === '' ? null : $value;
    }
}
