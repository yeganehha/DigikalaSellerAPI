# Webhook integration

## What the specification actually defines

The bundled file defines:

- `GET /open-api/v1/webhook/event-types`
- `POST /open-api/v1/webhook/subscription`
- `POST /open-api/v1/webhook/unsubscription`
- `POST /open-api/v1/webhook/subscription/change-activation`

These manage seller subscriptions. The incoming webhook envelope, signature headers/algorithm, retry schedule and callback registration are **not defined**. `event_type`, `event_id` and `X-Webhook-Gateway-Token` are not claimed to be Digikala fields/headers. Obtain the real delivery contract before connecting production.

## Automatic handling inside an application file

1. Route a public HTTPS URL to an application PHP file.
2. Load Composer's autoloader.
3. Load a trusted, local handler-registration file with `Router::fromFile()`.
4. Supply a sender-verification callback and an event-resolver callback.
5. Supply an idempotency store if duplicate deliveries must be suppressed.
6. Call `Receiver::serve()`; it reads the current HTTP request and emits the response.

An executable example is in `examples/public/webhook.php`; registrations are in `examples/webhook-handlers.php`. All body-field names are configured through environment variables. An existing framework can instead call `handle(ServerRequestInterface)` and return its PSR-7 response.

There are no Composer autoload side effects, implicit HTTP routes or directory scans. Calling the receiver in your entry file is the explicit activation point. Matching events and handler dispatch are automatic after that. Registering the callback URL with the sender is a separate task.

## Authentication is mandatory and fail-closed

The verifier is called with `(ServerRequestInterface $request, string $rawBody)` **before** JSON decoding or any handler. It must return exactly `true`; false or an exception produces HTTP 401. Use the untouched raw body for signatures. Do not reconstruct JSON before verification.

For a direct Digikala integration, implement exactly its agreed verification mechanism. Do not enable an unverified endpoint or invent an HMAC protocol.

The sample uses a token supplied by a trusted gateway:

- The gateway must authenticate the real source independently.
- It must remove any incoming `X-Webhook-Gateway-Token`, then inject the configured secret.
- Block all direct access to the application's receiver except from that gateway.
- Use HTTPS, a strong secret of at least 32 characters, and constant-time comparison.
- This custom token is not a Digikala feature. Without such a gateway, replace the sample verifier with the actual agreed verifier.

Rate limiting, source/IP restrictions, TLS termination and access-log redaction belong to the deployment. Do not blindly trust forwarded IP headers.

## Resolving events

The resolver receives the decoded object and must return `WebhookEvent(type, payload, id)`. `EventResolver::jsonFields($typeField, $idField, $namespace)` supports configured top-level keys and namespaces identifiers for one verified client/seller context. For nested/batched envelopes, write a resolver using the **verified** delivery shape. Invalid identity/type reported with `InvalidArgumentException` produces HTTP 400; unexpected resolver failures produce HTTP 500.

IDs must be globally unique for the configured store. If Digikala only guarantees per-seller IDs, construct a namespaced ID (seller + event ID) in your resolver or use separate stores. Do not synthesize an ID from time or generate a random ID per delivery. Hashing bodies is not a substitute for an agreed stable event identity.

## Handler files

A handler file returns a closure accepting `Router`. Register `on($eventType, $callback)` with a callback accepting `WebhookEvent`. Multiple callbacks per event are allowed. A `*` callback receives otherwise-unhandled events; it is a fallback, not an observer of all handled events. The sample logs only event type, never the full payload or credentials.

Always pass a fixed application-controlled local filename to `fromFile()`. It executes PHP and therefore must never receive a request-derived path. Handler files live outside the public document root.

## Responses

| Status | Meaning |
| --- | --- |
| 200 | All handlers succeeded, or stable ID was previously completed. |
| 422 | No matching handler; the event is not acknowledged as processed and its ID is not claimed. |
| 400 | Invalid JSON object, event identity/type or resolver failure. |
| 401 | Source verification failed or verifier threw. |
| 405 | Only POST is accepted; response includes `Allow: POST`. |
| 503 | Same stable ID is currently processing; response includes `Retry-After: 5`. |
| 413 | Body exceeds the configured byte limit. |
| 415 | Expected `application/json` or a `+json` media type. |
| 500 | Handler/store failure; the sender may retry according to its contract. |

Replies expose no payloads or exceptions. `serve()` reads at most the configured limit plus one byte from PHP input, and `handle()` applies the same limit before decoding. The default limit is 1 MiB.

## Idempotency and failure recovery

`FileIdempotencyStore` uses SHA-256 filenames and nonblocking exclusive file locks for atomic single-host claims. It holds the lock throughout handler execution. Successful IDs are retained for `retentionSeconds` (default seven days). A failed handler releases its lock; process termination releases the operating-system lock too. This implementation does not use time-based processing leases.

Use a private application-owned directory outside the web root, protected against other users/symlinks. Do not share it on unverified network filesystems. Completed files are not automatically deleted; deploy retention/cleanup consistent with the sender's maximum redelivery window. The example store path is `var/webhooks` and is excluded from Git.

This is **at-least-once processing**, not exactly-once delivery. A crash after a side effect but before `complete()` can repeat that effect. With multiple handlers, earlier successes can repeat if a later callback fails. Make side effects idempotent and prefer a transactional durable queue/outbox for business-critical work. A hung worker holds its file lock until it exits; enforce application/process execution timeouts.

For multi-host/long-running processing, implement `IdempotencyStore` with your existing database/Redis infrastructure, atomic claims and appropriate ownership/fencing; the interface deliberately avoids bundling another storage system. When no store is supplied, duplicate suppression is not enabled.

`complete()` or `release()` failures are returned as generic errors. A failed file-store completion is released where possible; process termination also releases its OS lock. Custom stores must define their own recovery semantics. Log diagnostics securely and return HTTP 200 only after durable processing or enqueueing succeeds.