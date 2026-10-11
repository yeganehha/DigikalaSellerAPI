<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class VariantIdsRequest
{
    /** @param list<int> $variantIds */
    public function __construct(public readonly array $variantIds)
    {
        if ($variantIds === [] || array_filter($variantIds, static fn (int $id): bool => $id < 1) !== []) {
            throw new InvalidArgumentException('At least one positive variant ID is required.');
        }
    }

    /** @return array{variant_ids: list<int>} */
    public function toArray(): array
    {
        return ['variant_ids' => array_values($this->variantIds)];
    }
}
