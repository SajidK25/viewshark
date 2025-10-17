<?php
/*******************************************************************************************************************
| Base Job Class
| All queue jobs should extend this class
|*******************************************************************************************************************/

abstract class BaseJob
{
    protected $logger;
    protected $startTime;
    
    public function __construct()
    {
        $this->logger = VLogger::getInstance();
        $this->startTime = microtime(true);
    }
    
    /**
     * Handle the job - must be implemented by child classes
     * @param array $data Job data
     * @return mixed Job result
     */
    abstract public function handle($data);
    
    /**
     * Log job progress
     * @param string $message Progress message
     * @param array $context Additional context
     */
    protected function logProgress($message, $context = [])
    {
        $context['job_class'] = get_class($this);
        $context['elapsed_time'] = microtime(true) - $this->startTime;
        
        $this->logger->info($message, $context);
    }
    
    /**
     * Log job error
     * @param string $message Error message
     * @param array $context Additional context
     */
    protected function logError($message, $context = [])
    {
        $context['job_class'] = get_class($this);
        $context['elapsed_time'] = microtime(true) - $this->startTime;
        
        $this->logger->error($message, $context);
    }
    
    /**
     * Validate required data fields
     * @param array $data Job data
     * @param array $required Required field names
     * @throws Exception If required fields are missing
     */
    protected function validateData($data, $required = [])
    {
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new Exception("Required field '{$field}' is missing from job data");
            }
        }
    }
    
    /**
     * Get database connection
     * @return object Database connection
     */
    protected function getDatabase()
    {
        global $class_database;
        return $class_database;
    }
    
    /**
     * Get Redis connection
     * @return VRedis Redis instance
     */
    protected function getRedis()
    {
        return VRedis::getInstance();
    }
    
    /**
     * Send notification (can be overridden)
     * @param string $message Notification message
     * @param array $data Notification data
     */
    protected function sendNotification($message, $data = [])
    {
        // Default implementation - log the notification
        $this->logger->info('Job notification', [
            'message' => $message,
            'data' => $data,
            'job_class' => get_class($this)
        ]);
    }
    
    /**
     * Update job progress (for long-running jobs)
     * @param int $current Current progress
     * @param int $total Total items
     * @param string $message Progress message
     */
    protected function updateProgress($current, $total, $message = '')
    {
        $percentage = $total > 0 ? round(($current / $total) * 100, 2) : 0;
        
        $this->logProgress("Job progress: {$percentage}%", [
            'current' => $current,
            'total' => $total,
            'percentage' => $percentage,
            'message' => $message
        ]);
    }
}