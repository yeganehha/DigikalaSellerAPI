<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\BuyBoxWinningPriceRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceDateRangeRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\FinanceTransactionsRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\InvoiceListRequest;
use Yeganehha\DigikalaSellerOpenApi\Exception\ApiException;

$accessToken = getenv('DIGIKALA_ACCESS_TOKEN');
if (!is_string($accessToken) || $accessToken === '') {
    fwrite(STDERR, "Set DIGIKALA_ACCESS_TOKEN in the environment.\n");
    exit(1);
}

$client = new MarketplaceClient(accessToken: $accessToken);

try {
    $transactions = $client->financeTransactions()->list(new FinanceTransactionsRequest(
        status: 'PENDING,ACCEPTED',
    ));
    echo 'Transactions on first page: ' . count($transactions->data['items'] ?? []) . PHP_EOL;

    $invoices = $client->invoices()->list(new InvoiceListRequest(page: 1, size: 50));
    echo 'Invoices on first page: ' . count($invoices->data['items'] ?? []) . PHP_EOL;

    $startDate = getenv('FINANCE_START_DATE');
    $endDate = getenv('FINANCE_END_DATE');
    if (is_string($startDate) && $startDate !== '' && is_string($endDate) && $endDate !== '') {
        $overview = $client->financeOverview()->get(new FinanceDateRangeRequest($startDate, $endDate));
        $period = is_array($overview->period) ? $overview->period : [];
        $overviewStart = $period['start_date'] ?? 'n/a';
        $overviewEnd = $period['end_date'] ?? 'n/a';
        echo 'Finance overview period: ' . $overviewStart . ' - ' . $overviewEnd . PHP_EOL;
    }

    $variantId = getenv('BUYBOX_PRODUCT_VARIANT_ID');
    if (is_string($variantId) && ctype_digit($variantId) && (int) $variantId > 0) {
        $suggestion = $client->buyBoxWinningPriceSuggestion()->get(
            new BuyBoxWinningPriceRequest((int) $variantId),
        );
        echo 'BuyBox suggested price (Rial): ' . ($suggestion->suggestedPrice ?? 'not available') . PHP_EOL;
    }
} catch (ApiException $exception) {
    fwrite(
        STDERR,
        'Marketplace API request failed (HTTP ' . ($exception->statusCode ?? 'no response') . ').' . PHP_EOL,
    );
    exit(1);
}
