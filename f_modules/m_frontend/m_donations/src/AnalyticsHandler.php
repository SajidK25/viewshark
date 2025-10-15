<?php
namespace Donations;

class AnalyticsHandler {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    /**
     * Update analytics for a new donation
     */
    public function updateAnalytics($streamer_id, $amount, $donor_id) {
        $date = date('Y-m-d');
        
        // Get or create analytics record for today
        $sql = "INSERT INTO donation_analytics 
                (streamer_id, date, total_donations, total_amount, unique_donors) 
                VALUES (?, ?, 1, ?, 1)
                ON DUPLICATE KEY UPDATE 
                total_donations = total_donations + 1,
                total_amount = total_amount + ?,
                unique_donors = (
                    SELECT COUNT(DISTINCT donor_id) 
                    FROM donations 
                    WHERE streamer_id = ? AND DATE(created_at) = ?
                )";
        
        $this->db->query($sql, [$streamer_id, $date, $amount, $amount, $streamer_id, $date]);
        
        // Update average donation
        $sql = "UPDATE donation_analytics 
                SET average_donation = total_amount / total_donations 
                WHERE streamer_id = ? AND date = ?";
        
        $this->db->query($sql, [$streamer_id, $date]);
    }

    /**
     * Get analytics for a specific period
     */
    public function getAnalytics($streamer_id, $start_date, $end_date) {
        $sql = "SELECT 
                    date,
                    total_donations,
                    total_amount,
                    average_donation,
                    unique_donors
                FROM donation_analytics
                WHERE streamer_id = ? 
                AND date BETWEEN ? AND ?
                ORDER BY date DESC";
        
        return $this->db->getRows($sql, [$streamer_id, $start_date, $end_date]);
    }

    /**
     * Get summary statistics
     */
    public function getSummary($streamer_id) {
        $sql = "SELECT 
                    COUNT(*) as total_donations,
                    SUM(amount) as total_amount,
                    AVG(amount) as average_donation,
                    COUNT(DISTINCT donor_id) as unique_donors,
                    MAX(amount) as largest_donation,
                    MIN(amount) as smallest_donation
                FROM donations
                WHERE streamer_id = ?";
        
        return $this->db->getRow($sql, [$streamer_id]);
    }

    /**
     * Get top donors
     */
    public function getTopDonors($streamer_id, $limit = 10) {
        $sql = "SELECT 
                    u.username,
                    u.display_name,
                    COUNT(*) as donation_count,
                    SUM(d.amount) as total_amount
                FROM donations d
                JOIN users u ON d.donor_id = u.user_id
                WHERE d.streamer_id = ?
                GROUP BY d.donor_id
                ORDER BY total_amount DESC
                LIMIT ?";
        
        return $this->db->getRows($sql, [$streamer_id, $limit]);
    }

    /**
     * Get donation trends
     */
    public function getTrends($streamer_id, $days = 30) {
        $sql = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as count,
                    SUM(amount) as total
                FROM donations
                WHERE streamer_id = ?
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY date";
        
        return $this->db->getRows($sql, [$streamer_id, $days]);
    }
} 