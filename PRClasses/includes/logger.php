<?php
/**
 * Logger class for application logging
 */

class Logger {
    private $log_file;
    private $log_level;
    
    // Log levels
    const DEBUG = 100;
    const INFO = 200;
    const NOTICE = 250;
    const WARNING = 300;
    const ERROR = 400;
    const CRITICAL = 500;
    const ALERT = 550;
    const EMERGENCY = 600;
    
    // Log level names
    private $level_names = [
        self::DEBUG => 'DEBUG',
        self::INFO => 'INFO',
        self::NOTICE => 'NOTICE',
        self::WARNING => 'WARNING',
        self::ERROR => 'ERROR',
        self::CRITICAL => 'CRITICAL',
        self::ALERT => 'ALERT',
        self::EMERGENCY => 'EMERGENCY'
    ];
    
    /**
     * Constructor
     * 
     * @param string $log_file Path to log file
     * @param int $log_level Minimum log level to record
     */
    public function __construct($log_file = null, $log_level = self::DEBUG) {
        $this->log_file = $log_file ?? __DIR__ . '/../logs/app.log';
        $this->log_level = $log_level;
        
        // Create logs directory if it doesn't exist
        $log_dir = dirname($this->log_file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
    }
    
    /**
     * Log a message
     * 
     * @param string $message Log message
     * @param int $level Log level
     * @param array $context Additional context data
     * @return bool True on success, false on failure
     */
    public function log($message, $level = self::INFO, $context = []) {
        // Check if we should log this level
        if ($level < $this->log_level) {
            return true;
        }
        
        // Format log entry
        $level_name = $this->level_names[$level] ?? 'UNKNOWN';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'guest';
        
        $log_entry = "[$timestamp] [$level_name] [$ip] [User:$user] $message";
        
        // Add context if provided
        if (!empty($context)) {
            $log_entry .= ' ' . json_encode($context);
        }
        
        $log_entry .= PHP_EOL;
        
        // Write to log file
        return file_put_contents($this->log_file, $log_entry, FILE_APPEND) !== false;
    }
    
    // Convenience methods for different log levels
    public function debug($message, $context = []) {
        return $this->log($message, self::DEBUG, $context);
    }
    
    public function info($message, $context = []) {
        return $this->log($message, self::INFO, $context);
    }
    
    public function notice($message, $context = []) {
        return $this->log($message, self::NOTICE, $context);
    }
    
    public function warning($message, $context = []) {
        return $this->log($message, self::WARNING, $context);
    }
    
    public function error($message, $context = []) {
        return $this->log($message, self::ERROR, $context);
    }
    
    public function critical($message, $context = []) {
        return $this->log($message, self::CRITICAL, $context);
    }
    
    public function alert($message, $context = []) {
        return $this->log($message, self::ALERT, $context);
    }
    
    public function emergency($message, $context = []) {
        return $this->log($message, self::EMERGENCY, $context);
    }
} 