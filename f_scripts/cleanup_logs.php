<?php
define('_ISVALID', true);
include_once __DIR__ . '/../f_core/config.core.php';

$days = (int) ($cfg['logging_retention_days'] ?? 30);
if ($days <= 0) { exit(0); }

$dir = 'f_data/logs/';
$cutoff = time() - ($days * 86400);

if (!is_dir($dir)) { exit(0); }

$deleted = 0;
foreach (glob($dir . '*.log*') as $file) {
    if (@filemtime($file) < $cutoff) {
        @unlink($file);
        $deleted++;
    }
}

echo "Cleanup complete, deleted {$deleted} files\n";

