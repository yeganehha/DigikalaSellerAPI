<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests\Support;

use Yeganehha\DigikalaSellerOpenApi\OpenApi\Operation;
use Yeganehha\DigikalaSellerOpenApi\RequestOptions;

final class ContractOptions
{
    public static function forOperation(Operation $operation): RequestOptions
    {
        $path = [];
        preg_match_all('/\{([^}]+)\}/', $operation->path, $matches);
        foreach ($matches[1] as $name) {
            $path[$name] = 123;
        }
        $query = [];
        $headers = [];
        foreach ($operation->parameters as $parameter) {
            if (!in_array($parameter['required'] ?? false, [true, 'true'], true)) {
                continue;
            }
            $name = $parameter['name'];
            $value = self::value($parameter['schema'] ?? []);
            if ($parameter['in'] === 'path') {
                $path[$name] = ($parameter['schema']['type'] ?? null) === 'string' ? '123' : 123;
            }
            if ($parameter['in'] === 'query') {
                $query[$name] = $value;
            }
            if ($parameter['in'] === 'header' && strtolower($name) !== 'content-type') {
                $headers[$name] = (string) $value;
            }
        }
        $content = $operation->requestBody['content'] ?? [];
        $body = null;
        $multipart = [];
        if (isset($content['multipart/form-data'])) {
            $schema = $content['multipart/form-data']['schema'] ?? [];
            foreach ($schema['properties'] ?? [] as $name => $property) {
                $multipart[] = ['name' => (string) $name, 'contents' => 'contract-upload'];
            }
        } elseif ($content !== []) {
            $schema = $content[array_key_first($content)]['schema'] ?? [];
            $body = self::value($schema);
        }
        return new RequestOptions(path: $path, query: $query, body: $body, headers: $headers, multipart: $multipart);
    }

    private static function value(array $schema, int $depth = 0): mixed
    {
        if ($depth > 20) {
            throw new \RuntimeException('Unexpected recursive contract schema.');
        }
        if (isset($schema['enum']) && $schema['enum'] !== []) {
            return $schema['enum'][0];
        }
        $type = $schema['type'] ?? 'object';
        if (is_array($type)) {
            $type = $type[0] ?? 'string';
        }
        if ($type === 'object') {
            $value = [];
            foreach ($schema['required'] ?? [] as $name) {
                if (!is_string($name)) {
                    continue;
                }
                $value[$name] = self::value($schema['properties'][$name] ?? [], $depth + 1);
            }
            return $value;
        }
        if ($type === 'array') {
            $minimum = max(1, (int) ($schema['minItems'] ?? 1));
            return array_fill(0, $minimum, self::value($schema['items'] ?? [], $depth + 1));
        }
        if (in_array($type, ['integer', 'number', 'float', 'double'], true)) {
            return max(1, $schema['minimum'] ?? 1);
        }
        if (in_array($type, ['boolean', 'bool'], true)) {
            return true;
        }
        return 'contract-value';
    }
}
