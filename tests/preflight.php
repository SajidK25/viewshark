<?php
// Simple preflight to verify runtime, perms, DB, and tools
// WARNING: Exposes environment details; only enable in dev. Gate with env vars.

header('Content-Type: text/plain; charset=utf-8');

$enabled = getenv('PREFLIGHT_ENABLE') === '1';
$token   = getenv('PREFLIGHT_TOKEN');
if (!$enabled && (!isset($_GET['token']) || $_GET['token'] !== $token)) {
    http_response_code(403);
    echo "Preflight disabled\n";
    exit;
}

function line($label, $ok, $extra = '') {
    echo sprintf("[%s] %s%s\n", $ok ? 'OK' : 'FAIL', $label, ($extra !== '' ? " - $extra" : ''));
}

// PHP version and extensions
$ok_php = version_compare(PHP_VERSION, '8.2.0', '>=');
line('PHP >= 8.2', $ok_php, PHP_VERSION);

$exts = ['mysqli','pdo_mysql','gd','exif','zip','intl','sockets'];
foreach ($exts as $e) {
    line("ext:$e", extension_loaded($e));
}

// ffmpeg / ffprobe
$ffmpeg  = trim(shell_exec('command -v ffmpeg 2>/dev/null'));
$ffprobe = trim(shell_exec('command -v ffprobe 2>/dev/null'));
line('ffmpeg present', $ffmpeg !== '', $ffmpeg);
line('ffprobe present', $ffprobe !== '', $ffprobe);

// Writable directories
$paths = [
    'f_data/data_cache/_c_db',
    'f_data/data_cache/_c_tpl',
    'f_data/data_languages',
    'f_data/data_logs/log_conv',
    'f_data/data_logs/log_dl',
    'f_data/data_logs/log_error',
    'f_data/data_logs/log_mail',
    'f_data/data_logs/log_pp',
    'f_data/data_logs/log_xfer',
    'f_data/data_sessions',
    'f_data/data_sitemaps/sm_global',
    'f_data/data_sitemaps/sm_image',
    'f_data/data_sitemaps/sm_short',
    'f_data/data_sitemaps/sm_video',
    'f_data/data_userfiles/user_media',
    'f_data/data_userfiles/user_profile',
    'f_data/data_userfiles/user_uploads',
    'f_data/data_userfiles/user_views',
];
foreach ($paths as $p) {
    $exists = is_dir($p);
    $w      = $exists && is_writable($p);
    line("writable:$p", $w, $exists ? '' : 'missing');
}

// DB connectivity and tables
define('_ISVALID', true);
require_once __DIR__ . '/../f_core/config.database.php';
$dbhost = $cfg_dbhost ?? 'localhost';
$dbname = $cfg_dbname ?? '';
$dbuser = $cfg_dbuser ?? '';
$dbpass = $cfg_dbpass ?? '';

$link = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
line('DB connect', (bool) $link, $link ? '' : mysqli_connect_error());

$tables = [
    'db_settings','db_accountuser','db_videofiles','db_livefiles','db_servers',
    'db_followers','db_subscribers','db_notifications_count'
];
if ($link) {
    foreach ($tables as $t) {
        $res = @mysqli_query($link, "SHOW TABLES LIKE '" . mysqli_real_escape_string($link, $t) . "'");
        $ok  = $res && mysqli_num_rows($res) > 0;
        line("table:$t", $ok);
        if ($res) mysqli_free_result($res);
    }
    mysqli_close($link);
}

echo "\nDone.\n";

