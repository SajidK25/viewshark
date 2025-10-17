<?php
// Simple preflight/health endpoint
define('_ISVALID', true);
include_once __DIR__ . '/../f_core/config.core.php';

header('Content-Type: application/json');

$status = [
    'status' => 'ok',
    'time' => date('c'),
    'db' => isset($class_database) ? 'connected' : 'unavailable',
];

echo json_encode($status);
