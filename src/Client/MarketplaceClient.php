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
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Order\OrdersApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Order\OrderStatisticsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Order\OrderHistoryApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Order\OrderCancellationApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\BuyBox\WinningPriceSuggestionApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\BuyBox\NthRankWinningPriceSuggestionApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Invoice\InvoicesApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Invoice\InvoiceDetailsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Invoice\InvoiceItemsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Invoice\InvoiceVatApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance\FinanceOverviewApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance\FinanceTransactionsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance\FinanceTransactionRowsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance\FinanceTransactionSummaryApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance\FinanceTransactionDetailsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance\FinancePayoutsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance\FinancePayoutDetailsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance\FinanceSellerInvoicesApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Finance\FinanceTransactionsExportApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantUpdateApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantB2bActivationApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantExportApi;

use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantPriceCalculatorApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantActivationApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantArchiveApi;

use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantSellingPriceApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantCreationConfigApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantCreationSaveApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantCreationEditApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantProductImagesApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantModerationImagesApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantImageRejectionReasonsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantSizeTypesApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantSizeRequestApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantColorRequestApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantWarrantyRequestApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\VariantThemeValuesApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantDetailsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantGoldDetailsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantGoldUpdateApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantStockDetailsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantStockUpdateApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantB2bPricesDetailsApi;
use Yeganehha\DigikalaSellerOpenApi\Endpoint\Variant\ProductVariantB2bPricesUpdateApi;

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
    public function productVariantDetails(): ProductVariantDetailsApi
    {
        return new ProductVariantDetailsApi($this);
    }

    public function productVariants(): ProductVariantsApi
    {
        return new ProductVariantsApi($this);
    }


    public function productVariantUpdate(): ProductVariantUpdateApi
    {
        return new ProductVariantUpdateApi($this);
    }

    public function productVariantB2bActivation(): ProductVariantB2bActivationApi
    {
        return new ProductVariantB2bActivationApi($this);
    }

    public function productVariantExport(): ProductVariantExportApi
    {
        return new ProductVariantExportApi($this);
    }

    public function productVariantGoldDetails(): ProductVariantGoldDetailsApi
    {
        return new ProductVariantGoldDetailsApi($this);
    }

    public function productVariantGoldUpdate(): ProductVariantGoldUpdateApi
    {
        return new ProductVariantGoldUpdateApi($this);
    }

    public function productVariantPriceCalculator(): ProductVariantPriceCalculatorApi
    {
        return new ProductVariantPriceCalculatorApi($this);
    }

    public function productVariantActivation(): ProductVariantActivationApi
    {
        return new ProductVariantActivationApi($this);
    }

    public function productVariantArchive(): ProductVariantArchiveApi
    {
        return new ProductVariantArchiveApi($this);
    }

    public function productVariantStockDetails(): ProductVariantStockDetailsApi
    {
        return new ProductVariantStockDetailsApi($this);
    }

    public function productVariantStockUpdate(): ProductVariantStockUpdateApi
    {
        return new ProductVariantStockUpdateApi($this);
    }

    public function productVariantSellingPrice(): ProductVariantSellingPriceApi
    {
        return new ProductVariantSellingPriceApi($this);
    }

    public function productVariantB2bPricesDetails(): ProductVariantB2bPricesDetailsApi
    {
        return new ProductVariantB2bPricesDetailsApi($this);
    }

    public function productVariantB2bPricesUpdate(): ProductVariantB2bPricesUpdateApi
    {
        return new ProductVariantB2bPricesUpdateApi($this);
    }

    public function variantCreationConfig(): VariantCreationConfigApi
    {
        return new VariantCreationConfigApi($this);
    }

    public function variantCreationSave(): VariantCreationSaveApi
    {
        return new VariantCreationSaveApi($this);
    }

    public function variantCreationEdit(): VariantCreationEditApi
    {
        return new VariantCreationEditApi($this);
    }

    public function variantProductImages(): VariantProductImagesApi
    {
        return new VariantProductImagesApi($this);
    }

    public function variantModerationImages(): VariantModerationImagesApi
    {
        return new VariantModerationImagesApi($this);
    }

    public function variantImageRejectionReasons(): VariantImageRejectionReasonsApi
    {
        return new VariantImageRejectionReasonsApi($this);
    }

    public function variantSizeTypes(): VariantSizeTypesApi
    {
        return new VariantSizeTypesApi($this);
    }

    public function variantSizeRequest(): VariantSizeRequestApi
    {
        return new VariantSizeRequestApi($this);
    }

    public function variantColorRequest(): VariantColorRequestApi
    {
        return new VariantColorRequestApi($this);
    }

    public function variantWarrantyRequest(): VariantWarrantyRequestApi
    {
        return new VariantWarrantyRequestApi($this);
    }

    public function variantThemeValues(): VariantThemeValuesApi
    {
        return new VariantThemeValuesApi($this);
    }
    public function orders(): OrdersApi
    {
        return new OrdersApi($this);
    }

    public function orderStatistics(): OrderStatisticsApi
    {
        return new OrderStatisticsApi($this);
    }

    public function orderHistory(): OrderHistoryApi
    {
        return new OrderHistoryApi($this);
    }

    public function orderCancellation(): OrderCancellationApi
    {
        return new OrderCancellationApi($this);
    }

    public function buyBoxWinningPriceSuggestion(): WinningPriceSuggestionApi
    {
        return new WinningPriceSuggestionApi($this);
    }

    public function buyBoxNthRankWinningPriceSuggestion(): NthRankWinningPriceSuggestionApi
    {
        return new NthRankWinningPriceSuggestionApi($this);
    }

    public function invoices(): InvoicesApi
    {
        return new InvoicesApi($this);
    }

    public function invoiceDetails(): InvoiceDetailsApi
    {
        return new InvoiceDetailsApi($this);
    }

    public function invoiceItems(): InvoiceItemsApi
    {
        return new InvoiceItemsApi($this);
    }

    public function invoiceVat(): InvoiceVatApi
    {
        return new InvoiceVatApi($this);
    }

    public function financeOverview(): FinanceOverviewApi
    {
        return new FinanceOverviewApi($this);
    }

    public function financeTransactions(): FinanceTransactionsApi
    {
        return new FinanceTransactionsApi($this);
    }

    public function financeTransactionRows(): FinanceTransactionRowsApi
    {
        return new FinanceTransactionRowsApi($this);
    }

    public function financeTransactionSummary(): FinanceTransactionSummaryApi
    {
        return new FinanceTransactionSummaryApi($this);
    }

    public function financeTransactionDetails(): FinanceTransactionDetailsApi
    {
        return new FinanceTransactionDetailsApi($this);
    }

    public function financePayouts(): FinancePayoutsApi
    {
        return new FinancePayoutsApi($this);
    }

    public function financePayoutDetails(): FinancePayoutDetailsApi
    {
        return new FinancePayoutDetailsApi($this);
    }

    public function financeSellerInvoices(): FinanceSellerInvoicesApi
    {
        return new FinanceSellerInvoicesApi($this);
    }

    public function financeTransactionsExport(): FinanceTransactionsExportApi
    {
        return new FinanceTransactionsExportApi($this);
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
