<?php
define('_ISVALID', true);
include_once '../../../f_core/config.core.php';
require_once __DIR__ . '/../config/config.php';

use Donations\DonationHandler;

// Get streamer information
$streamer_id = $_GET['streamer_id'] ?? 0;
$sql = "SELECT username, display_name FROM users WHERE user_id = ?";
$streamer = db()->getRow($sql, [$streamer_id]);

if (!$streamer) {
    handle_error('Invalid streamer', 404);
}

// Initialize donation handler
$donation_handler = new DonationHandler();

// Load view
view('donation_form', [
    'streamer' => $streamer,
    'config' => $square_config
]); 