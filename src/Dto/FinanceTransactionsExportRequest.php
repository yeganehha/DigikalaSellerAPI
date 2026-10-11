<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use InvalidArgumentException;

final class FinanceTransactionsExportRequest
{
    public function __construct(
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
        public readonly ?string $statuses = null,
        public readonly ?string $notationIds = null,
        public readonly ?string $searchStatus = null,
        public readonly ?string $searchNotations = null,
        public readonly ?string $aggregationType = null,
        public readonly ?string $source = null,
        /** @param array<string, mixed>|null $body JSON fields allowed by the optional OpenAPI request body. */
        public readonly ?array $body = null,
    ) {
        if ($source !== null && !in_array($source, ['per_notations', 'per_aggregations'], true)) {
            throw new InvalidArgumentException('Unsupported finance export source.');
        }
        if ($body !== null) {
            $allowedBodyFields = ['start_date', 'end_date', 'statuses', 'notation_ids', 'source', 'search'];
            foreach ($body as $name => $value) {
                if (!in_array($name, $allowedBodyFields, true)) {
                    throw new InvalidArgumentException(sprintf('Unsupported finance export body field: %s.', $name));
                }
            }
        }
    }

    /** @return array<string, string> */
    public function toQuery(): array
    {
        $query = [];
        foreach ([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'statuses' => $this->statuses,
            'notation_ids' => $this->notationIds,
            'search[status]' => $this->searchStatus,
            'search[notations]' => $this->searchNotations,
            'search[aggregation_type]' => $this->aggregationType,
            'source' => $this->source,
        ] as $name => $value) {
            if ($value !== null) {
                $query[$name] = $value;
            }
        }

        return $query;
    }

    /** @return array<string, mixed>|null */
    public function toBody(): ?array
    {
        return $this->body === [] ? null : $this->body;
    }
}
