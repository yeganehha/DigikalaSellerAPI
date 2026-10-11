<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class FinancePayoutListRequest
{
    public function __construct(public readonly ?string $status = null)
    {
    }

    /** @return array<string, string> */
    public function toQuery(): array
    {
        return $this->status === null ? [] : ['search[status]' => $this->status];
    }
}
