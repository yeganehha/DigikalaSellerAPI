<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use Yeganehha\DigikalaSellerOpenApi\Client;
use Yeganehha\DigikalaSellerOpenApi\Configuration;

$client = new Client(new Configuration());
$authorizationCode = requiredEnvironment('DK_AUTHORIZATION_CODE');
$tokenResponse = $client->authentication()->exchange($authorizationCode);
$tokens = $tokenResponse->json()['data'] ?? [];

if (!is_string($tokens['access_token'] ?? null) || !is_string($tokens['refresh_token'] ?? null)) {
    throw new RuntimeException('The token response does not contain the expected tokens.');
}

// Persist tokens in your application's encrypted secret store; never print them.
$authenticatedClient = $client->withAccessToken($tokens['access_token']);
$scopes = $authenticatedClient->authentication()->scopes();
printf("Token exchange succeeded; scopes endpoint returned HTTP %d.\n", $scopes->status());
