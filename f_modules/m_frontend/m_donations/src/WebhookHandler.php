<?php
namespace Donations;

class WebhookHandler {
    private $config;
    
    public function __construct() {
        global $square_config;
        $this->config = $square_config;
    }
    
    public function handle() {
        $payload = file_get_contents('php://input');
        
        if (!$this->verifySignature($payload)) {
            http_response_code(401);
            die('Invalid signature');
        }
        
        $event = json_decode($payload, true);
        
        switch ($event['type']) {
            case 'payment.created':
                $this->handlePaymentCreated($event['data']['object']);
                break;
            case 'payment.updated':
                $this->handlePaymentUpdated($event['data']['object']);
                break;
            case 'payout.created':
                $this->handlePayoutCreated($event['data']['object']);
                break;
            case 'payout.updated':
                $this->handlePayoutUpdated($event['data']['object']);
                break;
        }
        
        http_response_code(200);
        echo 'OK';
    }
    
    private function verifySignature($payload) {
        $signature = $_SERVER['HTTP_X-SQUARE-SIGNATURE'] ?? '';
        if (!$signature) {
            return false;
        }
        
        $computed_signature = hash_hmac('sha256', $payload, $this->config['square']['webhook_secret']);
        return hash_equals($computed_signature, $signature);
    }
    
    private function handlePaymentCreated($payment) {
        $metadata = $payment['metadata'] ?? [];
        $streamer_id = $metadata['streamer_id'] ?? null;
        
        if (!$streamer_id) {
            return;
        }
        
        $sql = "UPDATE donations SET 
                status = 'completed',
                updated_at = NOW()
                WHERE payment_id = ? AND streamer_id = ?";
        
        db()->executeQuery($sql, [$payment['id'], $streamer_id]);
        
        $this->logWebhookEvent('payment.created', $payment['id'], $streamer_id);
    }
    
    private function handlePaymentUpdated($payment) {
        $metadata = $payment['metadata'] ?? [];
        $streamer_id = $metadata['streamer_id'] ?? null;
        
        if (!$streamer_id) {
            return;
        }
        
        $status = $payment['status'] === 'COMPLETED' ? 'completed' : 'failed';
        
        $sql = "UPDATE donations SET 
                status = ?,
                updated_at = NOW()
                WHERE payment_id = ? AND streamer_id = ?";
        
        db()->executeQuery($sql, [$status, $payment['id'], $streamer_id]);
        
        $this->logWebhookEvent('payment.updated', $payment['id'], $streamer_id);
    }
    
    private function handlePayoutCreated($payout) {
        $sql = "UPDATE payouts SET 
                status = 'processing',
                updated_at = NOW()
                WHERE payout_id = ?";
        
        db()->executeQuery($sql, [$payout['id']]);
        
        $this->logWebhookEvent('payout.created', $payout['id']);
    }
    
    private function handlePayoutUpdated($payout) {
        $status = $payout['status'] === 'COMPLETED' ? 'completed' : 'failed';
        
        $sql = "UPDATE payouts SET 
                status = ?,
                updated_at = NOW()
                WHERE payout_id = ?";
        
        db()->executeQuery($sql, [$status, $payout['id']]);
        
        $this->logWebhookEvent('payout.updated', $payout['id']);
    }
    
    private function logWebhookEvent($event_type, $resource_id, $streamer_id = null) {
        $sql = "INSERT INTO webhook_logs (
                event_type, resource_id, streamer_id, created_at
            ) VALUES (?, ?, ?, NOW())";
        
        db()->executeQuery($sql, [$event_type, $resource_id, $streamer_id]);
    }
} 