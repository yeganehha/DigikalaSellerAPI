<?php

declare(strict_types=1);

namespace Yeganehha\DigikalaSellerOpenApi\Webhook;

use InvalidArgumentException;
use RuntimeException;

/** Single-host storage. Use a transactional shared store for multi-host deployments. */
final class FileIdempotencyStore implements IdempotencyStore
{
    /** @var array<string, resource> */
    private array $locks = [];

    public function __construct(private readonly string $directory, private readonly int $retentionSeconds = 604800)
    {
        if ($retentionSeconds < 1 || $directory === '') {
            throw new InvalidArgumentException('A directory and positive retention are required.');
        }
        if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new RuntimeException('Cannot create webhook storage directory.');
        }
        if (!is_writable($directory)) {
            throw new RuntimeException('Webhook storage directory must be writable.');
        }
    }

    public function claim(string $key): Claim
    {
        if (isset($this->locks[$key])) {
            return Claim::Busy;
        }
        $file = $this->directory . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.lock';
        $stream = @fopen($file, 'c+');
        if ($stream === false) {
            throw new RuntimeException('Cannot open webhook storage.');
        }
        if (!flock($stream, LOCK_EX | LOCK_NB)) {
            fclose($stream);
            return Claim::Busy;
        }
        $completedAt = stream_get_contents($stream);
        if ($completedAt === false || ($completedAt !== '' && !ctype_digit($completedAt))) {
            flock($stream, LOCK_UN);
            fclose($stream);
            throw new RuntimeException('Cannot read webhook storage.');
        }
        if (ctype_digit($completedAt) && (int) $completedAt > time() - $this->retentionSeconds) {
            flock($stream, LOCK_UN);
            fclose($stream);
            return Claim::Duplicate;
        }
        $this->locks[$key] = $stream;
        return Claim::Acquired;
    }

    public function complete(string $key): void
    {
        $stream = $this->locks[$key] ?? null;
        if ($stream === null) {
            throw new RuntimeException('The event was not claimed.');
        }
        $timestamp = (string) time();
        if (!rewind($stream) || !ftruncate($stream, 0)
            || fwrite($stream, $timestamp) !== strlen($timestamp) || !fflush($stream)) {
            throw new RuntimeException('Cannot persist webhook completion.');
        }
        $this->release($key);
    }

    public function release(string $key): void
    {
        if (!isset($this->locks[$key])) {
            return;
        }
        flock($this->locks[$key], LOCK_UN);
        fclose($this->locks[$key]);
        unset($this->locks[$key]);
    }

    public function __destruct()
    {
        foreach (array_keys($this->locks) as $key) {
            $this->release($key);
        }
    }
}
