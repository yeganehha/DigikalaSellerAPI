<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Dto;

use UnexpectedValueException;

/** Shared order-item representation for active orders and order history. */
final class OrderDto
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public readonly ?int $orderId,
        public readonly ?int $productId,
        public readonly ?int $productVariantId,
        public readonly ?string $productVariantTitle,
        public readonly ?int $quantity,
        public readonly ?int $totalPrice,
        public readonly array $attributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $orderId = $data['order_id'] ?? null;
        $productId = $data['product_id'] ?? null;
        $variantId = $data['product_variant_id'] ?? null;
        $variantTitle = $data['product_variant_title'] ?? null;
        $quantity = $data['quantity'] ?? null;
        $totalPrice = $data['total_price'] ?? null;

        foreach (['order_id' => $orderId, 'product_id' => $productId, 'product_variant_id' => $variantId, 'quantity' => $quantity, 'total_price' => $totalPrice] as $field => $value) {
            if ($value !== null && !is_int($value)) {
                throw new UnexpectedValueException(sprintf('Order response field %s has an invalid type.', $field));
            }
        }
        if ($variantTitle !== null && !is_string($variantTitle)) {
            throw new UnexpectedValueException('Order response field product_variant_title has an invalid type.');
        }

        return new self($orderId, $productId, $variantId, $variantTitle, $quantity, $totalPrice, $data);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
