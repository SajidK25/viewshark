<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

/**
 * Queue Management System
 */
class VQueue
{
    private $redis;
    private $defaultQueue = 'default';
    
    public function __construct()
    {
        $this->redis = VRedis::getInstance();
    }
    
    /**
     * Add a job to the queue
     * @param string $jobClass Job class name
     * @param array $data Job data
     * @param string $queue Queue name
     * @param int $delay Delay in seconds (0 = immediate)
     * @param int $priority Priority (higher = more important)
     * @return string|false Job ID or false on failure
     */
    public function enqueue($jobClass, $data = [], $queue = null, $delay = 0, $priority = 0)
    {
        if (!$this->redis->isConnected()) {
            return false;
        }
        
        $queue = $queue ?: $this->defaultQueue;
        $jobId = $this->generateJobId();
        
        $job = [
            'id' => $jobId,
            'class' => $jobClass,
            'data' => $data,
            'queue' => $queue,
            'priority' => $priority,
            'attempts' => 0,
            'max_attempts' => 3,
            'created_at' => time(),
            'available_at' => time() + $delay,
            'timeout' => 300, // 5 minutes default timeout
            'status' => 'pending'
        ];
        
        try {
            // Store job data
            $this->redis->set("job:{$jobId}", $job, 86400); // 24 hour TTL
            
            if ($delay > 0) {
                // Delayed job - add to delayed queue
                $this->redis->getRedis()->zadd("queue:delayed", time() + $delay, $jobId);
            } else {
                // Immediate job - add to queue
                $queueKey = "queue:{$queue}";
                if ($priority > 0) {
                    // High priority - add to front
                    $this->redis->getRedis()->lpush($queueKey, $jobId);
                } else {
                    // Normal priority - add to back
                    $this->redis->getRedis()->rpush($queueKey, $jobId);
                }
            }
            
            // Update queue stats
            $this->redis->incr("stats:queue:{$queue}:total");
            $this->redis->incr("stats:jobs:enqueued");
            
            VLogger::getInstance()->info('Job enqueued', [
                'job_id' => $jobId,
                'class' => $jobClass,
                'queue' => $queue,
                'delay' => $delay,
                'priority' => $priority
            ]);
            
            return $jobId;
            
        } catch (Exception $e) {
            VLogger::getInstance()->error('Failed to enqueue job', [
                'class' => $jobClass,
                'queue' => $queue,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get next job from queue
     * @param array $queues Queue names to check
     * @param int $timeout Timeout in seconds
     * @return array|false Job data or false if no job
     */
    public function dequeue($queues = ['default'], $timeout = 10)
    {
        if (!$this->redis->isConnected()) {
            return false;
        }
        
        // Process delayed jobs first
        $this->processDelayedJobs();
        
        // Prepare queue keys
        $queueKeys = [];
        foreach ($queues as $queue) {
            $queueKeys[] = "queue:{$queue}";
        }
        
        try {
            // Blocking pop from queues
            $result = $this->redis->brpop($queueKeys, $timeout);
            
            if (!$result) {
                return false;
            }
            
            $queueKey = $result[0];
            $jobId = $result[1];
            $queue = str_replace('queue:', '', $queueKey);
            
            // Get job data
            $job = $this->redis->get("job:{$jobId}");
            
            if (!$job) {
                VLogger::getInstance()->warning('Job data not found', ['job_id' => $jobId]);
                return false;
            }
            
            // Mark job as processing
            $job['status'] = 'processing';
            $job['started_at'] = time();
            $job['attempts']++;
            
            $this->redis->set("job:{$jobId}", $job, 86400);
            
            // Update stats
            $this->redis->incr("stats:queue:{$queue}:processed");
            $this->redis->incr("stats:jobs:processed");
            
            return $job;
            
        } catch (Exception $e) {
            VLogger::getInstance()->error('Failed to dequeue job', [
                'queues' => $queues,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Mark job as completed
     * @param string $jobId Job ID
     * @param mixed $result Job result
     * @return bool Success status
     */
    public function markCompleted($jobId, $result = null)
    {
        try {
            $job = $this->redis->get("job:{$jobId}");
            
            if (!$job) {
                return false;
            }
            
            $job['status'] = 'completed';
            $job['completed_at'] = time();
            $job['result'] = $result;
            
            $this->redis->set("job:{$jobId}", $job, 3600); // Keep for 1 hour
            
            // Update stats
            $this->redis->incr("stats:queue:{$job['queue']}:completed");
            $this->redis->incr("stats:jobs:completed");
            
            VLogger::getInstance()->info('Job completed', [
                'job_id' => $jobId,
                'class' => $job['class'],
                'duration' => time() - $job['started_at']
            ]);
            
            return true;
            
        } catch (Exception $e) {
            VLogger::getInstance()->error('Failed to mark job completed', [
                'job_id' => $jobId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Mark job as failed
     * @param string $jobId Job ID
     * @param string $error Error message
     * @return bool Success status
     */
    public function markFailed($jobId, $error)
    {
        try {
            $job = $this->redis->get("job:{$jobId}");
            
            if (!$job) {
                return false;
            }
            
            $job['status'] = 'failed';
            $job['failed_at'] = time();
            $job['error'] = $error;
            
            // Retry logic
            if ($job['attempts'] < $job['max_attempts']) {
                // Retry with exponential backoff
                $delay = pow(2, $job['attempts']) * 60; // 1min, 2min, 4min...
                $job['status'] = 'retrying';
                $job['available_at'] = time() + $delay;
                
                $this->redis->set("job:{$jobId}", $job, 86400);
                $this->redis->getRedis()->zadd("queue:delayed", time() + $delay, $jobId);
                
                VLogger::getInstance()->warning('Job failed, retrying', [
                    'job_id' => $jobId,
                    'class' => $job['class'],
                    'attempt' => $job['attempts'],
                    'retry_in' => $delay,
                    'error' => $error
                ]);
            } else {
                // Max attempts reached
                $this->redis->set("job:{$jobId}", $job, 86400);
                
                // Update stats
                $this->redis->incr("stats:queue:{$job['queue']}:failed");
                $this->redis->incr("stats:jobs:failed");
                
                VLogger::getInstance()->error('Job failed permanently', [
                    'job_id' => $jobId,
                    'class' => $job['class'],
                    'attempts' => $job['attempts'],
                    'error' => $error
                ]);
            }
            
            return true;
            
        } catch (Exception $e) {
            VLogger::getInstance()->error('Failed to mark job failed', [
                'job_id' => $jobId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Process delayed jobs
     */
    private function processDelayedJobs()
    {
        try {
            $now = time();
            $redis = $this->redis->getRedis();
            
            // Get jobs that are ready to run
            $jobIds = $redis->zrangebyscore("queue:delayed", 0, $now);
            
            foreach ($jobIds as $jobId) {
                $job = $this->redis->get("job:{$jobId}");
                
                if ($job && $job['available_at'] <= $now) {
                    // Move to appropriate queue
                    $queueKey = "queue:{$job['queue']}";
                    $redis->lpush($queueKey, $jobId);
                    
                    // Remove from delayed queue
                    $redis->zrem("queue:delayed", $jobId);
                    
                    // Update job status
                    $job['status'] = 'pending';
                    $this->redis->set("job:{$jobId}", $job, 86400);
                }
            }
            
        } catch (Exception $e) {
            VLogger::getInstance()->error('Failed to process delayed jobs', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get queue statistics
     * @param string $queue Queue name
     * @return array Statistics
     */
    public function getQueueStats($queue = null)
    {
        if ($queue) {
            return [
                'name' => $queue,
                'pending' => $this->redis->llen("queue:{$queue}"),
                'total' => (int)$this->redis->get("stats:queue:{$queue}:total") ?: 0,
                'processed' => (int)$this->redis->get("stats:queue:{$queue}:processed") ?: 0,
                'completed' => (int)$this->redis->get("stats:queue:{$queue}:completed") ?: 0,
                'failed' => (int)$this->redis->get("stats:queue:{$queue}:failed") ?: 0
            ];
        }
        
        // Global stats
        return [
            'total_enqueued' => (int)$this->redis->get("stats:jobs:enqueued") ?: 0,
            'total_processed' => (int)$this->redis->get("stats:jobs:processed") ?: 0,
            'total_completed' => (int)$this->redis->get("stats:jobs:completed") ?: 0,
            'total_failed' => (int)$this->redis->get("stats:jobs:failed") ?: 0,
            'delayed_jobs' => $this->redis->getRedis()->zcard("queue:delayed")
        ];
    }
    
    /**
     * Generate unique job ID
     * @return string Job ID
     */
    private function generateJobId()
    {
        return uniqid('job_', true) . '_' . time();
    }
    
    /**
     * Clear queue
     * @param string $queue Queue name
     * @return bool Success status
     */
    public function clearQueue($queue)
    {
        try {
            $this->redis->delete("queue:{$queue}");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get job details
     * @param string $jobId Job ID
     * @return array|false Job data or false if not found
     */
    public function getJob($jobId)
    {
        return $this->redis->get("job:{$jobId}");
    }
}