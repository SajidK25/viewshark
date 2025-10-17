<?php
/*******************************************************************************************************************
| Queue Management Backend Interface
|*******************************************************************************************************************/

define('_ISVALID', true);
include_once '../../f_core/config.core.php';

// Check admin access
if (!isset($_SESSION['ADMIN_NAME'])) {
    header('Location: /error');
    exit;
}

$action = VSecurity::getParam('action', 'alpha');
$queue = VSecurity::getParam('queue', 'alphanum');

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && VSecurity::validateCSRFFromPost('queue_management')) {
    switch ($action) {
        case 'clear_queue':
            if ($queue && clear_queue($queue)) {
                $success_message = "Queue '{$queue}' has been cleared successfully.";
            } else {
                $error_message = "Failed to clear queue '{$queue}'.";
            }
            break;
            
        case 'test_job':
            $jobId = enqueue_job('SendEmailJob', [
                'to' => 'admin@easystream.com',
                'subject' => 'Test Job from Queue Management',
                'message' => 'This is a test email sent from the queue management interface at ' . date('Y-m-d H:i:s')
            ], 'email');
            
            if ($jobId) {
                $success_message = "Test job enqueued successfully. Job ID: {$jobId}";
            } else {
                $error_message = "Failed to enqueue test job.";
            }
            break;
    }
}

// Get queue statistics
$queues = ['default', 'email', 'video', 'notifications'];
$queueStats = [];
foreach ($queues as $queueName) {
    $queueStats[$queueName] = get_queue_stats($queueName);
}

$globalStats = get_queue_stats();

// Get Redis info
$redis = VRedis::getInstance();
$redisInfo = $redis->isConnected() ? $redis->info() : [];
$redisConnected = $redis->isConnected();

// Get recent jobs (if Redis is connected)
$recentJobs = [];
if ($redisConnected) {
    try {
        $redisInstance = $redis->getRedis();
        $jobKeys = $redisInstance->keys('job:*');
        
        // Get last 20 jobs
        $jobKeys = array_slice($jobKeys, -20);
        
        foreach ($jobKeys as $jobKey) {
            $jobData = $redis->get(str_replace('easystream:', '', $jobKey));
            if ($jobData) {
                $recentJobs[] = $jobData;
            }
        }
        
        // Sort by created_at descending
        usort($recentJobs, function($a, $b) {
            return ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0);
        });
        
    } catch (Exception $e) {
        // Handle error silently
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Queue Management - EasyStream Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: #f8f9fa; padding: 15px; border-radius: 5px; }
        .stat-card h4 { margin: 0 0 10px 0; color: #333; }
        .stat-number { font-size: 24px; font-weight: bold; color: #007cba; }
        .stat-label { font-size: 12px; color: #666; }
        .btn { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 3px; cursor: pointer; margin-right: 10px; }
        .btn:hover { background: #005a87; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 12px; }
        th { background: #f8f9fa; }
        .status-pending { color: #ffc107; }
        .status-processing { color: #17a2b8; }
        .status-completed { color: #28a745; }
        .status-failed { color: #dc3545; }
        .status-retrying { color: #fd7e14; }
        .redis-status { padding: 5px 10px; border-radius: 3px; color: white; font-size: 12px; }
        .redis-connected { background: #28a745; }
        .redis-disconnected { background: #dc3545; }
        .job-id { font-family: monospace; font-size: 11px; }
        .progress-bar { width: 100%; height: 20px; background: #f0f0f0; border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; background: #007cba; transition: width 0.3s ease; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Queue Management System</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= secure_output($success_message) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= secure_output($error_message) ?></div>
        <?php endif; ?>
        
        <!-- Redis Status -->
        <div class="section">
            <h2>Redis Connection Status</h2>
            <span class="redis-status <?= $redisConnected ? 'redis-connected' : 'redis-disconnected' ?>">
                <?= $redisConnected ? 'Connected' : 'Disconnected' ?>
            </span>
            
            <?php if ($redisConnected && !empty($redisInfo)): ?>
                <div style="margin-top: 15px;">
                    <strong>Redis Info:</strong>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li>Version: <?= $redisInfo['redis_version'] ?? 'Unknown' ?></li>
                        <li>Used Memory: <?= isset($redisInfo['used_memory_human']) ? $redisInfo['used_memory_human'] : 'Unknown' ?></li>
                        <li>Connected Clients: <?= $redisInfo['connected_clients'] ?? 'Unknown' ?></li>
                        <li>Total Commands: <?= $redisInfo['total_commands_processed'] ?? 'Unknown' ?></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Global Statistics -->
        <div class="section">
            <h2>Global Queue Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>Total Enqueued</h4>
                    <div class="stat-number"><?= number_format($globalStats['total_enqueued'] ?? 0) ?></div>
                    <div class="stat-label">All time</div>
                </div>
                <div class="stat-card">
                    <h4>Total Processed</h4>
                    <div class="stat-number"><?= number_format($globalStats['total_processed'] ?? 0) ?></div>
                    <div class="stat-label">All time</div>
                </div>
                <div class="stat-card">
                    <h4>Total Completed</h4>
                    <div class="stat-number"><?= number_format($globalStats['total_completed'] ?? 0) ?></div>
                    <div class="stat-label">Successfully completed</div>
                </div>
                <div class="stat-card">
                    <h4>Total Failed</h4>
                    <div class="stat-number"><?= number_format($globalStats['total_failed'] ?? 0) ?></div>
                    <div class="stat-label">Permanently failed</div>
                </div>
                <div class="stat-card">
                    <h4>Delayed Jobs</h4>
                    <div class="stat-number"><?= number_format($globalStats['delayed_jobs'] ?? 0) ?></div>
                    <div class="stat-label">Waiting to run</div>
                </div>
            </div>
        </div>
        
        <!-- Queue Statistics -->
        <div class="section">
            <h2>Individual Queue Statistics</h2>
            <div class="stats-grid">
                <?php foreach ($queueStats as $queueName => $stats): ?>
                    <div class="stat-card">
                        <h4><?= ucfirst($queueName) ?> Queue</h4>
                        <div style="margin-bottom: 10px;">
                            <strong>Pending:</strong> <?= number_format($stats['pending']) ?>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Total:</strong> <?= number_format($stats['total']) ?>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Processed:</strong> <?= number_format($stats['processed']) ?>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Completed:</strong> <?= number_format($stats['completed']) ?>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Failed:</strong> <?= number_format($stats['failed']) ?>
                        </div>
                        
                        <?php if ($stats['total'] > 0): ?>
                            <?php $successRate = round(($stats['completed'] / $stats['total']) * 100, 1); ?>
                            <div style="margin-bottom: 10px;">
                                <strong>Success Rate:</strong> <?= $successRate ?>%
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $successRate ?>%;"></div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" style="margin-top: 15px;">
                            <?= csrf_field('queue_management') ?>
                            <input type="hidden" name="action" value="clear_queue">
                            <input type="hidden" name="queue" value="<?= $queueName ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear the <?= $queueName ?> queue?')">
                                Clear Queue
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Queue Actions -->
        <div class="section">
            <h2>Queue Actions</h2>
            
            <form method="POST" style="display: inline;">
                <?= csrf_field('queue_management') ?>
                <input type="hidden" name="action" value="test_job">
                <button type="submit" class="btn btn-success">Enqueue Test Job</button>
            </form>
            
            <button onclick="location.reload()" class="btn">Refresh Statistics</button>
        </div>
        
        <!-- Recent Jobs -->
        <?php if (!empty($recentJobs)): ?>
        <div class="section">
            <h2>Recent Jobs (Last 20)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Class</th>
                        <th>Queue</th>
                        <th>Status</th>
                        <th>Attempts</th>
                        <th>Created</th>
                        <th>Started</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentJobs as $job): ?>
                        <tr>
                            <td class="job-id"><?= substr($job['id'], 0, 20) ?>...</td>
                            <td><?= secure_output($job['class']) ?></td>
                            <td><?= secure_output($job['queue']) ?></td>
                            <td class="status-<?= $job['status'] ?>"><?= ucfirst($job['status']) ?></td>
                            <td><?= $job['attempts'] ?>/<?= $job['max_attempts'] ?></td>
                            <td><?= date('Y-m-d H:i:s', $job['created_at']) ?></td>
                            <td><?= isset($job['started_at']) ? date('Y-m-d H:i:s', $job['started_at']) : '-' ?></td>
                            <td><?= isset($job['completed_at']) ? date('Y-m-d H:i:s', $job['completed_at']) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Worker Status -->
        <div class="section">
            <h2>Queue Worker Information</h2>
            <div class="alert alert-warning">
                <strong>Queue Worker Status:</strong> Check Docker container logs to see if the queue worker is running.<br>
                <strong>Command:</strong> <code>docker logs vs-queue-worker</code><br>
                <strong>Restart Worker:</strong> <code>docker restart vs-queue-worker</code>
            </div>
            
            <h4>Available Job Classes:</h4>
            <ul>
                <li><strong>SendEmailJob</strong> - Handles email sending</li>
                <li><strong>VideoProcessingJob</strong> - Processes video transcoding</li>
                <li><strong>SendNotificationJob</strong> - Sends user notifications</li>
            </ul>
            
            <h4>Queue Names:</h4>
            <ul>
                <li><strong>default</strong> - General purpose jobs</li>
                <li><strong>email</strong> - Email sending jobs</li>
                <li><strong>video</strong> - Video processing jobs</li>
                <li><strong>notifications</strong> - Notification jobs</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>