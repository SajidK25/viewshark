<?php
define('_ISVALID', true);
include_once '../../../f_core/config.core.php';

// Load Square configuration
$square_config = require_once __DIR__ . '/config.square.php';

// Include Square SDK
require_once __DIR__ . '/vendor/autoload.php';

use Square\SquareClient;
use Square\Environment;
use Square\Models\CreatePaymentRequest;
use Square\Models\Money;

// Set JSON response header
header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request data'
    ]);
    exit;
}

// Validate required fields
if (!isset($data['streamer_id']) || !isset($data['amount'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

// Validate amount
if ($data['amount'] < $square_config['square']['min_donation'] || 
    $data['amount'] > $square_config['square']['max_donation']) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid donation amount'
    ]);
    exit;
}

try {
    // Initialize Square client
    $square_client = new SquareClient([
        'accessToken' => $square_config['square']['access_token'],
        'environment' => $square_config['square']['environment'] === 'production' ? Environment::PRODUCTION : Environment::SANDBOX
    ]);
    
    // Create payment request
    $money = new Money();
    $money->setAmount($data['amount'] * 100); // Convert to cents
    $money->setCurrency($square_config['square']['currency']);
    
    $payment_request = new CreatePaymentRequest();
    $payment_request->setSourceId('EXTERNAL');
    $payment_request->setAmountMoney($money);
    $payment_request->setLocationId($square_config['square']['location_id']);
    
    // Add metadata
    $payment_request->setMetadata([
        'streamer_id' => $data['streamer_id'],
        'donor_name' => $data['donor_name'] ?? '',
        'message' => $data['message'] ?? ''
    ]);
    
    // Create payment
    $payment = $square_client->getPaymentsApi()->createPayment($payment_request);
    
    if ($payment->isSuccess()) {
        // Record donation in database
        $sql = "INSERT INTO donations (
            streamer_id, amount, donor_name, message, payment_id, status, created_at
        ) VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        
        $params = [
            $data['streamer_id'],
            $data['amount'],
            $data['donor_name'] ?? '',
            $data['message'] ?? '',
            $payment->getResult()->getPayment()->getId()
        ];
        
        $class_database->executeQuery($sql, $params);
        
        // Get payment URL
        $payment_url = $payment->getResult()->getPayment()->getPaymentUrl();
        
        echo json_encode([
            'success' => true,
            'payment_url' => $payment_url,
            'message' => 'Payment created successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create payment: ' . $payment->getErrors()[0]->getDetail()
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error processing donation: ' . $e->getMessage()
    ]);
} 