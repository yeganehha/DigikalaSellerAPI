<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class AttributesRequest
{
    /** @param array<string, mixed> $fields */
    public function __construct(public readonly int $draftProductId, public readonly array $fields = [])
    {
        if ($this->draftProductId < 1) {
            throw new \InvalidArgumentException('Draft product ID must be positive.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_merge($this->fields, ['draft_product_id' => $this->draftProductId]);
    }
}
