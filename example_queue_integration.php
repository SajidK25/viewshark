<?php
/*******************************************************************************************************************
| Example: Queue System Integration
| This file demonstrates how to integrate the queue system into your EasyStream application
|*******************************************************************************************************************/

define('_ISVALID', true);
include_once 'f_core/config.core.php';

// Example 1: User Registration with Welcome Email
function handleUserRegistration($userData) {
    global $class_database;
    
    // Save user to database
    $userId = $class_database->doInsert('db_accountuser', $userData);
    
    if ($userId) {
        // Enqueue welcome email (runs in background)
        enqueue_welcome_email(
            $userId,
            $userData['usr_email'],
            $userData['usr_user']
        );
        
        // Enqueue welcome notification
        enqueue_notification(
            $userId,
            'welcome',
            'Welcome to EasyStream! Start by uploading your first video.',
            'Welcome to EasyStream!'
        );
        
        return $userId;
    }
    
    return false;
}

// Example 2: Video Upload Processing
function handleVideoUpload($videoData, $uploadedFile) {
    global $class_database;
    
    // Save video metadata to database
    $videoId = $class_database->doInsert('db_videofiles', $videoData);
    
    if ($videoId) {
        // Move uploaded file to processing directory
        $processingDir = 'f_data/data_userfiles/processing/';
        $inputFile = $processingDir . $videoId . '_original.mp4';
        $outputDir = 'f_data/data_userfiles/user_media/' . $videoData['usr_id'] . '/v/';
        
        if (move_uploaded_file($uploadedFile['tmp_name'], $inputFile)) {
            // Enqueue video processing job
            $jobId = enqueue_video_processing(
                $videoId,
                $inputFile,
                $outputDir,
                ['1080p', '720p', '480p', '360p']
            );
            
            // Notify user that upload is complete and processing started
            enqueue_video_upload_notification(
                $videoData['usr_id'],
                $videoData['file_title'],
                '/watch/' . $videoId
            );
            
            VLogger::getInstance()->info('Video upload queued for processing', [
                'video_id' => $videoId,
                'job_id' => $jobId,
                'user_id' => $videoData['usr_id']
            ]);
            
            return $videoId;
        }
    }
    
    return false;
}

// Example 3: Password Reset Request
function handlePasswordResetRequest($email) {
    global $class_database;
    
    // Check if user exists
    $user = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_email', $email);
    
    if ($user) {
        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));
        $resetUrl = 'https://yourdomain.com/reset-password?token=' . $resetToken;
        
        // Store reset token in database
        $resetData = [
            'usr_id' => $user,
            'reset_token' => $resetToken,
            'expires_at' => date('Y-m-d H:i:s', time() + 86400), // 24 hours
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $class_database->doInsert('db_password_resets', $resetData);
        
        // Enqueue password reset email
        enqueue_password_reset_email($email, $resetToken, $resetUrl);
        
        return true;
    }
    
    return false;
}

// Example 4: Bulk Notification to Subscribers
function notifySubscribersOfNewVideo($channelId, $videoId, $videoTitle) {
    global $class_database, $db;
    
    // Get all subscribers of the channel
    $sql = "SELECT usr_id FROM db_subscriptions WHERE channel_id = ? AND active = 1";
    $result = $db->Execute($sql, [$channelId]);
    
    $jobs = [];
    $videoUrl = '/watch/' . $videoId;
    
    // Get channel name
    $channelName = $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_id', $channelId);
    
    while ($result && !$result->EOF) {
        $subscriberId = $result->fields['usr_id'];
        
        // Prepare notification job for each subscriber
        $jobs[] = [
            'class' => 'SendNotificationJob',
            'data' => [
                'user_id' => $subscriberId,
                'type' => 'new_video',
                'message' => "{$channelName} has uploaded a new video: '{$videoTitle}'",
                'title' => 'New Video from Subscription',
                'action_url' => $videoUrl,
                'metadata' => [
                    'channel_id' => $channelId,
                    'channel_name' => $channelName,
                    'video_id' => $videoId
                ]
            ]
        ];
        
        $result->MoveNext();
    }
    
    // Enqueue all notification jobs as a batch
    if (!empty($jobs)) {
        $jobIds = enqueue_batch($jobs, 'notifications');
        
        VLogger::getInstance()->info('Subscriber notifications enqueued', [
            'channel_id' => $channelId,
            'video_id' => $videoId,
            'subscriber_count' => count($jobs),
            'job_count' => count($jobIds)
        ]);
        
        return count($jobIds);
    }
    
    return 0;
}

// Example 5: Scheduled Content Publishing
function scheduleContentPublishing($contentId, $publishDate) {
    $delay = strtotime($publishDate) - time();
    
    if ($delay > 0) {
        // Schedule the job to run at the specified time
        $jobId = enqueue_job('PublishContentJob', [
            'content_id' => $contentId,
            'publish_date' => $publishDate
        ], 'default', $delay);
        
        VLogger::getInstance()->info('Content publishing scheduled', [
            'content_id' => $contentId,
            'publish_date' => $publishDate,
            'delay_seconds' => $delay,
            'job_id' => $jobId
        ]);
        
        return $jobId;
    }
    
    return false;
}

// Example 6: Daily Statistics Report
function scheduleDailyReports() {
    // Calculate delay until next midnight
    $tomorrow = strtotime('tomorrow midnight');
    $delay = $tomorrow - time();
    
    // Schedule daily report generation
    $jobId = enqueue_job('GenerateDailyReportJob', [
        'report_date' => date('Y-m-d'),
        'recipients' => ['admin@easystream.com', 'analytics@easystream.com']
    ], 'default', $delay);
    
    return $jobId;
}

// Example 7: Video Processing Completion Handler
function handleVideoProcessingComplete($videoId, $success, $results) {
    global $class_database;
    
    // Get video and user info
    $video = $class_database->singleFieldValue('db_videofiles', '*', 'file_key', $videoId);
    
    if ($video) {
        // Update video status
        $updateData = [
            'processing_status' => $success ? 'completed' : 'failed',
            'processed_at' => date('Y-m-d H:i:s')
        ];
        
        $class_database->doUpdate('db_videofiles', 'file_key', $updateData, $videoId);
        
        // Notify user of processing completion
        enqueue_video_processing_notification(
            $video['usr_id'],
            $video['file_title'],
            '/watch/' . $videoId,
            $success
        );
        
        // If successful, notify subscribers
        if ($success) {
            notifySubscribersOfNewVideo(
                $video['usr_id'],
                $videoId,
                $video['file_title']
            );
        }
    }
}

// Example 8: Cleanup Old Jobs (can be run via cron)
function cleanupOldJobs() {
    $redis = VRedis::getInstance();
    
    if (!$redis->isConnected()) {
        return false;
    }
    
    try {
        $redisInstance = $redis->getRedis();
        $jobKeys = $redisInstance->keys('job:*');
        $cleaned = 0;
        $cutoffTime = time() - (7 * 24 * 3600); // 7 days ago
        
        foreach ($jobKeys as $jobKey) {
            $job = $redis->get(str_replace('easystream:', '', $jobKey));
            
            if ($job && isset($job['created_at']) && $job['created_at'] < $cutoffTime) {
                // Only clean completed or failed jobs
                if (in_array($job['status'], ['completed', 'failed'])) {
                    $redis->delete(str_replace('easystream:', '', $jobKey));
                    $cleaned++;
                }
            }
        }
        
        VLogger::getInstance()->info('Old jobs cleaned up', [
            'cleaned_count' => $cleaned,
            'cutoff_time' => date('Y-m-d H:i:s', $cutoffTime)
        ]);
        
        return $cleaned;
        
    } catch (Exception $e) {
        VLogger::getInstance()->error('Job cleanup failed', [
            'error' => $e->getMessage()
        ]);
        return false;
    }
}

// Example usage in your existing code:

// In your user registration handler:
if ($_POST['action'] === 'register') {
    $userData = [
        'usr_user' => VSecurity::postParam('username', 'alphanum'),
        'usr_email' => VSecurity::postParam('email', 'email'),
        'usr_password' => password_hash(VSecurity::postParam('password', 'string'), PASSWORD_DEFAULT),
        'usr_datereg' => date('Y-m-d H:i:s')
    ];
    
    $userId = handleUserRegistration($userData);
    
    if ($userId) {
        echo json_encode(['success' => true, 'user_id' => $userId]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Registration failed']);
    }
}

// In your video upload handler:
if ($_POST['action'] === 'upload_video' && isset($_FILES['video'])) {
    $videoData = [
        'usr_id' => $_SESSION['USER_ID'],
        'file_title' => VSecurity::postParam('title', 'string'),
        'file_description' => VSecurity::postParam('description', 'html'),
        'upload_date' => date('Y-m-d H:i:s'),
        'processing_status' => 'pending'
    ];
    
    $videoId = handleVideoUpload($videoData, $_FILES['video']);
    
    if ($videoId) {
        echo json_encode(['success' => true, 'video_id' => $videoId]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Upload failed']);
    }
}

// In your password reset handler:
if ($_POST['action'] === 'reset_password') {
    $email = VSecurity::postParam('email', 'email');
    
    if (handlePasswordResetRequest($email)) {
        echo json_encode(['success' => true, 'message' => 'Reset email sent']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Email not found']);
    }
}

echo "Queue integration examples completed. Check the queue management interface for job status.";
?>