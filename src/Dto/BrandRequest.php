<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class BrandRequest
{
    /** @param array<string, scalar|null> $fields */
    public function __construct(public readonly array $fields)
    {
        foreach (['brand_origin', 'description', 'logo_id', 'name_en', 'name_fa', 'category_id'] as $requiredField) {
            if (!isset($this->fields[$requiredField]) || $this->fields[$requiredField] === '') {
                throw new InvalidArgumentException(sprintf('Required field "%s" must not be empty.', $requiredField));
            }
        }
    }

    /** @return array<string, scalar|null> */
    public function toArray(): array
    {
        return $this->fields;
    }
}
