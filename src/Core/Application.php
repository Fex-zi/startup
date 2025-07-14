<?php

namespace Core;

class Application
{
    private static $instance = null;
    private $config;
    private $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->loadConfig();
        $this->initializeApplication();
        self::$instance = $this;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig()
    {
        $this->config = require_once __DIR__ . '/../../config/config.php';
        
        // Set timezone
        date_default_timezone_set($this->config['app']['timezone']);
        
        // Set error reporting based on debug mode
        if ($this->config['app']['debug']) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
    }

    private function initializeApplication()
    {
        // Start session with secure settings
        $this->startSecureSession();
        
        // Initialize autoloader
        spl_autoload_register([$this, 'autoload']);
        
        // Load helper functions
        require_once __DIR__ . '/../Utils/helpers.php';
    }

    private function startSecureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure session configuration
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            session_name($this->config['security']['session_name']);
            session_start();
            
            // Regenerate session ID periodically for security
            if (!isset($_SESSION['last_regeneration'])) {
                $_SESSION['last_regeneration'] = time();
            } elseif (time() - $_SESSION['last_regeneration'] > 300) {
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        }
    }

    public function autoload($className)
    {
        // Convert namespace to file path
        $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $file = __DIR__ . '/../' . $className . '.php';
        
        if (file_exists($file)) {
            require_once $file;
        }
    }

    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return null;
            }
        }
        
        return $value;
    }

    public function getExecutionTime()
    {
        return microtime(true) - $this->startTime;
    }

    public static function url($path = '')
    {
        // Get the base path from the current request
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname($scriptName);
        
        // Handle case where we're in a subdirectory
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        
        // Remove leading slash from path if present
        $path = ltrim($path, '/');
        
        return $basePath . '/' . $path;
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $error = [
            'type' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'time' => date('Y-m-d H:i:s')
        ];

        // Log error
        error_log(json_encode($error), 3, __DIR__ . '/../../storage/logs/error.log');

        if ($this->config['app']['debug']) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
            echo "<strong>Error:</strong> {$errstr} in <strong>{$errfile}</strong> on line <strong>{$errline}</strong>";
            echo "</div>";
        }

        return true;
    }
}
