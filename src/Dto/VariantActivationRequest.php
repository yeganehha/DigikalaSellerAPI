<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class VariantActivationRequest
{
    public function __construct(public readonly bool $activation)
    {
    }

    /** @return array{activation: bool} */
    public function toArray(): array
    {
        return ['activation' => $this->activation];
    }
}
