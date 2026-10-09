# Contributing

This SDK is independent of Digikala. Contributions should preserve compatibility with the bundled specification and avoid inventing undocumented provider behavior.

## Development

Use PHP 8.3+ and Composer 2:

```sh
composer install
composer validate --strict
composer format
composer lint
composer test
composer format:check
composer audit
```

Also test the lowest compatible dependencies with `composer update --prefer-lowest --prefer-stable` and rerun the checks. Do not commit vendor files, credentials, `.env`, webhook receipts or customer data.

## Changes

- Keep PHP strict types, PSR-4 names and PSR-12 formatting.
- Add focused tests for behavior changes and error paths.
- Keep `README.md`, `README.fa.md` and detailed guides consistent.
- Preserve the original license and review API-document updates separately.
- Run the complete mocked operation suite after updating the YAML. Generate the catalogue with `composer api:list` and the Markdown reference with `composer api:reference`.
- Do not use generated contract-test inputs for live API mutations.
- Do not introduce guessed authorization-code decryption, signature headers or webhook payload fields.

Report bugs with PHP/dependency versions, the documented method/path and a minimal reproduction using fake transport. Remove secrets and personal data. Follow [SECURITY.md](SECURITY.md) for private vulnerability reporting.

## Release gate

Follow [the publication checklist](docs/publishing.md). A successful file edit is not an executed test. Only claim checks that you actually ran, and do not tag a release with a failing build.