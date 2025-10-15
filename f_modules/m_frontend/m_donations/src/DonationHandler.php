<?php
namespace Donations;

use Square\SquareClient;
use Square\Environment;
use Square\Models\CreatePaymentRequest;
use Square\Models\Money;

class DonationHandler {
    private $square_client;
    private $config;
    
    public function __construct() {
        global $square_config;
        $this->config = $square_config;
        
        // Initialize Square client
        $this->square_client = new SquareClient([
            'accessToken' => $this->config['square']['access_token'],
            'environment' => $this->config['square']['environment'] === 'production' ? Environment::PRODUCTION : Environment::SANDBOX
        ]);
    }
    
    public function createDonation($streamer_id, $amount, $donor_name = '', $message = '') {
        try {
            $this->validateAmount($amount);
            
            $payment = $this->createSquarePayment($streamer_id, $amount, $donor_name, $message);
            
            if ($payment->isSuccess()) {
                $this->recordDonation($streamer_id, $amount, $donor_name, $message, $payment->getResult()->getPayment()->getId());
                
                return [
                    'success' => true,
                    'payment_id' => $payment->getResult()->getPayment()->getId(),
                    'message' => 'Donation processed successfully'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Failed to process donation: ' . $payment->getErrors()[0]->getDetail()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error processing donation: ' . $e->getMessage()
            ];
        }
    }
    
    private function validateAmount($amount) {
        if ($amount < $this->config['square']['min_donation'] || 
            $amount > $this->config['square']['max_donation']) {
            throw new \Exception('Invalid donation amount');
        }
    }
    
    private function createSquarePayment($streamer_id, $amount, $donor_name, $message) {
        $money = new Money();
        $money->setAmount($amount * 100); // Convert to cents
        $money->setCurrency($this->config['square']['currency']);
        
        $payment_request = new CreatePaymentRequest();
        $payment_request->setSourceId('EXTERNAL');
        $payment_request->setAmountMoney($money);
        $payment_request->setLocationId($this->config['square']['location_id']);
        
        // Add metadata
        $payment_request->setMetadata([
            'streamer_id' => $streamer_id,
            'donor_name' => $donor_name,
            'message' => $message
        ]);
        
        return $this->square_client->getPaymentsApi()->createPayment($payment_request);
    }
    
    private function recordDonation($streamer_id, $amount, $donor_name, $message, $payment_id) {
        $sql = "INSERT INTO donations (
            streamer_id, amount, donor_name, message, payment_id, status, created_at
        ) VALUES (?, ?, ?, ?, ?, 'completed', NOW())";
        
        $params = [$streamer_id, $amount, $donor_name, $message, $payment_id];
        db()->executeQuery($sql, $params);
        
        // Update streamer's balance
        $sql = "UPDATE users SET donation_balance = donation_balance + ? WHERE user_id = ?";
        db()->executeQuery($sql, [$amount, $streamer_id]);
    }
    
    public function getStreamerBalance($streamer_id) {
        $sql = "SELECT donation_balance FROM users WHERE user_id = ?";
        $result = db()->getRow($sql, [$streamer_id]);
        return $result['donation_balance'] ?? 0;
    }
    
    public function requestPayout($streamer_id) {
        try {
            $balance = $this->getStreamerBalance($streamer_id);
            
            if ($balance < $this->config['streamer']['min_balance']) {
                return [
                    'success' => false,
                    'message' => 'Insufficient balance for payout'
                ];
            }
            
            $payout = $this->createSquarePayout($streamer_id, $balance);
            
            if ($payout->isSuccess()) {
                $this->recordPayout($streamer_id, $balance, $payout->getResult()->getPayout()->getId());
                
                return [
                    'success' => true,
                    'message' => 'Payout processed successfully',
                    'amount' => $balance
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Failed to process payout: ' . $payout->getErrors()[0]->getDetail()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error processing payout: ' . $e->getMessage()
            ];
        }
    }
    
    private function createSquarePayout($streamer_id, $amount) {
        // Calculate fees
        $fee_amount = ($amount * ($this->config['streamer']['payout_fee'] / 100)) + $this->config['streamer']['payout_fee_fixed'];
        $payout_amount = $amount - $fee_amount;
        
        return $this->square_client->getPayoutsApi()->createPayout([
            'amount_money' => [
                'amount' => $payout_amount * 100, // Convert to cents
                'currency' => $this->config['square']['currency']
            ],
            'location_id' => $this->config['square']['location_id']
        ]);
    }
    
    private function recordPayout($streamer_id, $amount, $payout_id) {
        // Calculate fees
        $fee_amount = ($amount * ($this->config['streamer']['payout_fee'] / 100)) + $this->config['streamer']['payout_fee_fixed'];
        $payout_amount = $amount - $fee_amount;
        
        // Record payout
        $sql = "INSERT INTO payouts (
            streamer_id, amount, fee, payout_id, status, created_at
        ) VALUES (?, ?, ?, ?, 'completed', NOW())";
        
        $params = [$streamer_id, $payout_amount, $fee_amount, $payout_id];
        db()->executeQuery($sql, $params);
        
        // Reset streamer's balance
        $sql = "UPDATE users SET donation_balance = 0 WHERE user_id = ?";
        db()->executeQuery($sql, [$streamer_id]);
    }
    
    public function getDonationHistory($streamer_id, $limit = 10) {
        $sql = "SELECT d.*, u.username as streamer_name 
                FROM donations d 
                JOIN users u ON d.streamer_id = u.user_id 
                WHERE d.streamer_id = ? 
                ORDER BY d.created_at DESC 
                LIMIT ?";
        
        return db()->getRows($sql, [$streamer_id, $limit]);
    }
} 