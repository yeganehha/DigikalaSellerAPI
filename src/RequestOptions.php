<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi;

final readonly class RequestOptions
{
    /**
     * @param array<string, int|string> $path
     * @param array<string, mixed> $query
     * @param array<string, string|string[]> $headers
     * @param array<string, string> $cookies
     * @param array<int, array<string, mixed>> $multipart Guzzle multipart parts; streams stay caller-owned.
     */
    public function __construct(
        public array $path = [],
        public array $query = [],
        public mixed $body = null,
        public array $headers = [],
        public array $cookies = [],
        public array $multipart = [],
        public ?string $contentType = null,
    ) {
    }
}
