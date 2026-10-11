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



برای اجرای نمونه‌ی فقط‌خواندنی واریانت، پس از تنظیم `DIGIKALA_ACCESS_TOKEN` دستور `php examples/variant-list.php` را اجرا کنید.
## پوشش فعلی endpointها

این پروژه مسیرهای Health-check، Refresh Token، دسته‌بندی و Product را که کلاس endpoint آن‌ها در `src/Endpoint/` وجود دارد، همراه با تمام ۲۶ عملیات مستندشده‌ی Product Variant و Variant Creation در گزیده‌ی پیوست پیاده‌سازی می‌کند. DTOهای درخواست و پاسخ در `src/Dto/` هستند؛ پاسخ‌های دارای schema واریانت در فهرست، جزئیات، ایجاد و ویرایش از `ProductVariantDto` مشترک استفاده می‌کنند.

گزیده‌ی OpenAPI ناقص است: ۲۷۵ عملیات را نام می‌برد، اما مسیر، schema درخواست و شکل پاسخ همه‌ی آن‌ها را ارائه نمی‌کند. بنابراین این کتابخانه ادعای پوشش کامل ۲۷۵ API را ندارد و جزئیات operationهای غایب از متن پیوست را حدس نمی‌زند.
## مجوز

MIT؛ جزئیات در فایل [LICENSE](LICENSE) است.
