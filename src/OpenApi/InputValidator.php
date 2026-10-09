<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\OpenApi;

use Yeganehha\DigikalaSellerOpenApi\RequestOptions;
use InvalidArgumentException;

final class InputValidator
{
    public function validate(Operation $operation, RequestOptions $options): void
    {
        foreach ($operation->parameters() as $parameter) {
            $location = $parameter['in'] ?? '';
            $name = (string) ($parameter['name'] ?? '');
            $required = in_array($parameter['required'] ?? false, [true, 'true'], true);
            $values = match ($location) {
                'path' => $options->path,
                'query' => $this->flattenQuery($options->query),
                'header' => array_change_key_case($options->headers, CASE_LOWER),
                default => [],
            };
            if ($location === 'header') {
                $name = strtolower($name);
                if (in_array($name, ['content-type', 'accept', 'authorization'], true)) {
                    continue;
                }
            }
            if ($required && (!array_key_exists($name, $values) || $values[$name] === null)) {
                throw new InvalidArgumentException(sprintf('Missing required %s parameter: %s.', $location, $name));
            }
            if (array_key_exists($name, $values)) {
                $this->value($values[$name], $parameter['schema'] ?? [], $name);
            }
        }

        $requestBody = $operation->requestBody;
        if ($options->body === null) {
            if (in_array($requestBody['required'] ?? false, [true, 'true'], true)) {
                throw new InvalidArgumentException('This operation requires a request body.');
            }
            return;
        }
        if (!is_array($requestBody)) {
            throw new InvalidArgumentException('This operation does not document a request body.');
        }
        $contentType = $options->contentType ?? array_key_first($requestBody['content'] ?? []);
        $schema = $requestBody['content'][$contentType]['schema'] ?? [];
        if ($contentType !== 'multipart/form-data') {
            $this->value($options->body, $schema, 'body');
        }
    }
    /** @return array<string, mixed> */
    private function flattenQuery(array $query, string $prefix = ''): array
    {
        $result = [];
        foreach ($query as $key => $value) {
            $name = $prefix === '' ? (string) $key : $prefix . '[' . $key . ']';
            $result[$name] = $value;
            if (is_array($value)) {
                $result = array_replace($result, $this->flattenQuery($value, $name));
            }
        }
        return $result;
    }

    private function value(mixed $value, array $schema, string $name): void
    {
        if ($value === null && ($schema['nullable'] ?? false) === true) {
            return;
        }
        if (is_array($schema['enum'] ?? null) && !in_array($value, $schema['enum'], true)) {
            throw new InvalidArgumentException(sprintf('Invalid allowed value for %s.', $name));
        }
        $type = $schema['type'] ?? null;
        // Nonstandard upstream types are deliberately left to server-side validation.
        $valid = match ($type) {
            'integer' => is_int($value),
            'number', 'double', 'float' => is_int($value) || is_float($value),
            'boolean', 'bool' => is_bool($value),
            'string' => is_string($value),
            'array' => is_array($value) && array_is_list($value),
            'object' => is_array($value) || $value instanceof \stdClass,
            default => true,
        };
        if (!$valid) {
            throw new InvalidArgumentException(sprintf('Invalid type for %s.', $name));
        }
        if (is_array($value) || $value instanceof \stdClass) {
            $values = (array) $value;
            foreach ($type !== 'array' && is_array($schema['required'] ?? null) ? $schema['required'] : [] as $required) {
                if (!array_key_exists($required, $values) || $values[$required] === null) {
                    throw new InvalidArgumentException(sprintf('Missing required field: %s.%s.', $name, $required));
                }
            }
            foreach ($schema['properties'] ?? [] as $property => $childSchema) {
                if (array_key_exists($property, $values)) {
                    $this->value($values[$property], $childSchema, $name . '.' . $property);
                }
            }
            if ($type === 'array' && is_array($schema['items'] ?? null)) {
                foreach ($values as $index => $item) {
                    $this->value($item, $schema['items'], $name . '.' . $index);
                }
            }
        }
    }
}
