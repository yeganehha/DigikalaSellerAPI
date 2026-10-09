# Security policy

## Reporting

Do not disclose credentials, payloads containing customer data, or exploit details in a public issue. Use the repository host's private vulnerability-reporting feature when the maintainer has enabled it. If it is unavailable, contact the maintainer privately using their verified repository profile before sharing sensitive details. No unverified contact address is supplied here.

## Operational boundaries

- This is an unofficial SDK. Remote API permissions and enforcement belong to Digikala; your application must separately authorize its users and seller/tenant operations.
- Never accept a base URI, document filename, handler filename, token or HTTP client settings directly from untrusted input.
- Default HTTP transport has TLS verification, bounded timeouts and no redirects. Injected clients must preserve these safeguards.
- HTTP errors expose response data explicitly, not in automatic exception messages. Applications are responsible for redacting explicit diagnostics.
- Webhook verification and event resolution are required. The document does not specify the incoming authentication/payload protocol; obtain it from the provider.
- The trusted-gateway example is not a Digikala signature implementation. Protect the ingress, remove spoofed gateway headers, and prevent direct backend access.
- Keep webhook storage outside the web root on a trusted private directory. Implement atomic shared storage and business idempotency for multi-instance applications.
- Apply ingress rate limits, timeouts, payload limits, monitoring and suitable retention/cleanup.

## Dependencies and releases

Run `composer audit` before release and regularly in deployed applications. Check runtime and development tool maintenance dates. No security support period is promised for an untagged initial release; the maintainer should define supported SDK versions when publishing the first release.
