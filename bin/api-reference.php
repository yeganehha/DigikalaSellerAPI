<?php

declare(strict_types=1);

use Yeganehha\DigikalaSellerOpenApi\OpenApi\Document;

require dirname(__DIR__) . '/vendor/autoload.php';

$document = Document::bundled();
if (in_array('--markdown', $argv, true)) {
    echo "# API reference\n\n";
    echo 'Source: `digikala-open-api-document.yml`. Operations: ' . count($document->operations()) . ".\n\n";
    echo "Call each operation using `Client::call()` or `Client::request()`.\n\n";
    foreach ($document->groups() as $group => $operations) {
        echo '## ' . $group . "\n\n";
        foreach ($operations as $operation) {
            echo '### `' . $operation->id . "`\n\n";
            echo '`' . $operation->method . ' ' . $operation->path . "`\n\n";
            $description = trim(strip_tags((string) ($operation->definition['description'] ?? '')));
            if ($description !== '') {
                echo $description . "\n\n";
            }
            foreach ($operation->parameters() as $parameter) {
                $required = filter_var($parameter['required'] ?? false, FILTER_VALIDATE_BOOLEAN);
                echo '- `' . $parameter['in'] . '`: `' . $parameter['name'] . '`';
                echo $required ? " (required)\n" : " (optional)\n";
            }
            $body = $operation->requestBody;
            if (is_array($body)) {
                echo "\nBody content types: " . implode(', ', array_keys($body['content'] ?? [])) . ".\n";
            }
            echo "\n";
        }
    }
    exit(0);
}

$catalogue = [];
foreach ($document->operations() as $operation) {
    $catalogue[] = [
        'id' => $operation->id,
        'method' => $operation->method,
        'path' => $operation->path,
        'group' => $operation->group,
        'parameters' => $operation->parameters(),
        'requestBody' => $operation->definition['requestBody'] ?? null,
        'deprecated' => $operation->definition['deprecated'] ?? false,
    ];
}
echo json_encode($catalogue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n";
