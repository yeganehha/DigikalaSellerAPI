<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Client;

use Yeganehha\DigikalaSellerOpenApi\Endpoint\Auth\RefreshTokenApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\HealthCheckApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Category\CategoryTreeApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\AutoTitleSaveApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\AutoTitleSuggestionApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\BrandRequestApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\BrandsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\CategoryAttributesApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\CategorySearchApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\CategoryValidationApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\DraftProductApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\DraftProductCountApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\DraftProductsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductCreationSaveApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductDetailsValidationApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductEditApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductEditAutoTitleApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductPublishApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductScoreApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductSearchApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductSearchSuggestionApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductSellerDetailsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\SellerProductsApi;
use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductImageUploadApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\BrandLogoUploadApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ContentRequestImageUploadApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductImageAiApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\AttributesValidationApi;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use InvalidArgumentException;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductAssignApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Product\ProductCreationAssignApi;

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

    public function categories(): CategoryTreeApi
    {
        return new CategoryTreeApi($this);
    }

    public function productSearch(): ProductSearchApi
    {
        return new ProductSearchApi($this);
    }

    public function productSearchSuggestions(): ProductSearchSuggestionApi
    {
        return new ProductSearchSuggestionApi($this);
    }

    public function productSellerDetails(): ProductSellerDetailsApi
    {
        return new ProductSellerDetailsApi($this);
    }

    public function categorySearch(): CategorySearchApi
    {
        return new CategorySearchApi($this);
    }

    public function categoryValidation(): CategoryValidationApi
    {
        return new CategoryValidationApi($this);
    }

    public function productDetailsValidation(): ProductDetailsValidationApi
    {
        return new ProductDetailsValidationApi($this);
    }

    public function draftProductCount(): DraftProductCountApi
    {
        return new DraftProductCountApi($this);
    }

    public function draftProduct(): DraftProductApi
    {
        return new DraftProductApi($this);
    }

    public function autoTitleSuggestion(): AutoTitleSuggestionApi
    {
        return new AutoTitleSuggestionApi($this);
    }

    public function autoTitleSave(): AutoTitleSaveApi
    {
        return new AutoTitleSaveApi($this);
    }

    public function categoryAttributes(): CategoryAttributesApi
    {
        return new CategoryAttributesApi($this);
    }

    public function attributesValidation(): AttributesValidationApi
    {
        return new AttributesValidationApi($this);
    }

    public function brands(): BrandsApi
    {
        return new BrandsApi($this);
    }

    public function draftProducts(): DraftProductsApi
    {
        return new DraftProductsApi($this);
    }

    public function sellerProducts(): SellerProductsApi
    {
        return new SellerProductsApi($this);
    }

    public function productScore(): ProductScoreApi
    {
        return new ProductScoreApi($this);
    }

    public function productEdit(): ProductEditApi
    {
        return new ProductEditApi($this);
    }

    public function productPublish(): ProductPublishApi
    {
        return new ProductPublishApi($this);
    }

    public function productEditAutoTitle(): ProductEditAutoTitleApi
    {
        return new ProductEditAutoTitleApi($this);
    }

    public function productAssign(): ProductAssignApi
    {
        return new ProductAssignApi($this);
    }

    public function productCreationSave(): ProductCreationSaveApi
    {
        return new ProductCreationSaveApi($this);
    }
    public function productCreationAssign(): ProductCreationAssignApi
    {
        return new ProductCreationAssignApi($this);
    }



    public function brandRequest(): BrandRequestApi
    {
        return new BrandRequestApi($this);
    }

    public function productImageUpload(): ProductImageUploadApi
    {
        return new ProductImageUploadApi($this);
    }

    public function brandLogoUpload(): BrandLogoUploadApi
    {
        return new BrandLogoUploadApi($this);
    }

    public function contentRequestImageUpload(): ContentRequestImageUploadApi
    {
        return new ContentRequestImageUploadApi($this);
    }

    public function productImageAi(): ProductImageAiApi
    {
        return new ProductImageAiApi($this);
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
        ?array $query = null,
        ?array $multipart = null,
    ): array {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => $contentType,
        ];
        $token = $bearerToken ?? $this->accessToken;
        if ($token !== null && $token !== '') {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        $options = [
            'headers' => $headers,
            'http_errors' => false,
            'timeout' => $this->timeout,
        ];
        if ($query !== null) {
            $options['query'] = $query;
        }
        if ($multipart !== null) {
            unset($headers['Content-Type']);
            $options['headers'] = $headers;
            $options['multipart'] = $multipart;
        } elseif ($json !== null) {
            $options['json'] = $json;
        }

        try {
            $response = $this->httpClient->request($method, $this->baseUri . $path, $options);
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
