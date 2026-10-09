<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Webhook;

use Closure;
use InvalidArgumentException;

final class EventResolver
{
    /** Configure field names using the delivery contract supplied for your Digikala client. */
    public static function jsonFields(string $typeField, string $idField, string $namespace): Closure
    {
        if ($typeField === '' || $idField === '' || $namespace === '') {
            throw new InvalidArgumentException('Type, ID fields and client namespace are required.');
        }
        return static function (array $payload) use ($typeField, $idField, $namespace): WebhookEvent {
            $type = $payload[$typeField] ?? null;
            $id = $payload[$idField] ?? null;
            if (!is_string($type) || (!is_string($id) && !is_int($id)) || (string) $id === '') {
                throw new InvalidArgumentException('Missing event identity.');
            }
            return new WebhookEvent(type: $type, payload: $payload, id: $namespace . ':' . $type . ':' . (string) $id);
        };
    }
}
