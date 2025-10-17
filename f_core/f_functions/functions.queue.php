<?php
defined('_ISVALID') or header('Location: /error');

/**
 * Lightweight queue helpers with Redis fallback to filesystem.
 */

function queue_redis_client()
{
    static $redis = null;
    if ($redis !== null) return $redis;

    $host = getenv('REDIS_HOST') ?: ($GLOBALS['cfg']['redis_host'] ?? null);
    $port = (int) (getenv('REDIS_PORT') ?: ($GLOBALS['cfg']['redis_port'] ?? 6379));
    $db   = (int) (getenv('REDIS_DB') ?: ($GLOBALS['cfg']['redis_db'] ?? 0));

    if (!$host) return $redis = false;

    if (!class_exists('Redis')) return $redis = false;

    try {
        $r = new Redis();
        if (!$r->connect($host, $port, 2.0)) return $redis = false;
        if ($db) $r->select($db);
        return $redis = $r;
    } catch (Exception $e) {
        return $redis = false;
    }
}

function queue_enqueue($queue, array $payload)
{
    $data = json_encode(['t' => time(), 'payload' => $payload]);
    if ($r = queue_redis_client()) {
        return (bool) $r->lPush("q:$queue", $data);
    }
    // FS fallback
    $dir = 'f_data/queues/' . preg_replace('/[^a-z0-9:_-]/i', '_', $queue);
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $fn = $dir . '/' . microtime(true) . '-' . bin2hex(random_bytes(4)) . '.json';
    return (bool) @file_put_contents($fn, $data);
}

function queue_dequeue($queue, $timeout = 2)
{
    if ($r = queue_redis_client()) {
        $res = $r->brPop(["q:$queue"], $timeout);
        if (is_array($res) && count($res) === 2) {
            return json_decode($res[1], true);
        }
        return null;
    }
    // FS fallback (non-blocking, best effort)
    $dir = 'f_data/queues/' . preg_replace('/[^a-z0-9:_-]/i', '_', $queue);
    if (!is_dir($dir)) return null;
    $files = glob($dir . '/*.json');
    if (!$files) return null;
    sort($files);
    $fn = $files[0];
    $raw = @file_get_contents($fn);
    @unlink($fn);
    return $raw ? json_decode($raw, true) : null;
}

function queue_available()
{
    return (bool) queue_redis_client();
}

