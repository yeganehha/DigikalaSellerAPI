<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class VariantArchiveRequest
{
    public function __construct(public readonly bool $archive)
    {
    }

    /** @return array{archive: bool} */
    public function toArray(): array
    {
        return ['archive' => $this->archive];
    }
}
