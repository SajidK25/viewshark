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

define('_ISVALID', true);
include_once '../../f_core/config.core.php';

// Check admin access
if (!VSession::isLoggedIn() || !VLogin::checkBackendAccess()) {
    header('Location: /error');
    exit;
}

$logger = VLogger::getInstance();

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_logs':
            $level = VSecurity::getParam('level', 'alpha');
            $limit = VSecurity::getParam('limit', 'int', 100, ['min' => 1, 'max' => 1000]);
            
            $logs = $logger->getRecentLogs($level, $limit);
            echo json_encode($logs);
            break;
            
        case 'clear_logs':
            if (VSecurity::validateCSRFFromPost('clear_logs')) {
                $logFiles = glob('f_data/logs/*.log*');
                foreach ($logFiles as $file) {
                    unlink($file);
                }
                echo json_encode(['success' => true, 'message' => 'Logs cleared successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            }
            break;
    }
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Log Viewer - EasyStream Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .controls { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px; }
        .controls select, .controls button { margin-right: 10px; padding: 8px 12px; }
        .log-entry { margin-bottom: 15px; padding: 15px; border-left: 4px solid #ddd; background: #fafafa; }
        .log-entry.error { border-left-color: #f44336; }
        .log-entry.warning { border-left-color: #ff9800; }
        .log-entry.info { border-left-color: #2196f3; }
        .log-entry.debug { border-left-color: #9e9e9e; }
        .log-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .log-level { padding: 2px 8px; border-radius: 3px; color: white; font-size: 12px; font-weight: bold; }
        .log-level.error { background: #f44336; }
        .log-level.warning { background: #ff9800; }
        .log-level.info { background: #2196f3; }
        .log-level.debug { background: #9e9e9e; }
        .log-message { font-weight: bold; margin-bottom: 10px; }
        .log-details { font-size: 12px; color: #666; }
        .log-context { background: #f0f0f0; padding: 10px; margin-top: 10px; border-radius: 4px; font-family: monospace; font-size: 11px; }
        .loading { text-align: center; padding: 20px; }
        .btn { background: #1976d2; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #1565c0; }
        .btn.danger { background: #f44336; }
        .btn.danger:hover { background: #d32f2f; }
    </style>
</head>
<body>
    <div class="container">
        <h1>System Logs</h1>
        
        <div class="controls">
            <select id="logLevel">
                <option value="">All Levels</option>
                <option value="error">Errors</option>
                <option value="warning">Warnings</option>
                <option value="info">Info</option>
                <option value="debug">Debug</option>
            </select>
            
            <select id="logLimit">
                <option value="50">50 entries</option>
                <option value="100" selected>100 entries</option>
                <option value="200">200 entries</option>
                <option value="500">500 entries</option>
            </select>
            
            <button class="btn" onclick="loadLogs()">Refresh</button>
            <button class="btn danger" onclick="clearLogs()">Clear All Logs</button>
        </div>
        
        <div id="logContainer">
            <div class="loading">Loading logs...</div>
        </div>
    </div>

    <script>
        function loadLogs() {
            const level = document.getElementById('logLevel').value;
            const limit = document.getElementById('logLimit').value;
            
            document.getElementById('logContainer').innerHTML = '<div class="loading">Loading logs...</div>';
            
            fetch(`?action=get_logs&level=${level}&limit=${limit}`)
                .then(response => response.json())
                .then(logs => {
                    displayLogs(logs);
                })
                .catch(error => {
                    document.getElementById('logContainer').innerHTML = '<div class="error">Error loading logs: ' + error.message + '</div>';
                });
        }
        
        function displayLogs(logs) {
            const container = document.getElementById('logContainer');
            
            if (logs.length === 0) {
                container.innerHTML = '<div class="loading">No logs found</div>';
                return;
            }
            
            let html = '';
            logs.forEach(log => {
                html += `
                    <div class="log-entry ${log.level}">
                        <div class="log-header">
                            <span class="log-level ${log.level}">${log.level.toUpperCase()}</span>
                            <span class="log-details">
                                ${log.timestamp} | IP: ${log.ip} | Request: ${log.request_id}
                                ${log.user_id ? ' | User: ' + log.user_id : ''}
                            </span>
                        </div>
                        <div class="log-message">${escapeHtml(log.message)}</div>
                        <div class="log-details">
                            URI: ${log.request_uri || 'N/A'} | Method: ${log.request_method || 'N/A'}
                        </div>
                        ${log.context && Object.keys(log.context).length > 0 ? 
                            `<div class="log-context">${escapeHtml(JSON.stringify(log.context, null, 2))}</div>` : 
                            ''
                        }
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
        
        function clearLogs() {
            if (!confirm('Are you sure you want to clear all logs? This action cannot be undone.')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('csrf_token', '<?= VSecurity::generateCSRFToken('clear_logs') ?>');
            
            fetch('?action=clear_logs', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert(result.message);
                    loadLogs();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error clearing logs: ' + error.message);
            });
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Auto-refresh every 30 seconds
        setInterval(loadLogs, 30000);
        
        // Load logs on page load
        loadLogs();
    </script>
</body>
</html>