<?php

declare(strict_types=1);

use Yeganehha\DigikalaSellerOpenApi\Client;
use Yeganehha\DigikalaSellerOpenApi\Configuration;

require dirname(__DIR__) . '/vendor/autoload.php';

function requiredEnvironment(string $name): string
{
    $value = getenv($name);
    if ($value === false || trim($value) === '') {
        throw new RuntimeException('Set the required environment variable: ' . $name);
    }
    return $value;
}

function exampleClient(bool $authenticated = true): Client
{
    $baseUri = getenv('DK_BASE_URI') ?: 'https://seller.digikala.com';
    $allowHttp = filter_var(getenv('DK_ALLOW_INSECURE_HTTP') ?: false, FILTER_VALIDATE_BOOLEAN);
    return new Client(new Configuration(
        baseUri: $baseUri,
        accessToken: $authenticated ? requiredEnvironment('DK_ACCESS_TOKEN') : null,
        allowInsecureHttp: $allowHttp,
    ));
}
