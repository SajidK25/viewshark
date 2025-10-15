<?php
namespace Donations;

class GoalHandler {
    private $db;
    private $notification_handler;

    public function __construct() {
        $this->db = db();
        $this->notification_handler = new NotificationHandler();
    }

    /**
     * Create a new donation goal
     */
    public function createGoal($streamer_id, $title, $description, $target_amount, $end_date = null) {
        $sql = "INSERT INTO donation_goals 
                (streamer_id, title, description, target_amount, end_date) 
                VALUES (?, ?, ?, ?, ?)";
        
        $goal_id = $this->db->insert($sql, [
            $streamer_id, 
            $title, 
            $description, 
            $target_amount, 
            $end_date
        ]);

        // Create notification
        $this->notification_handler->createNotification(
            $streamer_id,
            'goal_created',
            'New Donation Goal Created',
            "A new donation goal '{$title}' has been created with a target of $" . number_format($target_amount, 2)
        );

        return $goal_id;
    }

    /**
     * Add a milestone to a goal
     */
    public function addMilestone($goal_id, $title, $description, $target_amount, $reward_description) {
        $sql = "INSERT INTO donation_milestones 
                (goal_id, title, description, target_amount, reward_description) 
                VALUES (?, ?, ?, ?, ?)";
        
        return $this->db->insert($sql, [
            $goal_id, 
            $title, 
            $description, 
            $target_amount, 
            $reward_description
        ]);
    }

    /**
     * Update goal progress
     */
    public function updateProgress($goal_id, $amount) {
        // Update goal progress
        $sql = "UPDATE donation_goals 
                SET current_amount = current_amount + ?,
                    status = CASE 
                        WHEN current_amount + ? >= target_amount THEN 'completed'
                        ELSE status 
                    END
                WHERE goal_id = ?";
        
        $this->db->query($sql, [$amount, $amount, $goal_id]);

        // Get goal details
        $goal = $this->getGoal($goal_id);
        
        // Check and update milestones
        $this->checkMilestones($goal_id, $goal['current_amount']);

        // If goal is completed, create notification
        if ($goal['current_amount'] >= $goal['target_amount']) {
            $this->notification_handler->createNotification(
                $goal['streamer_id'],
                'goal_completed',
                'Donation Goal Completed!',
                "Congratulations! The goal '{$goal['title']}' has been completed!"
            );
        }

        return $goal;
    }

    /**
     * Check and update milestones
     */
    private function checkMilestones($goal_id, $current_amount) {
        $sql = "SELECT * FROM donation_milestones 
                WHERE goal_id = ? 
                AND is_achieved = 0 
                AND target_amount <= ?";
        
        $achieved_milestones = $this->db->getRows($sql, [$goal_id, $current_amount]);

        foreach ($achieved_milestones as $milestone) {
            $this->markMilestoneAchieved($milestone['milestone_id']);
        }
    }

    /**
     * Mark a milestone as achieved
     */
    private function markMilestoneAchieved($milestone_id) {
        $sql = "UPDATE donation_milestones 
                SET is_achieved = 1, 
                    achieved_at = CURRENT_TIMESTAMP 
                WHERE milestone_id = ?";
        
        $this->db->query($sql, [$milestone_id]);

        // Get milestone and goal details for notification
        $sql = "SELECT m.*, g.streamer_id, g.title as goal_title 
                FROM donation_milestones m
                JOIN donation_goals g ON m.goal_id = g.goal_id
                WHERE m.milestone_id = ?";
        
        $milestone = $this->db->getRow($sql, [$milestone_id]);

        // Create notification
        $this->notification_handler->createNotification(
            $milestone['streamer_id'],
            'milestone_achieved',
            'Milestone Achieved!',
            "Milestone '{$milestone['title']}' has been achieved for goal '{$milestone['goal_title']}'!"
        );
    }

    /**
     * Get goal details
     */
    public function getGoal($goal_id) {
        $sql = "SELECT * FROM donation_goals WHERE goal_id = ?";
        return $this->db->getRow($sql, [$goal_id]);
    }

    /**
     * Get all goals for a streamer
     */
    public function getStreamerGoals($streamer_id) {
        $sql = "SELECT * FROM donation_goals 
                WHERE streamer_id = ? 
                ORDER BY created_at DESC";
        return $this->db->getRows($sql, [$streamer_id]);
    }

    /**
     * Get milestones for a goal
     */
    public function getGoalMilestones($goal_id) {
        $sql = "SELECT * FROM donation_milestones 
                WHERE goal_id = ? 
                ORDER BY target_amount ASC";
        return $this->db->getRows($sql, [$goal_id]);
    }

    /**
     * Get active goals for a streamer
     */
    public function getActiveGoals($streamer_id) {
        $sql = "SELECT * FROM donation_goals 
                WHERE streamer_id = ? 
                AND status = 'active' 
                ORDER BY created_at DESC";
        return $this->db->getRows($sql, [$streamer_id]);
    }
} 