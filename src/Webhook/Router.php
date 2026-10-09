<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Webhook;

use Closure;
use InvalidArgumentException;

final class Router
{
    /** @var array<string, list<Closure>> */
    private array $handlers = [];

    public function on(string $eventType, callable $handler): self
    {
        if ($eventType === '') {
            throw new InvalidArgumentException('Event type must not be empty.');
        }
        $this->handlers[$eventType][] = Closure::fromCallable($handler);
        return $this;
    }

    public static function fromFile(string $file): self
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new InvalidArgumentException('The trusted handler configuration file is not readable.');
        }
        $register = require $file;
        if (!is_callable($register)) {
            throw new InvalidArgumentException('Handler file must return a callable accepting a Router.');
        }
        $router = new self();
        $register($router);
        return $router;
    }

    public function supports(string $type): bool
    {
        return isset($this->handlers[$type]) || isset($this->handlers['*']);
    }

    public function dispatch(WebhookEvent $event): void
    {
        $handler = $this->handlers[$event->type] ?? $this->handlers['*'] ?? null;
        if ($handler === null) {
            throw new InvalidArgumentException('No handler registered for this event type.');
        }
        foreach ($handler as $callback) {
            $callback($event);
        }
    }
}
