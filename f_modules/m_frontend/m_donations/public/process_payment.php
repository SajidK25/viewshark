<?php
define('_ISVALID', true);
include_once '../../../f_core/config.core.php';
require_once __DIR__ . '/../config/config.php';

use Donations\DonationHandler;

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    handle_error('Invalid request data', 400);
}

// Validate required fields
$required_fields = ['nonce', 'amount', 'streamer_id'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        handle_error("Missing required field: {$field}", 400);
    }
}

// Initialize donation handler
$donation_handler = new DonationHandler();

try {
    // Process the donation
    $result = $donation_handler->createDonation(
        $input['streamer_id'],
        $input['amount'],
        $input['nonce'],
        $input['message'] ?? null
    );

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Donation processed successfully',
        'donation_id' => $result['donation_id']
    ]);
} catch (Exception $e) {
    // Log error
    error_log("Donation processing error: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 