<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class UploadImageRequest
{
    /** @var resource */
    public readonly mixed $stream;

    /** @param resource $stream */
    public function __construct(mixed $stream, public readonly string $filename)
    {
        if (!is_resource($stream) || trim($filename) === '') {
            throw new InvalidArgumentException('A readable stream and filename are required.');
        }
        $this->stream = $stream;
    }

    /** @return list<array{name: string, contents: resource, filename: string}> */
    public function toMultipart(): array
    {
        return [[
            'name' => 'file',
            'contents' => $this->stream,
            'filename' => $this->filename,
        ]];
    }
}
