# کتابخانه‌ی PHP برای API بازارگاه دیجی‌کالا

یک کلاینت PHP تایپ‌شده برای Open API بازارگاه دیجی‌کالا. نسخه‌ی اولیه‌ی این مخزن دو endpoint بررسی سلامت سرویس و نوسازی توکن را ارائه می‌کند. ساختار پروژه برای افزودن endpointهای مستندشده به شکل کلاس‌های مستقل و همراه DTO درخواست و پاسخ آماده است.

## پیش‌نیازها

- PHP نسخه‌ی 8.1 یا جدیدتر
- Composer

## نصب

```bash
composer require digikala/marketplace-api-client
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

## پوشش فعلی endpointها

| endpoint | متد | DTO درخواست | DTO پاسخ |
| --- | --- | --- | --- |
| `GET /open-api/v1/` | `healthCheck()->get()` | `HealthCheckRequest` | `HealthCheckResponse` |
| `POST /open-api/v1/auth/refresh-token` | `refreshToken()->refresh()` | `RefreshTokenRequest` | `RefreshTokenResponse` ← `RefreshTokenData` ← `TokenExpiry` |

مستند OpenAPI پیوست‌شده ۲۷۵ عملیات را فهرست می‌کند. در این مرحله فقط همین دو endpoint پیاده‌سازی شده‌اند و سایر endpointها هنوز پوشش داده نشده‌اند.

## مجوز

MIT؛ جزئیات در فایل [LICENSE](LICENSE) است.
