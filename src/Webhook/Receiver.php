<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Webhook;

use Closure;
use GuzzleHttp\Psr7\Response;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class Receiver
{
    /**
     * The verifier must authenticate the raw request using the actual delivery contract.
     * The resolver must supply a stable, seller-scoped delivery identifier when available.
     *
     * @param Closure(ServerRequestInterface, string): bool $verifier
     * @param Closure(array, ServerRequestInterface): WebhookEvent $resolver
     * @param null|Closure(Throwable): void $onError
     */
    public function __construct(
        private readonly Router $router,
        private readonly Closure $verifier,
        private readonly Closure $resolver,
        private readonly ?IdempotencyStore $store = null,
        private readonly ?Closure $onError = null,
        private readonly int $maxBytes = 1048576,
    ) {
        if ($maxBytes < 1) {
            throw new \InvalidArgumentException('The webhook body limit must be positive.');
        }
    }

    /** Read the current PHP request and emit a response without terminating the process. */
    public function serve(): void
    {
        $input = fopen('php://input', 'rb');
        if ($input === false) {
            throw new \RuntimeException('Cannot open the HTTP request body.');
        }
        try {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (!is_string($value)) {
                    continue;
                }
                if (str_starts_with($name, 'HTTP_')) {
                    $headers[str_replace('_', '-', substr($name, 5))] = $value;
                } elseif (in_array($name, ['CONTENT_TYPE', 'CONTENT_LENGTH'], true)) {
                    $headers[str_replace('_', '-', $name)] = $value;
                }
            }
            $request = new \GuzzleHttp\Psr7\ServerRequest(
                $_SERVER['REQUEST_METHOD'] ?? 'GET',
                \GuzzleHttp\Psr7\ServerRequest::getUriFromGlobals(),
                $headers,
                $input,
                serverParams: $_SERVER,
            );
            $response = $this->handle($request);
            http_response_code($response->getStatusCode());
            foreach ($response->getHeaders() as $name => $values) {
                header($name . ': ' . implode(', ', $values));
            }
            echo (string) $response->getBody();
        } finally {
            fclose($input);
        }
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (strtoupper($request->getMethod()) !== 'POST') {
            return $this->response(405, 'method_not_allowed')->withHeader('Allow', 'POST');
        }
        $mediaType = strtolower(trim(explode(';', $request->getHeaderLine('Content-Type'))[0]));
        if ($mediaType !== 'application/json' && !str_ends_with($mediaType, '+json')) {
            return $this->response(415, 'unsupported_media_type');
        }
        $length = $request->getHeaderLine('Content-Length');
        if (ctype_digit($length) && (int) $length > $this->maxBytes) {
            return $this->response(413, 'payload_too_large');
        }
        try {
            $body = $request->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }
            $raw = '';
            while (!$body->eof() && strlen($raw) <= $this->maxBytes) {
                $chunk = $body->read(min(8192, $this->maxBytes + 1 - strlen($raw)));
                if ($chunk === '' && !$body->eof()) {
                    return $this->response(400, 'unreadable_payload');
                }
                $raw .= $chunk;
            }
            if (strlen($raw) > $this->maxBytes) {
                return $this->response(413, 'payload_too_large');
            }
            try {
                $verified = ($this->verifier)($request, $raw) === true;
            } catch (Throwable) {
                $verified = false;
            }
            if (!$verified) {
                return $this->response(401, 'unauthorized');
            }
            try {
                $payload = json_decode($raw, true, 128, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
            } catch (JsonException) {
                return $this->response(400, 'invalid_json');
            }
            if (!is_array($payload) || !str_starts_with(ltrim($raw), '{')) {
                return $this->response(400, 'invalid_payload');
            }
            try {
                $event = ($this->resolver)($payload, $request);
            } catch (\InvalidArgumentException) {
                return $this->response(400, 'invalid_event');
            }
            if (!$this->router->supports($event->type)) {
                return $this->response(422, 'unsupported_event');
            }
            $key = $event->id === null ? null : hash('sha256', $event->type . ':' . $event->id);
            if ($this->store !== null && $key !== null) {
                $claim = $this->store->claim($key);
                if ($claim === Claim::Duplicate) {
                    return $this->response(200, 'already_processed');
                }
                if ($claim === Claim::Busy) {
                    return $this->response(503, 'processing')->withHeader('Retry-After', '5');
                }
            }
            try {
                $this->router->dispatch($event);
                if ($this->store !== null && $key !== null) {
                    $this->store->complete($key);
                }
            } catch (Throwable $exception) {
                if ($this->store !== null && $key !== null) {
                    $this->store->release($key);
                }
                throw $exception;
            }
            return $this->response(200, 'processed');
        } catch (Throwable $exception) {
            if ($this->onError !== null) {
                try {
                    ($this->onError)($exception);
                } catch (Throwable) {
                    // Diagnostic failures must not change the HTTP failure response.
                }
            }
            return $this->response(500, 'processing_failed');
        }
    }

    private function response(int $status, string $message): ResponseInterface
    {
        return new Response(
            $status,
            ['Content-Type' => 'application/json', 'Cache-Control' => 'no-store'],
            json_encode(['status' => $message], JSON_THROW_ON_ERROR),
        );
    }
}
