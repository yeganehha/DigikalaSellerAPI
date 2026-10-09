<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\OpenApi;

final readonly class Operation
{
    public function __construct(
        public string $id,
        public string $name,
        public string $method,
        public string $path,
        public string $group,
        public array $definition,
        public array $parameters,
        public ?array $requestBody,
    ) {
    }
    /** @return list<array<string, mixed>> */
    public function parameters(): array
    {
        return $this->parameters;
    }

    public static function methodName(string $method, string $path): string
    {
        $path = preg_replace('~^/open-api/v[0-9]+/?~', '', $path) ?? $path;
        $parts = [];
        foreach (explode('/', trim($path, '/')) as $part) {
            if ($part === '') {
                continue;
            }
            if (preg_match('/^\{([^}]+)\}$/', $part, $matches) === 1) {
                $part = 'by_' . $matches[1];
            }
            $parts[] = str_replace(' ', '', ucwords(preg_replace('/[^a-zA-Z0-9]+/', ' ', $part) ?? $part));
        }

        return strtolower($method) . ($parts === [] ? 'Service' : implode('', $parts));
    }
}
