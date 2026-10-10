<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

/** A lossless response DTO for endpoints whose OpenAPI schema is incomplete. */
final class ApiResponse
{
    /** @param array<string, mixed> $payload */
    public function __construct(
        public readonly string|int|null $status,
        public readonly mixed $data,
        public readonly array $payload,
    ) {
    }

    /** @param array<string, mixed> $payload @param callable(array<string, mixed>): mixed|null $itemMapper */
    public static function fromArray(array $payload, ?callable $itemMapper = null): self
    {
        $status = $payload['status'] ?? null;
        if ($status !== null && !is_string($status) && !is_int($status)) {
            throw new UnexpectedValueException('API response status has an invalid type.');
        }

        $data = $payload['data'] ?? null;
        if ($itemMapper !== null && is_array($data) && is_array($data['items'] ?? null)) {
            $data['items'] = array_map($itemMapper, array_values(array_filter($data['items'], 'is_array')));
        }

        return new self($status, $data, $payload);
    }
}
