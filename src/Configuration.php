<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi;

use InvalidArgumentException;

final readonly class Configuration
{
    public string $baseUri;

    public function __construct(
        public ?string $accessToken = null,
        string $baseUri = 'https://seller.digikala.com',
        public float $timeout = 30.0,
        public float $connectTimeout = 10.0,
        bool $allowInsecureHttp = false,
        public bool $validateInputs = true,
    ) {
        $parts = parse_url($baseUri);
        $schemes = $allowInsecureHttp ? ['https', 'http'] : ['https'];
        if (
            !is_array($parts)
            || !in_array($parts['scheme'] ?? '', $schemes, true)
            || empty($parts['host'])
            || isset($parts['user'], $parts['pass'])
            || isset($parts['user'])
            || isset($parts['query'])
            || isset($parts['fragment'])
            || !in_array($parts['path'] ?? '', ['', '/'], true)
            || preg_match('/[\x00-\x20\x7f]/', $baseUri)
        ) {
            throw new InvalidArgumentException('Base URI must be a trusted HTTP(S) origin without credentials, path, query or fragment.');
        }
        if (!is_finite($timeout) || !is_finite($connectTimeout) || $timeout <= 0 || $connectTimeout <= 0) {
            throw new InvalidArgumentException('Timeouts must be finite positive seconds.');
        }
        if ($accessToken !== null && ($accessToken === '' || preg_match('/[\x00-\x20\x7f]/', $accessToken))) {
            throw new InvalidArgumentException('Access token must be nonempty and contain no whitespace or control characters.');
        }
        $this->baseUri = rtrim($baseUri, '/');
    }

    public function withToken(string $token): self
    {
        return new self(
            $token,
            $this->baseUri,
            $this->timeout,
            $this->connectTimeout,
            str_starts_with($this->baseUri, 'http://'),
            $this->validateInputs,
        );
    }
}
