<?php
define('_ISVALID', true);
include_once '../../../f_core/config.core.php';
require_once __DIR__ . '/../config/config.php';

use Donations\DonationHandler;

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    json_response([
        'success' => false,
        'message' => 'Invalid request data'
    ], 400);
}

// Validate required fields
if (!isset($data['streamer_id']) || !isset($data['amount'])) {
    json_response([
        'success' => false,
        'message' => 'Missing required fields'
    ], 400);
}

// Process donation
$handler = new DonationHandler();
$result = $handler->createDonation(
    $data['streamer_id'],
    $data['amount'],
    $data['donor_name'] ?? '',
    $data['message'] ?? ''
);

json_response($result); 