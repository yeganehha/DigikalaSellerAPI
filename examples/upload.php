<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use Yeganehha\DigikalaSellerOpenApi\RequestOptions;

$filename = requiredEnvironment('DK_UPLOAD_FILE');
if (!is_file($filename) || !is_readable($filename)) {
    throw new RuntimeException('DK_UPLOAD_FILE must point to a readable local image.');
}

$stream = fopen($filename, 'rb');
if ($stream === false) {
    throw new RuntimeException('Cannot open the image.');
}

try {
    $response = exampleClient()->request(
        'POST',
        '/open-api/v1/product-creation/images/upload',
        new RequestOptions(multipart: [[
            'name' => 'file',
            'contents' => $stream,
            'filename' => basename($filename),
        ]]),
    );
    echo json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n";
} finally {
    fclose($stream);
}
