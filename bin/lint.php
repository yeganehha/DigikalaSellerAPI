<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$paths = ['src', 'tests', 'examples', 'bin'];
$files = [$root . '/.php-cs-fixer.dist.php'];
foreach ($paths as $path) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root . '/' . $path, FilesystemIterator::SKIP_DOTS),
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
}
$failed = false;
foreach ($files as $file) {
    $process = proc_open([PHP_BINARY, '-l', $file], [1 => STDOUT, 2 => STDERR], $pipes);
    if (!is_resource($process) || proc_close($process) !== 0) {
        $failed = true;
    }
}
exit($failed ? 1 : 0);
