-- Add donation_balance column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS donation_balance DECIMAL(10,2) DEFAULT 0.00;

-- Create donations table
CREATE TABLE IF NOT EXISTS donations (
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    streamer_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    donor_name VARCHAR(255),
    message TEXT,
    payment_id VARCHAR(255) NOT NULL,
    status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (streamer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_payment_id (payment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create payouts table
CREATE TABLE IF NOT EXISTS payouts (
    payout_id INT AUTO_INCREMENT PRIMARY KEY,
    streamer_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    fee DECIMAL(10,2) NOT NULL,
    payout_id VARCHAR(255) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (streamer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_payout_id (payout_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create webhook_logs table
CREATE TABLE IF NOT EXISTS webhook_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    resource_id VARCHAR(255) NOT NULL,
    streamer_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (streamer_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_donations_streamer_id ON donations(streamer_id);
CREATE INDEX IF NOT EXISTS idx_donations_status ON donations(status);
CREATE INDEX IF NOT EXISTS idx_payouts_streamer_id ON payouts(streamer_id);
CREATE INDEX IF NOT EXISTS idx_payouts_status ON payouts(status);
CREATE INDEX IF NOT EXISTS idx_webhook_logs_event_type ON webhook_logs(event_type);
CREATE INDEX IF NOT EXISTS idx_webhook_logs_resource_id ON webhook_logs(resource_id); 