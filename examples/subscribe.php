<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$events = array_values(array_filter(array_map('trim', explode(',', requiredEnvironment('DK_WEBHOOK_EVENTS')))));
if ($events === []) {
    throw new RuntimeException('At least one webhook event type is required.');
}

$subscription = exampleClient()->webhooks();
$subscription->subscribe($events);
$subscription->activate(true);
fwrite(STDOUT, "Subscription requested and activated. Callback URL configuration is separate.\n");
