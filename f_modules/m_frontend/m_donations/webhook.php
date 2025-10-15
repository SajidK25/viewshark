<?php
define('_ISVALID', true);
include_once '../../../f_core/config.core.php';

// Load Square configuration
$square_config = require_once __DIR__ . '/config.square.php';

// Get the raw POST data
$payload = file_get_contents('php://input');

// Verify webhook signature
$signature = $_SERVER['HTTP_X-SQUARE-SIGNATURE'] ?? '';
if (!$signature || !verifySquareWebhook($payload, $signature, $square_config['square']['webhook_secret'])) {
    http_response_code(401);
    die('Invalid signature');
}

// Parse the webhook payload
$event = json_decode($payload, true);

// Process the event based on type
switch ($event['type']) {
    case 'payment.created':
        handlePaymentCreated($event['data']['object']);
        break;
    case 'payment.updated':
        handlePaymentUpdated($event['data']['object']);
        break;
    case 'payout.created':
        handlePayoutCreated($event['data']['object']);
        break;
    case 'payout.updated':
        handlePayoutUpdated($event['data']['object']);
        break;
}

// Return success response
http_response_code(200);
echo 'OK';

function verifySquareWebhook($payload, $signature, $webhook_secret) {
    $computed_signature = hash_hmac('sha256', $payload, $webhook_secret);
    return hash_equals($computed_signature, $signature);
}

function handlePaymentCreated($payment) {
    global $class_database;
    
    // Get metadata
    $metadata = $payment['metadata'] ?? [];
    $streamer_id = $metadata['streamer_id'] ?? null;
    
    if (!$streamer_id) {
        return;
    }
    
    // Update donation status
    $sql = "UPDATE donations SET 
            status = 'completed',
            updated_at = NOW()
            WHERE payment_id = ? AND streamer_id = ?";
    
    $class_database->executeQuery($sql, [$payment['id'], $streamer_id]);
    
    // Log the event
    logWebhookEvent('payment.created', $payment['id'], $streamer_id);
}

function handlePaymentUpdated($payment) {
    global $class_database;
    
    // Get metadata
    $metadata = $payment['metadata'] ?? [];
    $streamer_id = $metadata['streamer_id'] ?? null;
    
    if (!$streamer_id) {
        return;
    }
    
    // Update donation status based on payment status
    $status = $payment['status'] === 'COMPLETED' ? 'completed' : 'failed';
    
    $sql = "UPDATE donations SET 
            status = ?,
            updated_at = NOW()
            WHERE payment_id = ? AND streamer_id = ?";
    
    $class_database->executeQuery($sql, [$status, $payment['id'], $streamer_id]);
    
    // Log the event
    logWebhookEvent('payment.updated', $payment['id'], $streamer_id);
}

function handlePayoutCreated($payout) {
    global $class_database;
    
    // Update payout status
    $sql = "UPDATE payouts SET 
            status = 'processing',
            updated_at = NOW()
            WHERE payout_id = ?";
    
    $class_database->executeQuery($sql, [$payout['id']]);
    
    // Log the event
    logWebhookEvent('payout.created', $payout['id']);
}

function handlePayoutUpdated($payout) {
    global $class_database;
    
    // Update payout status
    $status = $payout['status'] === 'COMPLETED' ? 'completed' : 'failed';
    
    $sql = "UPDATE payouts SET 
            status = ?,
            updated_at = NOW()
            WHERE payout_id = ?";
    
    $class_database->executeQuery($sql, [$status, $payout['id']]);
    
    // Log the event
    logWebhookEvent('payout.updated', $payout['id']);
}

function logWebhookEvent($event_type, $resource_id, $streamer_id = null) {
    global $class_database;
    
    $sql = "INSERT INTO webhook_logs (
            event_type, resource_id, streamer_id, created_at
        ) VALUES (?, ?, ?, NOW())";
    
    $class_database->executeQuery($sql, [$event_type, $resource_id, $streamer_id]);
} 