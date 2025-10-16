<?php
/*******************************************************************************************************************
| Log Analysis Script
| Analyzes log files and generates reports
|*******************************************************************************************************************/

define('_ISVALID', true);
include_once '../f_core/config.core.php';

class LogAnalyzer
{
    private $logDir = '../f_data/logs/';
    
    public function generateReport($days = 7)
    {
        $report = [
            'summary' => $this->getSummary($days),
            'errors' => $this->getTopErrors($days),
            'security_events' => $this->getSecurityEvents($days),
            'performance_issues' => $this->getPerformanceIssues($days),
            'user_activity' => $this->getUserActivity($days)
        ];
        
        return $report;
    }
    
    private function getSummary($days)
    {
        $summary = [
            'total_entries' => 0,
            'by_level' => [
                'error' => 0,
                'warning' => 0,
                'info' => 0,
                'debug' => 0
            ],
            'by_day' => []
        ];
        
        $cutoffDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $logFiles = glob($this->logDir . '*.log');
        foreach ($logFiles as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $entry = json_decode($line, true);
                if (!$entry || $entry['timestamp'] < $cutoffDate) continue;
                
                $summary['total_entries']++;
                
                $level = $entry['level'] ?? 'unknown';
                if (isset($summary['by_level'][$level])) {
                    $summary['by_level'][$level]++;
                }
                
                $day = date('Y-m-d', strtotime($entry['timestamp']));
                $summary['by_day'][$day] = ($summary['by_day'][$day] ?? 0) + 1;
            }
        }
        
        return $summary;
    }
    
    private function getTopErrors($days, $limit = 10)
    {
        $errors = [];
        $cutoffDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $errorFiles = glob($this->logDir . '*_error.log');
        foreach ($errorFiles as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $entry = json_decode($line, true);
                if (!$entry || $entry['timestamp'] < $cutoffDate) continue;
                
                $message = $entry['message'] ?? 'Unknown error';
                $errors[$message] = ($errors[$message] ?? 0) + 1;
            }
        }
        
        arsort($errors);
        return array_slice($errors, 0, $limit, true);
    }
    
    private function getSecurityEvents($days)
    {
        $events = [];
        $cutoffDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $logFiles = glob($this->logDir . '*.log');
        foreach ($logFiles as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $entry = json_decode($line, true);
                if (!$entry || $entry['timestamp'] < $cutoffDate) continue;
                
                if (isset($entry['context']['security_event']) && $entry['context']['security_event']) {
                    $events[] = [
                        'timestamp' => $entry['timestamp'],
                        'message' => $entry['message'],
                        'ip' => $entry['ip'] ?? 'unknown',
                        'user_id' => $entry['user_id'] ?? null
                    ];
                }
            }
        }
        
        return array_slice($events, -50); // Last 50 security events
    }
    
    private function getPerformanceIssues($days)
    {
        $issues = [];
        $cutoffDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $logFiles = glob($this->logDir . '*.log');
        foreach ($logFiles as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $entry = json_decode($line, true);
                if (!$entry || $entry['timestamp'] < $cutoffDate) continue;
                
                if (isset($entry['context']['performance_issue']) && $entry['context']['performance_issue']) {
                    $issues[] = [
                        'timestamp' => $entry['timestamp'],
                        'message' => $entry['message'],
                        'execution_time' => $entry['context']['execution_time'] ?? 0
                    ];
                }
            }
        }
        
        // Sort by execution time (slowest first)
        usort($issues, function($a, $b) {
            return $b['execution_time'] <=> $a['execution_time'];
        });
        
        return array_slice($issues, 0, 20); // Top 20 performance issues
    }
    
    private function getUserActivity($days)
    {
        $activity = [];
        $cutoffDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $logFiles = glob($this->logDir . '*.log');
        foreach ($logFiles as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $entry = json_decode($line, true);
                if (!$entry || $entry['timestamp'] < $cutoffDate) continue;
                
                $userId = $entry['user_id'] ?? null;
                if ($userId) {
                    $activity[$userId] = ($activity[$userId] ?? 0) + 1;
                }
            }
        }
        
        arsort($activity);
        return array_slice($activity, 0, 10, true); // Top 10 most active users
    }
    
    public function exportReport($format = 'json')
    {
        $report = $this->generateReport();
        
        switch ($format) {
            case 'json':
                return json_encode($report, JSON_PRETTY_PRINT);
                
            case 'html':
                return $this->generateHTMLReport($report);
                
            case 'csv':
                return $this->generateCSVReport($report);
                
            default:
                return $report;
        }
    }
    
    private function generateHTMLReport($report)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Log Analysis Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin-bottom: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .error { color: #f44336; }
        .warning { color: #ff9800; }
        .info { color: #2196f3; }
    </style>
</head>
<body>
    <h1>Log Analysis Report</h1>
    <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    
    <div class="section">
        <h2>Summary</h2>
        <p>Total log entries: ' . $report['summary']['total_entries'] . '</p>
        <table>
            <tr><th>Level</th><th>Count</th></tr>';
        
        foreach ($report['summary']['by_level'] as $level => $count) {
            $html .= "<tr><td class=\"{$level}\">{$level}</td><td>{$count}</td></tr>";
        }
        
        $html .= '</table>
    </div>
    
    <div class="section">
        <h2>Top Errors</h2>
        <table>
            <tr><th>Error Message</th><th>Count</th></tr>';
        
        foreach ($report['errors'] as $error => $count) {
            $html .= '<tr><td>' . htmlspecialchars($error) . '</td><td>' . $count . '</td></tr>';
        }
        
        $html .= '</table>
    </div>
    
    <div class="section">
        <h2>Recent Security Events</h2>
        <table>
            <tr><th>Timestamp</th><th>Event</th><th>IP</th><th>User ID</th></tr>';
        
        foreach (array_slice($report['security_events'], -10) as $event) {
            $html .= '<tr>
                <td>' . $event['timestamp'] . '</td>
                <td>' . htmlspecialchars($event['message']) . '</td>
                <td>' . $event['ip'] . '</td>
                <td>' . ($event['user_id'] ?: 'Guest') . '</td>
            </tr>';
        }
        
        $html .= '</table>
    </div>
    
</body>
</html>';
        
        return $html;
    }
}

// CLI usage
if (php_sapi_name() === 'cli') {
    $analyzer = new LogAnalyzer();
    
    $format = $argv[1] ?? 'json';
    $days = (int)($argv[2] ?? 7);
    
    echo $analyzer->exportReport($format);
} else {
    // Web usage
    $analyzer = new LogAnalyzer();
    
    $format = $_GET['format'] ?? 'html';
    $days = (int)($_GET['days'] ?? 7);
    
    if ($format === 'html') {
        echo $analyzer->exportReport($format);
    } else {
        header('Content-Type: application/json');
        echo $analyzer->exportReport('json');
    }
}
?>