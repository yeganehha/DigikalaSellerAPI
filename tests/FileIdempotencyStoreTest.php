<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Tests;

use Yeganehha\DigikalaSellerOpenApi\Webhook\Claim;
use Yeganehha\DigikalaSellerOpenApi\Webhook\FileIdempotencyStore;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class FileIdempotencyStoreTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/dk-sdk-' . bin2hex(random_bytes(12));
        mkdir($this->directory, 0700);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory . '/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->directory);
    }

    public function testClaimReleaseAndCompletionPersistAcrossInstances(): void
    {
        $first = new FileIdempotencyStore($this->directory);
        $second = new FileIdempotencyStore($this->directory);
        self::assertSame(Claim::Acquired, $first->claim('seller-1:order_processed:123'));
        self::assertSame(Claim::Busy, $second->claim('seller-1:order_processed:123'));
        $first->release('seller-1:order_processed:123');
        self::assertSame(Claim::Acquired, $second->claim('seller-1:order_processed:123'));
        $second->complete('seller-1:order_processed:123');
        self::assertSame(Claim::Duplicate, $first->claim('seller-1:order_processed:123'));
        self::assertSame(Claim::Acquired, $first->claim('seller-2:order_processed:123'));
    }

    public function testExpiredCompletionCanBeClaimedAgain(): void
    {
        $key = 'expired-event';
        file_put_contents(
            $this->directory . '/' . hash('sha256', $key) . '.lock',
            (string) (time() - 60),
        );
        $store = new FileIdempotencyStore($this->directory, retentionSeconds: 10);
        self::assertSame(Claim::Acquired, $store->claim($key));
    }

    public function testCorruptStateFailsInsteadOfAcknowledgingAnEvent(): void
    {
        $key = 'corrupt-event';
        file_put_contents($this->directory . '/' . hash('sha256', $key) . '.lock', 'not-a-timestamp');
        $store = new FileIdempotencyStore($this->directory);
        $this->expectException(RuntimeException::class);
        $store->claim($key);
    }

    public function testEmptyDirectoryIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FileIdempotencyStore('');
    }
}
