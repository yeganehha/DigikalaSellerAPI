<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

final class FinanceTransactionDetailDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly array $attributes,
        public readonly ?int $transactionId,
        public readonly ?int $finalAmount,
        public readonly mixed $metaData,
        public readonly array $financialDetails,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $transactionId = $data['transaction_id'] ?? null;
        $finalAmount = $data['final_amount'] ?? null;
        if (($transactionId !== null && !is_int($transactionId)) || ($finalAmount !== null && !is_int($finalAmount))) {
            throw new UnexpectedValueException('Finance transaction detail contains an invalid integer field.');
        }
        $metaData = $data['meta_data'] ?? null;
        $financialDetails = $data['financial_details'] ?? [];
        if (($metaData !== null && !is_array($metaData)) || !is_array($financialDetails)) {
            throw new UnexpectedValueException('Finance transaction detail contains an invalid nested value.');
        }

        return new self($data, $transactionId, $finalAmount, $metaData, $financialDetails);
    }
}
