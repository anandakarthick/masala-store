<?php
// Clear all cached views
$viewsPath = __DIR__ . '/storage/framework/views';

$files = glob($viewsPath . '/*.php');
$count = 0;

foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
        $count++;
    }
}

echo "Deleted $count cached view files.\n";

// Also clear bootstrap cache
$bootstrapCache = __DIR__ . '/bootstrap/cache';
$cacheFiles = ['config.php', 'routes-v7.php', 'services.php', 'packages.php'];

foreach ($cacheFiles as $cacheFile) {
    $path = $bootstrapCache . '/' . $cacheFile;
    if (file_exists($path)) {
        unlink($path);
        echo "Deleted: $cacheFile\n";
    }
}

echo "\nCache cleared! Now run: php artisan config:clear && php artisan view:clear\n";
