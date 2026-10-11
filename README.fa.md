# کتابخانه‌ی PHP برای API بازارگاه دیجی‌کالا

یک کلاینت PHP تایپ‌شده برای Open API بازارگاه دیجی‌کالا. این مخزن در حال حاضر endpoint بررسی سلامت، نوسازی توکن، کشف دسته‌بندی، جست‌وجوی محصول و گردش‌کارهای فروشنده و محصول را که در گزیده‌ی پیوست‌شده آمده‌اند پیاده‌سازی می‌کند. هر operation پیاده‌سازی‌شده کلاس endpoint مستقل دارد و هرجا schema ورودی در مستند مشخص بوده، DTO درخواست استفاده شده است. پاسخ‌هایی که schema کامل ندارند بدون حدس‌زدن فیلدها از طریق `ApiResponse` در دسترس هستند.

## پیش‌نیازها

- PHP نسخه‌ی 8.1 یا جدیدتر
- Composer

## نصب

```bash
composer require yeganehha/digikala-seller-open-api
```

این بسته هنوز در Packagist منتشر نشده است. برای توسعه‌ی محلی، مخزن را دریافت و `composer install` را اجرا کنید.

## شروع سریع

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\HealthCheckRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\RefreshTokenRequest;

$client = new MarketplaceClient();

$health = $client->healthCheck()->get(new HealthCheckRequest());
var_dump($health->payload);

// برای نوسازی، access token منقضی‌شده و refresh token فعلی لازم است.
$tokens = $client->refreshToken()->refresh(
    new RefreshTokenRequest(
        accessToken: getenv('DIGIKALA_ACCESS_TOKEN') ?: throw new RuntimeException('متغیر DIGIKALA_ACCESS_TOKEN تنظیم نشده است.'),
        refreshToken: getenv('DIGIKALA_REFRESH_TOKEN') ?: throw new RuntimeException('متغیر DIGIKALA_REFRESH_TOKEN تنظیم نشده است.'),
    ),
);

// هر دو توکن دریافتی را به شکل امن ذخیره کنید؛ ممکن است refresh token تعویض شود.
$nextAccessToken = $tokens->data->accessToken;
$nextRefreshToken = $tokens->data->refreshToken;
```

نمونه‌ی قابل اجرا در [`examples/refresh-token.php`](examples/refresh-token.php) قرار دارد. توکن‌ها، کد مجوز و سایر اطلاعات محرمانه را در مخزن ثبت نکنید.

## مدیریت خطا

خطای شبکه، پاسخ غیر ۲xx، JSON نامعتبر و شکل پاسخ غیرمنتظره با استثنا گزارش می‌شوند. در صورت دریافت پاسخ HTTP، مقدار `ApiException::$statusCode` شامل کد وضعیت است. خطای اعتبارسنجی DTO با `UnexpectedValueException` و ورودی نامعتبر با `InvalidArgumentException` گزارش می‌شود.

## اجرای تست‌ها

```bash
composer install
composer test
```

تست‌ها از Guzzle Mock Handler استفاده می‌کنند و به سرویس production درخواست نمی‌فرستند.
## جست‌وجوی دسته و محصول

```php
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\CategoryTreeRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductSearchRequest;

$client = new MarketplaceClient(accessToken: getenv('DIGIKALA_ACCESS_TOKEN') ?: null);
$categories = $client->categories()->get(new CategoryTreeRequest(parentId: 12));
$products = $client->productSearch()->search(new ProductSearchRequest(keyword: 'گوشی'));
```

برای فیلتر محصولات فروشنده، پیش‌نویس‌ها، جزئیات محصول، درخواست برند و آپلود، از DTO اختصاصی هر endpoint استفاده کنید. در آپلود فایل، یک stream بازشده و نام فایل را به `UploadImageRequest` بدهید؛ کلاینت multipart را ارسال می‌کند و boundary را دستی تنظیم نمی‌کند.

## مدیریت واریانت‌ها

```php
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\ProductVariantListRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\VariantStockRequest;

$client = new MarketplaceClient(accessToken: getenv('DIGIKALA_ACCESS_TOKEN') ?: null);
$variants = $client->productVariants()->list(new ProductVariantListRequest(
    page: 1,
    size: 50,
    filters: ['active' => true, 'shipping_type' => 'digikala'],
));
$variant = $client->productVariantDetails()->get(12345);
$stock = $client->productVariantStockUpdate()->update(12345, new VariantStockRequest(10));
```

فهرست و جزئیات واریانت از `ProductVariantDto` مشترک استفاده می‌کنند؛ فراداده‌ی صفحه‌بندی مانند `pager` و `sort_data` نیز در `ApiResponse` حفظ می‌شود. عملیات Variant و Variant Creation تعریف‌شده در گزیده‌ی OpenAPI، هرکدام endpoint class و DTO درخواست مستقل دارند.




## عملیات سفارش‌ها

```php
use Yeganehha\DigikalaSellerOpenApi\Client\MarketplaceClient;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderHistoryRequest;
use Yeganehha\DigikalaSellerOpenApi\Dto\OrderListRequest;

$client = new MarketplaceClient(accessToken: getenv('DIGIKALA_ACCESS_TOKEN') ?: null);
$activeOrders = $client->orders()->list(new OrderListRequest(
    searchTerm: 'کفش سفید',
    createdToday: true,
));
$history = $client->orderHistory()->list(new OrderHistoryRequest(
    orderType: 'processed',
    shippingType: 'digikala',
));
$statistics = $client->orderStatistics()->get();
```

هر آیتم در فهرست سفارش‌های فعال و تاریخچه از `OrderDto` مشترک استفاده می‌کند؛ pager، sort data و metadata در `ApiResponse` حفظ می‌شود. لغو سفارش نیز با `orderCancellation()->cancel($orderItemId, new OrderCancellationRequest($reasonId, $count))` در دسترس است؛ این عملیات تغییردهنده است.

نمونه‌ی فقط‌خواندنی سفارش: `php examples/order-list.php` پس از تنظیم `DIGIKALA_ACCESS_TOKEN`.
## BuyBox، فاکتورها و امور مالی

این گزیده ۲ مسیر BuyBox، چهار مسیر Invoice و ۹ مسیر Finance را مشخص می‌کند. هر مسیر endpoint class و factory جداگانه در `MarketplaceClient` دارد:

- `GET /open-api/v1/pricing/buybox/price-suggestion/winning-price`
- `GET /open-api/v1/pricing/buybox/price-suggestion/nth-rank-winning-price`
- `GET /open-api/v1/invoices`
- `GET /open-api/v1/invoices/{invoice_id}/details`
- `GET /open-api/v1/invoices/{invoice_id}/items/{financial_notation_id}/{calculation_type}`
- `POST /open-api/v1/invoices/vat`
- `GET /open-api/v1/finance/overview/v2`
- `GET /open-api/v1/finance/transactions`
- `GET /open-api/v1/finance/transactions/rows`
- `GET /open-api/v1/finance/transactions/summary`
- `GET /open-api/v1/finance/transactions/{transaction_id}`
- `GET /open-api/v1/finance/payouts`
- `GET /open-api/v1/finance/payouts/{payout_id}`
- `GET /open-api/v1/finance/seller-invoices`
- `POST /open-api/v1/finance/transactions/export/excel`

فهرست و جزئیات Invoice از `InvoiceDto` مشترک استفاده می‌کنند. DTOهای اختصاصی هر operation، query و body مستندشده را می‌سازند؛ wrapper پاسخ نیز pagination و metadata را حفظ می‌کند.

```bash
php examples/finance-read.php
```

`DIGIKALA_ACCESS_TOKEN` الزامی است. برای درخواست اختیاری Finance overview، متغیرهای `FINANCE_START_DATE` و `FINANCE_END_DATE` را تنظیم کنید؛ برای BuyBox، `BUYBOX_PRODUCT_VARIANT_ID` را بدهید. نمونه فقط GET اجرا می‌کند و VAT یا Excel export را فراخوانی نمی‌کند.

برای اجرای نمونه‌ی فقط‌خواندنی واریانت، پس از تنظیم `DIGIKALA_ACCESS_TOKEN` دستور `php examples/variant-list.php` را اجرا کنید.
## پوشش فعلی endpointها
این کتابخانه مسیرهای Health-check، Refresh Token، دسته‌بندی و Product دارای endpoint class را، به‌علاوه‌ی چهار Order operation، پانزده operation مربوط به BuyBox/Invoice/Finance و ۲۶ operation Variant/Variant Creation که در گزیده مسیر و schema آن‌ها آمده است، پیاده‌سازی می‌کند. فهرست و تاریخچه‌ی سفارش از `OrderDto` مشترک استفاده می‌کنند و فهرست و جزئیات invoice نیز `InvoiceDto` مشترک دارند.

گزیده‌ی OpenAPI ناقص است: ۲۷۵ عملیات را نام می‌برد، اما مسیر، schema درخواست و شکل پاسخ همه‌ی آن‌ها را ارائه نمی‌کند. بنابراین این کتابخانه ادعای پوشش کامل ۲۷۵ API را ندارد و جزئیات operationهای غایب از متن پیوست را حدس نمی‌زند.
## مجوز

MIT؛ جزئیات در فایل [LICENSE](LICENSE) است.
