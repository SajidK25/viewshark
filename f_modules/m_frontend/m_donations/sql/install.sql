-- Create tables
CREATE TABLE IF NOT EXISTS donations (
    donation_id INT PRIMARY KEY AUTO_INCREMENT,
    streamer_id INT NOT NULL,
    donor_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_id VARCHAR(255) NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (streamer_id) REFERENCES users(user_id),
    FOREIGN KEY (donor_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS donation_goals (
    goal_id INT PRIMARY KEY AUTO_INCREMENT,
    streamer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    target_amount DECIMAL(10,2) NOT NULL,
    current_amount DECIMAL(10,2) DEFAULT 0.00,
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date TIMESTAMP NULL,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (streamer_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS donation_milestones (
    milestone_id INT PRIMARY KEY AUTO_INCREMENT,
    goal_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    target_amount DECIMAL(10,2) NOT NULL,
    reward_description TEXT,
    is_completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (goal_id) REFERENCES donation_goals(goal_id)
);

CREATE TABLE IF NOT EXISTS donation_analytics (
    analytics_id INT PRIMARY KEY AUTO_INCREMENT,
    streamer_id INT NOT NULL,
    date DATE NOT NULL,
    total_donations INT DEFAULT 0,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    average_donation DECIMAL(10,2) DEFAULT 0.00,
    unique_donors INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (streamer_id) REFERENCES users(user_id),
    UNIQUE KEY unique_streamer_date (streamer_id, date)
);

CREATE TABLE IF NOT EXISTS donation_notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    streamer_id INT NOT NULL,
    type ENUM('donation', 'goal', 'milestone', 'payout') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (streamer_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS api_keys (
    key_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    api_key VARCHAR(64) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    last_used TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS api_rate_limits (
    rate_limit_id INT PRIMARY KEY AUTO_INCREMENT,
    api_key VARCHAR(64) NOT NULL,
    request_count INT DEFAULT 1,
    window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (api_key) REFERENCES api_keys(api_key)
);

-- Create indexes
CREATE INDEX idx_donations_streamer ON donations(streamer_id);
CREATE INDEX idx_donations_donor ON donations(donor_id);
CREATE INDEX idx_donations_status ON donations(status);
CREATE INDEX idx_goals_streamer ON donation_goals(streamer_id);
CREATE INDEX idx_goals_status ON donation_goals(status);
CREATE INDEX idx_milestones_goal ON donation_milestones(goal_id);
CREATE INDEX idx_analytics_streamer_date ON donation_analytics(streamer_id, date);
CREATE INDEX idx_notifications_streamer ON donation_notifications(streamer_id);
CREATE INDEX idx_notifications_read ON donation_notifications(is_read);
CREATE INDEX idx_api_key_active ON api_keys(api_key, is_active);
CREATE INDEX idx_rate_limit_window ON api_rate_limits(api_key, window_start);

-- Create views
CREATE OR REPLACE VIEW v_streamer_donations AS
SELECT d.*, u.username as donor_username, u.display_name as donor_display_name
FROM donations d JOIN users u ON d.donor_id = u.user_id;

CREATE OR REPLACE VIEW v_streamer_goals AS
SELECT g.*, 
       COUNT(m.milestone_id) as milestone_count,
       SUM(CASE WHEN m.is_completed = 1 THEN 1 ELSE 0 END) as completed_milestones
FROM donation_goals g
LEFT JOIN donation_milestones m ON g.goal_id = m.goal_id
GROUP BY g.goal_id;

-- Create triggers
DELIMITER //

CREATE TRIGGER tr_update_goal_amount
AFTER INSERT ON donations
FOR EACH ROW
BEGIN
    UPDATE donation_goals
    SET current_amount = current_amount + NEW.amount
    WHERE streamer_id = NEW.streamer_id
    AND status = 'active'
    AND (end_date IS NULL OR end_date > NOW());
END//

CREATE TRIGGER tr_check_milestone_completion
AFTER UPDATE ON donation_goals
FOR EACH ROW
BEGIN
    UPDATE donation_milestones
    SET is_completed = 1, completed_at = NOW()
    WHERE goal_id = NEW.goal_id
    AND is_completed = 0
    AND target_amount <= NEW.current_amount;
END//

DELIMITER ; 