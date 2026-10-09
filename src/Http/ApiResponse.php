<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Http;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

final readonly class ApiResponse
{
    public function __construct(public ResponseInterface $response)
    {
    }
    public function status(): int
    {
        return $this->statusCode();
    }

    public function httpResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function statusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    public function json(): array
    {
        try {
            $decoded = json_decode($this->body(), true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        } catch (JsonException) {
            throw new UnexpectedValueException('The API response is not valid JSON. Use body() for binary responses.');
        }
        if (!is_array($decoded)) {
            throw new UnexpectedValueException('The API response must contain a JSON object or array.');
        }
        return $decoded;
    }

    public function data(): mixed
    {
        return $this->json()['data'] ?? null;
    }

    public function header(string $name): string
    {
        return $this->response->getHeaderLine($name);
    }
}
