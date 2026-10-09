# Public release checklist

This repository is an unofficial library, not an automatically published Packagist package.

## Ownership and legal review

1. Confirm the `erfanebrahimi` Composer vendor name belongs to the publisher and the package name is available. Otherwise update `composer.json` and installation instructions together.
2. Preserve the existing MIT license and copyright attribution.
3. Verify redistribution rights for the supplied Digikala API document independently of SDK source licensing.
4. Do not imply official Digikala endorsement or ship seller credentials, customer data, tokens, or production webhook records.

## Reproducible checks

Use PHP 8.3+ and Composer 2 on a clean checkout:

```bash
composer validate --strict
composer update --prefer-dist
composer audit
composer format
composer format:check
composer lint
composer test
composer api:list
composer api:reference > docs/operations.md
```

Review formatting changes and the generated operation count/reference. The declared count in the source document may drift; actual parsed HTTP operations are the release criterion. The mock contract suite checks every parsed operation's method, path, request transport and authentication; it does not verify remote business rules.

Run a lowest-supported dependency build too:

```bash
composer update --prefer-lowest --prefer-stable
composer test
```

If either dependency set fails, fix it before tagging. Do not ignore dependency advisories. For a library the Composer lockfile is intentionally not distributed; record CI-resolved versions with release build logs.

## Integration checks

- Register an active client and use credentials with approved scopes.
- Use the configured sandbox URL only after validating its actual compatibility. The specification names production and links to a third-party mock sandbox, not a universally available hosted sandbox URL.
- Smoke-test read-only operations first. Mutation, export, batch and subscription tests can have effects or send emails; run them only with authorization and suitable test data.
- Test multipart boundary handling and any non-JSON response formats against the target service.
- Confirm authorization-code decryption and token storage requirements with Digikala; neither is inferred from the document.
- Obtain the real webhook payload, event identity, sender verification protocol, retry policy and callback registration procedure.
- Test the trusted ingress and ensure the backend cannot bypass it. Exercise duplicate, overlapping, failed, unknown and oversized deliveries.
- Set completion retention to match the verified redelivery window. Enforce process timeouts, durable business idempotency and storage cleanup; configure leases only if your custom shared store uses them.

## Package and tag

Review `.gitattributes` and the Composer archive contents. The runtime YAML document must be included even when examples, tests and development tools are excluded.

Update `CHANGELOG.md` with verified results. Tag a semantic version only after the checklist succeeds. Do not add a hard-coded `version` to `composer.json`; Composer derives it from VCS tags. Push the repository/tag and submit the repository URL through your Packagist account. No account, repository URL or release tag is assumed by this project.

## Validation boundary

The assistant workspace provides file inspection and edits but no PHP/Composer process runner. Automated tests, dependency installation/audit, code style fixing and remote integration must be executed in CI or the publisher's environment. File-write validation alone is not a green build.
