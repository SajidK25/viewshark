<?php
/*******************************************************************************************************************
| Send Notification Job
| Handles sending notifications to users
|*******************************************************************************************************************/

class SendNotificationJob extends BaseJob
{
    /**
     * Handle notification sending
     * @param array $data Notification data
     * @return bool Success status
     */
    public function handle($data)
    {
        $this->validateData($data, ['user_id', 'type', 'message']);
        
        $userId = $data['user_id'];
        $type = $data['type'];
        $message = $data['message'];
        $title = $data['title'] ?? 'EasyStream Notification';
        $actionUrl = $data['action_url'] ?? null;
        $metadata = $data['metadata'] ?? [];
        
        $this->logProgress('Sending notification', [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title
        ]);
        
        try {
            // Store notification in database
            $notificationId = $this->storeNotification($userId, $type, $title, $message, $actionUrl, $metadata);
            
            // Send real-time notification if user is online
            $this->sendRealTimeNotification($userId, $notificationId, $type, $title, $message, $actionUrl);
            
            // Send email notification if enabled for user
            $this->sendEmailNotification($userId, $type, $title, $message, $actionUrl);
            
            // Send push notification if user has enabled it
            $this->sendPushNotification($userId, $type, $title, $message, $actionUrl);
            
            $this->logProgress('Notification sent successfully', [
                'user_id' => $userId,
                'notification_id' => $notificationId,
                'type' => $type
            ]);
            
            return true;
            
        } catch (Exception $e) {
            $this->logError('Failed to send notification', [
                'user_id' => $userId,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Store notification in database
     * @param int $userId User ID
     * @param string $type Notification type
     * @param string $title Title
     * @param string $message Message
     * @param string $actionUrl Action URL
     * @param array $metadata Metadata
     * @return int Notification ID
     */
    private function storeNotification($userId, $type, $title, $message, $actionUrl, $metadata)
    {
        $db = $this->getDatabase();
        
        // Create table if it doesn't exist
        $this->createNotificationTable();
        
        $notificationData = [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'metadata' => json_encode($metadata),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $success = $db->doInsert('db_notifications', $notificationData);
        
        if (!$success) {
            throw new Exception('Failed to store notification in database');
        }
        
        // Get the inserted notification ID
        global $db as $adodb;
        return $adodb->Insert_ID();
    }
    
    /**
     * Send real-time notification via WebSocket/Server-Sent Events
     * @param int $userId User ID
     * @param int $notificationId Notification ID
     * @param string $type Type
     * @param string $title Title
     * @param string $message Message
     * @param string $actionUrl Action URL
     */
    private function sendRealTimeNotification($userId, $notificationId, $type, $title, $message, $actionUrl)
    {
        try {
            $redis = $this->getRedis();
            
            $notificationData = [
                'id' => $notificationId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'timestamp' => time()
            ];
            
            // Store in Redis for real-time delivery
            $redis->lpush("notifications:user:{$userId}", json_encode($notificationData));
            
            // Limit to last 50 notifications per user
            $redis->getRedis()->ltrim("notifications:user:{$userId}", 0, 49);
            
            // Publish to notification channel for WebSocket delivery
            $redis->getRedis()->publish("notifications", json_encode([
                'user_id' => $userId,
                'notification' => $notificationData
            ]));
            
            $this->logProgress('Real-time notification queued', [
                'user_id' => $userId,
                'notification_id' => $notificationId
            ]);
            
        } catch (Exception $e) {
            $this->logError('Failed to send real-time notification', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Send email notification if user has enabled it
     * @param int $userId User ID
     * @param string $type Type
     * @param string $title Title
     * @param string $message Message
     * @param string $actionUrl Action URL
     */
    private function sendEmailNotification($userId, $type, $title, $message, $actionUrl)
    {
        try {
            // Check if user wants email notifications for this type
            if (!$this->userWantsEmailNotification($userId, $type)) {
                return;
            }
            
            // Get user email
            $userEmail = $this->getUserEmail($userId);
            if (!$userEmail) {
                return;
            }
            
            // Prepare email content
            $emailSubject = "EasyStream: {$title}";
            $emailMessage = $this->buildEmailTemplate($title, $message, $actionUrl);
            
            // Queue email job
            $queue = new VQueue();
            $queue->enqueue('SendEmailJob', [
                'to' => $userEmail,
                'subject' => $emailSubject,
                'message' => $emailMessage,
                'from' => 'notifications@easystream.com'
            ], 'email');
            
            $this->logProgress('Email notification queued', [
                'user_id' => $userId,
                'email' => $userEmail,
                'type' => $type
            ]);
            
        } catch (Exception $e) {
            $this->logError('Failed to queue email notification', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Send push notification
     * @param int $userId User ID
     * @param string $type Type
     * @param string $title Title
     * @param string $message Message
     * @param string $actionUrl Action URL
     */
    private function sendPushNotification($userId, $type, $title, $message, $actionUrl)
    {
        try {
            // Get user's push notification tokens
            $pushTokens = $this->getUserPushTokens($userId);
            
            if (empty($pushTokens)) {
                return;
            }
            
            // Queue push notification job for each token
            $queue = new VQueue();
            
            foreach ($pushTokens as $token) {
                $queue->enqueue('SendPushNotificationJob', [
                    'token' => $token,
                    'title' => $title,
                    'message' => $message,
                    'action_url' => $actionUrl,
                    'user_id' => $userId
                ], 'notifications');
            }
            
            $this->logProgress('Push notifications queued', [
                'user_id' => $userId,
                'token_count' => count($pushTokens),
                'type' => $type
            ]);
            
        } catch (Exception $e) {
            $this->logError('Failed to queue push notifications', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Check if user wants email notifications for this type
     * @param int $userId User ID
     * @param string $type Notification type
     * @return bool
     */
    private function userWantsEmailNotification($userId, $type)
    {
        try {
            $db = $this->getDatabase();
            
            // Check user notification preferences
            $preference = $db->singleFieldValue('db_user_preferences', 'email_notifications', 'user_id', $userId);
            
            if ($preference === null) {
                return true; // Default to enabled
            }
            
            $preferences = json_decode($preference, true);
            return isset($preferences[$type]) ? $preferences[$type] : true;
            
        } catch (Exception $e) {
            return true; // Default to enabled on error
        }
    }
    
    /**
     * Get user email address
     * @param int $userId User ID
     * @return string|null Email address
     */
    private function getUserEmail($userId)
    {
        try {
            $db = $this->getDatabase();
            return $db->singleFieldValue('db_accountuser', 'usr_email', 'usr_id', $userId);
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Get user push notification tokens
     * @param int $userId User ID
     * @return array Push tokens
     */
    private function getUserPushTokens($userId)
    {
        try {
            $db = $this->getDatabase();
            
            // This would need a push_tokens table
            // For now, return empty array
            return [];
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Build email template
     * @param string $title Title
     * @param string $message Message
     * @param string $actionUrl Action URL
     * @return string HTML email content
     */
    private function buildEmailTemplate($title, $message, $actionUrl)
    {
        $actionButton = '';
        if ($actionUrl) {
            $actionButton = '<p style="text-align: center; margin: 30px 0;">
                <a href="' . htmlspecialchars($actionUrl) . '" 
                   style="background: #4A90E2; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">
                   View Details
                </a>
            </p>';
        }
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>' . htmlspecialchars($title) . '</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #4A90E2;">EasyStream</h1>
            </div>
            
            <h2 style="color: #333;">' . htmlspecialchars($title) . '</h2>
            
            <div style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0;">
                ' . nl2br(htmlspecialchars($message)) . '
            </div>
            
            ' . $actionButton . '
            
            <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
            
            <p style="font-size: 12px; color: #666; text-align: center;">
                This is an automated message from EasyStream. Please do not reply to this email.
            </p>
        </body>
        </html>';
    }
    
    /**
     * Create notification table if it doesn't exist
     */
    private function createNotificationTable()
    {
        global $db;
        
        $sql = "CREATE TABLE IF NOT EXISTS `db_notifications` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `type` varchar(50) NOT NULL,
            `title` varchar(255) NOT NULL,
            `message` text NOT NULL,
            `action_url` varchar(500) DEFAULT NULL,
            `metadata` text,
            `is_read` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` datetime NOT NULL,
            `read_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_user_id` (`user_id`),
            KEY `idx_type` (`type`),
            KEY `idx_is_read` (`is_read`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $db->Execute($sql);
        } catch (Exception $e) {
            $this->logError('Failed to create notification table', [
                'error' => $e->getMessage()
            ]);
        }
    }
}