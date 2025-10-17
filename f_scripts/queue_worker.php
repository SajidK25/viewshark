<?php
define('_ISVALID', true);
include_once __DIR__ . '/../f_core/config.core.php';
include_once __DIR__ . '/../f_core/f_functions/functions.queue.php';

$logger = VLogger::getInstance();
$queue  = $argv[1] ?? 'jobs';
$logger->info('Queue worker started', ['queue' => $queue, 'redis' => queue_available()]);

while (true) {
    $job = queue_dequeue($queue, 5);
    if (!$job) { continue; }

    try {
        $payload = $job['payload'] ?? [];
        $type = $payload['type'] ?? 'unknown';

        switch ($type) {
            case 'generate_preview':
                // TODO: call your preview generator with $payload['file_key']
                $logger->info('Handled generate_preview', $payload);
                break;
            case 'send_notification':
                // TODO: call your notification sender
                $logger->info('Handled send_notification', $payload);
                break;
            default:
                $logger->warning('Unknown job type', $payload);
        }
    } catch (Exception $e) {
        $logger->error('Job failed: ' . $e->getMessage(), ['job' => $job]);
    }
}

