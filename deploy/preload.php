<?php

declare(strict_types=1);

// OPcache preloading for Laravel
$autoload = dirname(__DIR__) . '/vendor/autoload.php';

if (! file_exists($autoload)) {
    return;
}

require_once $autoload;

$base = dirname(__DIR__);

// Critical core Laravel directories to precompile in shared memory
$preloadDirs = [
    $base . '/vendor/laravel/framework/src/Illuminate/Container',
    $base . '/vendor/laravel/framework/src/Illuminate/Contracts',
    $base . '/vendor/laravel/framework/src/Illuminate/Support',
    $base . '/vendor/laravel/framework/src/Illuminate/Http',
    $base . '/vendor/laravel/framework/src/Illuminate/Routing',
    $base . '/vendor/laravel/framework/src/Illuminate/Pipeline',
    $base . '/vendor/laravel/framework/src/Illuminate/Events',
    $base . '/vendor/laravel/framework/src/Illuminate/Database/Eloquent',
];

foreach ($preloadDirs as $dir) {
    if (! is_dir($dir)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            if (str_contains($path, 'Test') || str_contains($path, 'Blade')) {
                continue;
            }
            try {
                @opcache_compile_file($path);
            } catch (\Throwable) {
                // Ignore non-preloadable classes
            }
        }
    }
}
