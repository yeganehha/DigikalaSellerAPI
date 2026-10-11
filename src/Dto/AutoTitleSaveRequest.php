<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class AutoTitleSaveRequest
{
    /** @param list<string>|null $advantages @param list<string>|null $disadvantages */
    public function __construct(
        public readonly int $draftProductId,
        public readonly string $titleFa,
        public readonly ?string $titleEn = null,
        public readonly ?array $advantages = null,
        public readonly ?string $description = null,
        public readonly ?array $disadvantages = null,
    ) {
        if ($this->draftProductId < 1 || trim($this->titleFa) === '') {
            throw new InvalidArgumentException('Draft product ID and Persian title are required.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'draftProductId' => $this->draftProductId,
            'titleFa' => $this->titleFa,
        ];
        foreach ([
            'titleEn' => $this->titleEn,
            'description' => $this->description,
            'advantages' => $this->advantages,
            'disadvantages' => $this->disadvantages,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
