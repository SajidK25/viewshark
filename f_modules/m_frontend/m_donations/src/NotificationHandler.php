<?php
namespace Donations;

class NotificationHandler {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    /**
     * Create a new notification
     */
    public function createNotification($streamer_id, $type, $title, $message, $donor_id = null) {
        $sql = "INSERT INTO donation_notifications 
                (streamer_id, donor_id, type, title, message) 
                VALUES (?, ?, ?, ?, ?)";
        
        return $this->db->insert($sql, [
            $streamer_id,
            $donor_id,
            $type,
            $title,
            $message
        ]);
    }

    /**
     * Get unread notifications for a streamer
     */
    public function getUnreadNotifications($streamer_id, $limit = 10) {
        $sql = "SELECT * FROM donation_notifications 
                WHERE streamer_id = ? 
                AND is_read = 0 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        return $this->db->getRows($sql, [$streamer_id, $limit]);
    }

    /**
     * Get all notifications for a streamer
     */
    public function getAllNotifications($streamer_id, $limit = 20) {
        $sql = "SELECT * FROM donation_notifications 
                WHERE streamer_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        return $this->db->getRows($sql, [$streamer_id, $limit]);
    }

    /**
     * Mark notifications as read
     */
    public function markAsRead($notification_ids) {
        if (empty($notification_ids)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($notification_ids) - 1) . '?';
        $sql = "UPDATE donation_notifications 
                SET is_read = 1 
                WHERE notification_id IN ($placeholders)";
        
        return $this->db->query($sql, $notification_ids);
    }

    /**
     * Get notification count
     */
    public function getUnreadCount($streamer_id) {
        $sql = "SELECT COUNT(*) as count 
                FROM donation_notifications 
                WHERE streamer_id = ? 
                AND is_read = 0";
        
        $result = $this->db->getRow($sql, [$streamer_id]);
        return $result['count'];
    }

    /**
     * Delete old notifications
     */
    public function cleanupOldNotifications($days = 30) {
        $sql = "DELETE FROM donation_notifications 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        return $this->db->query($sql, [$days]);
    }

    /**
     * Get notification by ID
     */
    public function getNotification($notification_id) {
        $sql = "SELECT * FROM donation_notifications WHERE notification_id = ?";
        return $this->db->getRow($sql, [$notification_id]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($streamer_id) {
        $sql = "UPDATE donation_notifications 
                SET is_read = 1 
                WHERE streamer_id = ?";
        
        return $this->db->query($sql, [$streamer_id]);
    }
} 