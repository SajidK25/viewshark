-- Donation Goals Table
CREATE TABLE donation_goals (
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

-- Donation Milestones Table
CREATE TABLE donation_milestones (
    milestone_id INT PRIMARY KEY AUTO_INCREMENT,
    goal_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    target_amount DECIMAL(10,2) NOT NULL,
    reward_description TEXT,
    is_achieved BOOLEAN DEFAULT FALSE,
    achieved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (goal_id) REFERENCES donation_goals(goal_id)
);

-- Donation Analytics Table
CREATE TABLE donation_analytics (
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

-- Donation Notifications Table
CREATE TABLE donation_notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    streamer_id INT NOT NULL,
    donor_id INT,
    type ENUM('donation', 'goal_created', 'goal_completed', 'milestone_achieved') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (streamer_id) REFERENCES users(user_id),
    FOREIGN KEY (donor_id) REFERENCES users(user_id)
); 