<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

final class OrderCancellationRequest
{
    public function __construct(
        public readonly int $cancellationReasonId,
        public readonly ?int $count = null,
    ) {
        if ($cancellationReasonId < 1 || ($count !== null && $count < 1)) {
            throw new \InvalidArgumentException('Cancellation reason ID and count must be positive.');
        }
    }

    /** @return array{cancellation_reason_id: int, count?: int} */
    public function toArray(): array
    {
        $data = ['cancellation_reason_id' => $this->cancellationReasonId];
        if ($this->count !== null) {
            $data['count'] = $this->count;
        }

        return $data;
    }
}
