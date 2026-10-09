<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests;

use Yeganehha\DigikalaSellerOpenApi\Configuration;
use Yeganehha\DigikalaSellerOpenApi\Http\RequestBuilder;
use Yeganehha\DigikalaSellerOpenApi\OpenApi\Document;
use Yeganehha\DigikalaSellerOpenApi\RequestOptions;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ValidationTest extends TestCase
{
    public function testRequiredBodyFieldsAreEnforced(): void
    {
        $operation = Document::bundled()->find('POST', '/open-api/v1/auth/token');
        $this->expectException(InvalidArgumentException::class);
        (new RequestBuilder())->build($operation, new RequestOptions(body: []), new Configuration());
    }

    public function testMissingPathParameterIsRejected(): void
    {
        $operation = Document::bundled()->find('GET', '/open-api/v1/variants/{variant_id}');
        $this->expectException(InvalidArgumentException::class);
        (new RequestBuilder())->build($operation, new RequestOptions(), new Configuration());
    }

    public function testRequiredQueryParameterIsEnforced(): void
    {
        $operation = Document::bundled()->find('GET', '/open-api/v1/product-creation/search/v2');
        $this->expectException(InvalidArgumentException::class);
        (new RequestBuilder())->build($operation, new RequestOptions(), new Configuration());
    }

    public function testUnknownOperationIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Document::bundled()->find('GET', '/not-a-documented-api');
    }

    public function testInsecureBaseUriIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Configuration(baseUri: 'http://seller.digikala.com');
    }

    public function testProtectedHeadersCannotBeOverridden(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new RequestBuilder())->build(
            operation: Document::bundled()->find('GET', '/open-api/v1/'),
            options: new RequestOptions(headers: ['Authorization' => 'Bearer replacement']),
            configuration: new Configuration(),
        );
    }

    public function testFalseIsNotTreatedAsAMissingRequiredValue(): void
    {
        $request = (new RequestBuilder())->build(
            operation: Document::bundled()->find('PUT', '/open-api/v1/variants/{variant_id}/activation'),
            options: new RequestOptions(path: ['variant_id' => 1234], body: ['activation' => false]),
            configuration: new Configuration(),
        );
        self::assertSame(['activation' => false], json_decode((string) $request->getBody(), true));
    }
}
