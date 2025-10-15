<?php
// Module paths
define('DONATIONS_PATH', __DIR__ . '/..');
define('DONATIONS_SRC', DONATIONS_PATH . '/src');
define('DONATIONS_PUBLIC', DONATIONS_PATH . '/public');
define('DONATIONS_VIEWS', DONATIONS_PATH . '/views');
define('DONATIONS_ASSETS', DONATIONS_PATH . '/assets');

// Autoloader
spl_autoload_register(function ($class) {
    $file = DONATIONS_SRC . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require_once $file;
});

// Helper functions
function redirect($url) {
    header("Location: $url");
    exit;
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function view($template, $data = []) {
    extract($data);
    require DONATIONS_VIEWS . "/$template.php";
}

function handle_error($message, $code = 500) {
    if (request_wants_json()) {
        json_response(['error' => $message], $code);
    } else {
        http_response_code($code);
        view('error', ['message' => $message]);
    }
}

function request_wants_json() {
    return isset($_SERVER['HTTP_ACCEPT']) && 
           strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
}

function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function db() {
    global $class_database;
    return $class_database;
}

// Module configuration
return [
    'square' => [
        'application_id' => 'YOUR_SQUARE_APP_ID',
        'access_token' => 'YOUR_SQUARE_ACCESS_TOKEN',
        'location_id' => 'YOUR_SQUARE_LOCATION_ID',
        'environment' => 'sandbox',
        'currency' => 'USD',
        'min_donation' => 1.00,
        'max_donation' => 1000.00,
        'default_amounts' => [5, 10, 25, 50, 100],
        'webhook_secret' => 'YOUR_WEBHOOK_SECRET'
    ],
    'payout' => [
        'min_balance' => 50.00,
        'payout_fee' => 0.05,
        'payout_fee_fixed' => 1.00
    ],
    'api' => [
        'rate_limit' => 100,
        'rate_window' => 60,
        'allowed_ips' => []
    ],
    'tables' => [
        'donations' => 'donations',
        'donation_goals' => 'donation_goals',
        'donation_milestones' => 'donation_milestones',
        'donation_analytics' => 'donation_analytics',
        'donation_notifications' => 'donation_notifications',
        'api_keys' => 'api_keys',
        'api_rate_limits' => 'api_rate_limits'
    ],
    'notifications' => [
        'retention_days' => 30,
        'batch_size' => 100
    ],
    'analytics' => [
        'default_period' => 30,
        'cache_duration' => 3600,
        'min_data_points' => 10
    ],
    'security' => [
        'allowed_origins' => [],
        'max_request_size' => '10M',
        'session_timeout' => 3600
    ]
]; 