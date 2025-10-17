<?php
/*******************************************************************************************************************
| Send Email Job
| Handles email sending in the background
|*******************************************************************************************************************/

class SendEmailJob extends BaseJob
{
    /**
     * Handle email sending
     * @param array $data Email data
     * @return bool Success status
     */
    public function handle($data)
    {
        $this->validateData($data, ['to', 'subject', 'message']);
        
        $to = $data['to'];
        $subject = $data['subject'];
        $message = $data['message'];
        $from = $data['from'] ?? 'noreply@easystream.com';
        $headers = $data['headers'] ?? [];
        
        $this->logProgress('Starting email send', [
            'to' => $to,
            'subject' => $subject,
            'from' => $from
        ]);
        
        try {
            // Prepare headers
            $emailHeaders = [
                'From: ' . $from,
                'Reply-To: ' . $from,
                'Content-Type: text/html; charset=UTF-8',
                'X-Mailer: EasyStream'
            ];
            
            // Add custom headers
            foreach ($headers as $header) {
                $emailHeaders[] = $header;
            }
            
            $headerString = implode("\r\n", $emailHeaders);
            
            // Send email
            $success = mail($to, $subject, $message, $headerString);
            
            if ($success) {
                $this->logProgress('Email sent successfully', [
                    'to' => $to,
                    'subject' => $subject
                ]);
                
                // Log to database for tracking
                $this->logEmailToDatabase($to, $subject, 'sent');
                
                return true;
            } else {
                throw new Exception('mail() function returned false');
            }
            
        } catch (Exception $e) {
            $this->logError('Failed to send email', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            
            // Log failed email to database
            $this->logEmailToDatabase($to, $subject, 'failed', $e->getMessage());
            
            throw $e;
        }
    }
    
    /**
     * Log email to database for tracking
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $status Status (sent/failed)
     * @param string $error Error message if failed
     */
    private function logEmailToDatabase($to, $subject, $status, $error = null)
    {
        try {
            $db = $this->getDatabase();
            
            $emailLog = [
                'recipient' => $to,
                'subject' => $subject,
                'status' => $status,
                'error_message' => $error,
                'sent_at' => date('Y-m-d H:i:s'),
                'job_class' => get_class($this)
            ];
            
            // Create table if it doesn't exist
            $this->createEmailLogTable();
            
            $db->doInsert('db_email_log', $emailLog);
            
        } catch (Exception $e) {
            $this->logError('Failed to log email to database', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Create email log table if it doesn't exist
     */
    private function createEmailLogTable()
    {
        global $db;
        
        $sql = "CREATE TABLE IF NOT EXISTS `db_email_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `recipient` varchar(255) NOT NULL,
            `subject` varchar(500) NOT NULL,
            `status` enum('sent','failed') NOT NULL,
            `error_message` text,
            `sent_at` datetime NOT NULL,
            `job_class` varchar(100) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_recipient` (`recipient`),
            KEY `idx_status` (`status`),
            KEY `idx_sent_at` (`sent_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $db->Execute($sql);
        } catch (Exception $e) {
            $this->logError('Failed to create email log table', [
                'error' => $e->getMessage()
            ]);
        }
    }
}