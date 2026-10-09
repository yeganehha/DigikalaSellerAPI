<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\OpenApi;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

final class Document
{
    private const METHODS = ['get', 'post', 'put', 'patch', 'delete', 'head', 'options', 'trace'];

    /** @var array<string, Operation> */
    private array $operations = [];

    public function __construct(private readonly array $source)
    {
        if (!is_array($source['paths'] ?? null)) {
            throw new InvalidArgumentException('The document must contain a paths mapping.');
        }
        foreach ($source['paths'] as $path => $item) {
            $item = $this->resolve($item);
            foreach (self::METHODS as $method) {
                if (!isset($item[$method])) {
                    continue;
                }
                $definition = $this->resolve($item[$method]);
                $id = $definition['operationId'] ?? Operation::methodName($method, $path);
                $name = Operation::methodName($method, $path);
                $tag = $definition['tags'][0] ?? 'General';
                $group = preg_replace('/\s*\([0-9]+\)$/', '', $tag) ?? $tag;
                $parameters = [];
                foreach (array_merge($item['parameters'] ?? [], $definition['parameters'] ?? []) as $parameter) {
                    $parameter = $this->resolve($parameter);
                    $parameters[$parameter['in'] . ':' . $parameter['name']] = $parameter;
                }
                if (isset($this->operations[$id])) {
                    throw new RuntimeException('Duplicate operation identifier: ' . $id);
                }
                $this->operations[$id] = new Operation(
                    $id,
                    $name,
                    strtoupper($method),
                    $path,
                    $group,
                    $definition,
                    array_values($parameters),
                    isset($definition['requestBody']) ? $this->resolve($definition['requestBody']) : null,
                );
            }
        }
        if ($this->operations === []) {
            throw new InvalidArgumentException('No HTTP operations were found.');
        }
    }

    public static function bundled(): self
    {
        static $document;

        return $document ??= self::fromFile(dirname(__DIR__, 2) . '/digikala-open-api-document.yml');
    }

    public static function fromFile(string $path): self
    {
        $source = Yaml::parseFile($path);
        if (!is_array($source)) {
            throw new InvalidArgumentException('The document must be a YAML mapping.');
        }

        return new self($source);
    }

    /** @return array<string, Operation> */
    public function operations(): array
    {
        return $this->operations;
    }

    public function operation(string $id): Operation
    {
        return $this->operations[$id] ?? throw new InvalidArgumentException('Unknown operation: ' . $id);
    }

    public function find(string $method, string $path): Operation
    {
        foreach ($this->operations as $operation) {
            if ($operation->method === strtoupper($method) && $operation->path === $path) {
                return $operation;
            }
        }
        throw new InvalidArgumentException('The method and path are not in the document.');
    }

    public function schema(array $schema): array
    {
        return $this->resolve($schema);
    }

    public function source(): array
    {
        return $this->source;
    }
    /** @return array<string, list<Operation>> */
    public function groups(): array
    {
        $groups = [];
        foreach ($this->operations as $operation) {
            $groups[$operation->group][] = $operation;
        }
        return $groups;
    }

    /** @return list<string> */
    public function webhookEventTypes(): array
    {
        $operation = $this->find('POST', '/open-api/v1/webhook/subscription');
        return $operation->requestBody['content']['application/json']['schema']['properties']['event_types']['items']['enum'] ?? [];
    }

    private function resolve(mixed $value): array
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException('Expected an OpenAPI mapping.');
        }
        $seen = [];
        while (isset($value['$ref'])) {
            $reference = $value['$ref'];
            if (!is_string($reference) || !str_starts_with($reference, '#/')) {
                throw new InvalidArgumentException('Only local OpenAPI references are supported.');
            }
            if (isset($seen[$reference]) || count($seen) >= 32) {
                throw new InvalidArgumentException('Circular or excessive OpenAPI references.');
            }
            $seen[$reference] = true;
            $value = $this->source;
            foreach (explode('/', substr($reference, 2)) as $segment) {
                $segment = str_replace(['~1', '~0'], ['/', '~'], rawurldecode($segment));
                if (!is_array($value) || !array_key_exists($segment, $value)) {
                    throw new InvalidArgumentException('Unresolved local OpenAPI reference.');
                }
                $value = $value[$segment];
            }
            if (!is_array($value)) {
                throw new InvalidArgumentException('A reference must resolve to a mapping.');
            }
        }

        return $value;
    }
}
