<?php
namespace Donations\Core;

class Logger {
    private $logFile;
    private $logLevels = ['debug', 'info', 'warning', 'error', 'critical'];

    public function __construct() {
        $this->logFile = DONATIONS_PATH . '/logs/donations.log';
        $this->ensureLogDirectory();
    }

    public function log($message, $level = 'info') {
        if (!in_array($level, $this->logLevels)) {
            $level = 'info';
        }

        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;

        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    private function ensureLogDirectory() {
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    public function debug($message) {
        $this->log($message, 'debug');
    }

    public function info($message) {
        $this->log($message, 'info');
    }

    public function warning($message) {
        $this->log($message, 'warning');
    }

    public function error($message) {
        $this->log($message, 'error');
    }

    public function critical($message) {
        $this->log($message, 'critical');
    }

    public function getLogContents($lines = 100) {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $logs = file($this->logFile);
        return array_slice($logs, -$lines);
    }

    public function clearLog() {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }
} 