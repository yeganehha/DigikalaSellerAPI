<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi;

use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;
use Yeganehha\DigikalaSellerOpenApi\Exception\TransportException;
use Yeganehha\DigikalaSellerOpenApi\Http\ApiResponse;
use Yeganehha\DigikalaSellerOpenApi\Http\RequestBuilder;
use Yeganehha\DigikalaSellerOpenApi\OpenApi\Document;
use Yeganehha\DigikalaSellerOpenApi\OpenApi\Operation;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

final class Client
{
    private readonly ClientInterface $http;
    private readonly Document $document;
    private readonly RequestBuilder $builder;

    public function __construct(
        private readonly Configuration $configuration = new Configuration(),
        ?ClientInterface $http = null,
        ?Document $document = null,
    ) {
        $this->http = $http ?? new HttpClient();
        $this->document = $document ?? Document::bundled();
        $this->builder = new RequestBuilder();
    }

    public function document(): Document
    {
        return $this->document;
    }

    public function authentication(): Authentication
    {
        return new Authentication($this);
    }

    public function webhooks(): WebhookSubscription
    {
        return new WebhookSubscription($this);
    }

    public function withAccessToken(#[\SensitiveParameter] string $token): self
    {
        return new self($this->configuration->withToken($token), $this->http, $this->document);
    }

    public function call(string $operationId, ?RequestOptions $options = null): ApiResponse
    {
        return $this->execute($this->document->operation($operationId), $options ?? new RequestOptions());
    }

    public function request(string $method, string $path, ?RequestOptions $options = null): ApiResponse
    {
        return $this->execute($this->document->find($method, $path), $options ?? new RequestOptions());
    }

    /**
     * Retrieve pages lazily. Only documented GET operations with a page parameter qualify.
     * @return \Generator<int, ApiResponse>
     */
    public function pages(string $operationId, ?RequestOptions $options = null, int $maxPages = 100): \Generator
    {
        $operation = $this->document->operation($operationId);
        $pageParameters = array_filter($operation->parameters(), static fn (array $parameter): bool =>
            ($parameter['in'] ?? null) === 'query' && ($parameter['name'] ?? null) === 'page');
        if ($operation->method !== 'GET' || $pageParameters === [] || $maxPages < 1) {
            throw new \InvalidArgumentException('Pagination requires a GET operation with page and a positive limit.');
        }
        $options ??= new RequestOptions();
        $start = $options->query['page'] ?? 1;
        if (!is_int($start) || $start < 1) {
            throw new \InvalidArgumentException('The starting page must be a positive integer.');
        }
        for ($offset = 0; $offset < $maxPages; ++$offset) {
            $page = $start + $offset;
            $response = $this->call($operationId, new RequestOptions(
                path: $options->path,
                query: array_replace($options->query, ['page' => $page]),
                body: $options->body,
                headers: $options->headers,
                multipart: $options->multipart,
                cookies: $options->cookies,
                contentType: $options->contentType,
            ));
            yield $page => $response;
            $total = $response->json()['data']['pager']['total_pages'] ?? null;
            if (!is_numeric($total)) {
                throw new RuntimeException('The response does not contain a usable total_pages value.');
            }
            if ($page >= (int) $total) {
                return;
            }
        }
        throw new RuntimeException('Pagination stopped at maxPages; narrow the query or raise the limit.');
    }

    private function execute(Operation $operation, RequestOptions $options): ApiResponse
    {
        $request = $this->builder->build($operation, $options, $this->configuration);
        try {
            $response = $this->http->send($request, [
                'timeout' => $this->configuration->timeout,
                'connect_timeout' => $this->configuration->connectTimeout,
                'http_errors' => false,
                'allow_redirects' => false,
                'verify' => true,
            ]);
        } catch (GuzzleException) {
            // Guzzle exceptions may contain a request with its bearer token.
            throw new TransportException('The API request failed or timed out. No automatic retry was performed.');
        }
        $result = new ApiResponse($response);
        if ($result->statusCode() < 200 || $result->statusCode() >= 300) {
            throw new ApiException($result);
        }
        $mediaType = strtolower(trim(explode(';', $result->header('Content-Type'))[0]));
        if ($mediaType !== '' && $mediaType !== 'application/json' && !str_ends_with($mediaType, '+json')) {
            return $result;
        }
        try {
            $failed = ($result->json()['status'] ?? null) === 'error';
        } catch (\UnexpectedValueException) {
            $failed = false;
        }
        if ($failed) {
            throw new ApiException($result);
        }
        return $result;
    }
}
