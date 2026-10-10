<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Client;

use Yeganehha\DigikalaSellerOpenApi\Endpoint\Auth\RefreshTokenApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\HealthCheckApi;
use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;

final class MarketplaceClient
{
    private readonly ClientInterface $httpClient;
    private readonly string $baseUri;
    private readonly ?string $accessToken;
    private readonly float $timeout;

    public function __construct(
        ?string $accessToken = null,
        string $baseUri = 'https://seller.digikala.com',
        float $timeout = 15.0,
        ?ClientInterface $httpClient = null,
    ) {
        if ($timeout <= 0) {
            throw new InvalidArgumentException('Timeout must be greater than zero.');
        }

        $normalizedBaseUri = rtrim($baseUri, '/');
        if (filter_var($normalizedBaseUri, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Base URI must be a valid absolute URL.');
        }

        $this->baseUri = $normalizedBaseUri;
        $this->accessToken = $accessToken;
        $this->timeout = $timeout;
        $this->httpClient = $httpClient ?? new Client();
    }

    public function healthCheck(): HealthCheckApi
    {
        return new HealthCheckApi($this);
    }

    public function refreshToken(): RefreshTokenApi
    {
        return new RefreshTokenApi($this);
    }

    /**
     * @param array<string, mixed>|null $json
     * @return array<string, mixed>
     *
     * @throws ApiException
     */
    public function request(
        string $method,
        string $path,
        ?array $json = null,
        ?string $bearerToken = null,
        string $contentType = 'application/json',
    ): array {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => $contentType,
        ];
        $token = $bearerToken ?? $this->accessToken;
        if ($token !== null && $token !== '') {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        try {
            $response = $this->httpClient->request($method, $this->baseUri . $path, [
                'headers' => $headers,
                'http_errors' => false,
                'timeout' => $this->timeout,
                'json' => $json,
            ]);
        } catch (\Throwable $exception) {
            throw new ApiException('Marketplace API request failed before a response was received.', null, $exception);
        }

        $statusCode = $response->getStatusCode();
        $responseBody = (string) $response->getBody();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new ApiException(
                sprintf('Marketplace API request failed with HTTP status %d.', $statusCode),
                $statusCode,
            );
        }

        if (trim($responseBody) === '') {
            return [];
        }

        try {
            $decoded = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ApiException('Marketplace API returned invalid JSON.', $statusCode, $exception);
        }

        if (!is_array($decoded)) {
            throw new ApiException('Marketplace API returned an unexpected JSON value.', $statusCode);
        }

        return $decoded;
    }
}
