<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Http;

use Yeganehha\DigikalaSellerOpenApi\Configuration;
use Yeganehha\DigikalaSellerOpenApi\OpenApi\InputValidator;
use Yeganehha\DigikalaSellerOpenApi\OpenApi\Operation;
use Yeganehha\DigikalaSellerOpenApi\RequestOptions;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

final class RequestBuilder
{
    public function build(Operation $operation, RequestOptions $options, Configuration $configuration): RequestInterface
    {
        if ($options->multipart !== []) {
            if ($options->body !== null) {
                throw new InvalidArgumentException('Use body or multipart, not both.');
            }
            $options = new RequestOptions(
                path: $options->path,
                query: $options->query,
                body: $options->multipart,
                headers: $options->headers,
                cookies: $options->cookies,
                contentType: 'multipart/form-data',
            );
        }
        if ($configuration->validateInputs) {
            (new InputValidator())->validate($operation, $options);
        }
        $path = preg_replace_callback('/\{([^}]+)\}/', static function (array $match) use ($options): string {
            $value = $options->path[$match[1]] ?? null;
            if ((!is_string($value) && !is_int($value)) || (string) $value === '') {
                throw new InvalidArgumentException('Missing or invalid path parameter: ' . $match[1]);
            }
            if (in_array((string) $value, ['.', '..'], true)) {
                throw new InvalidArgumentException('Dot segments are not permitted in path parameters.');
            }
            return rawurlencode((string) $value);
        }, $operation->path);
        $uri = rtrim($configuration->baseUri, '/') . $path;
        $query = http_build_query($options->query, '', '&', PHP_QUERY_RFC3986);
        if ($query !== '') {
            $uri .= '?' . $query;
        }
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'digikala-open-api-php',
        ];
        if ($configuration->accessToken !== null) {
            $headers['Authorization'] = 'Bearer ' . $configuration->accessToken;
        }
        foreach ($options->headers as $name => $value) {
            if (!is_string($name) || preg_match('/[\r\n]/', $name)) {
                throw new InvalidArgumentException('Invalid request header.');
            }
            if (in_array(strtolower($name), ['host', 'authorization', 'content-length', 'content-type'], true)) {
                throw new InvalidArgumentException('Use client configuration or contentType for protected headers.');
            }
            foreach ((array) $value as $item) {
                if (!is_string($item) || preg_match('/[\r\n]/', $item)) {
                    throw new InvalidArgumentException('Invalid request header value.');
                }
            }
            $headers[$name] = $value;
        }
        if ($options->cookies !== []) {
            $cookies = [];
            foreach ($options->cookies as $name => $value) {
                if (!is_string($name) || !is_string($value)
                    || preg_match('/[\x00-\x20\x7f;,=]/', $name . $value)) {
                    throw new InvalidArgumentException('Invalid request cookie.');
                }
                $cookies[] = $name . '=' . rawurlencode($value);
            }
            $headers['Cookie'] = implode('; ', $cookies);
        }
        $body = null;
        if ($options->body !== null) {
            $content = $operation->requestBody['content'] ?? [];
            $type = $options->contentType ?? array_key_first($content);
            if (!is_string($type) || !array_key_exists($type, $content)) {
                throw new InvalidArgumentException('Request content type is not documented for this operation.');
            }
            if ($type === 'multipart/form-data') {
                if (!is_array($options->body) || !array_is_list($options->body)) {
                    throw new InvalidArgumentException('Multipart body must be a list of Guzzle multipart parts.');
                }
                $names = [];
                foreach ($options->body as $part) {
                    if (!is_array($part) || !isset($part['name']) || !array_key_exists('contents', $part)) {
                        throw new InvalidArgumentException('Every multipart part requires name and contents.');
                    }
                    $names[] = $part['name'];
                }
                foreach ((array) ($content[$type]['schema']['required'] ?? []) as $required) {
                    if (!in_array($required, $names, true)) {
                        throw new InvalidArgumentException('Missing required multipart field: ' . $required);
                    }
                }
                $body = new MultipartStream($options->body);
                $headers['Content-Type'] = $type . '; boundary=' . $body->getBoundary();
            } elseif ($type === 'application/json' || str_ends_with($type, '+json')) {
                $schemaType = $content[$type]['schema']['type'] ?? null;
                $value = $options->body;
                if ($schemaType === 'object' && is_array($value)) {
                    $value = (object) $value;
                }
                $body = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                $headers['Content-Type'] = $type;
            } elseif ($type === 'application/x-www-form-urlencoded' && is_array($options->body)) {
                $body = http_build_query($options->body, '', '&', PHP_QUERY_RFC3986);
                $headers['Content-Type'] = $type;
            } else {
                if (!is_string($options->body) && !is_resource($options->body)) {
                    throw new InvalidArgumentException('This content type requires a string or stream body.');
                }
                $body = $options->body;
                $headers['Content-Type'] = $type;
            }
        } else {
            $headers['Content-Type'] = 'application/json';
        }
        return new Request($operation->method, $uri, $headers, $body);
    }
}
