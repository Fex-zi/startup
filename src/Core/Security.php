<?php

namespace Core;

class Security
{
    private static $instance = null;
    private $config;

    private function __construct()
    {
        $app = Application::getInstance();
        $this->config = $app->getConfig('security');
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCSRFToken($token)
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public function csrfField()
    {
        $token = $this->generateCSRFToken();
        $fieldName = $this->config['csrf_token_name'];
        return "<input type='hidden' name='{$fieldName}' value='{$token}'>";
    }

    public function validateRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tokenName = $this->config['csrf_token_name'];
            $token = $_POST[$tokenName] ?? '';
            
            if (!$this->validateCSRFToken($token)) {
                http_response_code(403);
                die('CSRF token validation failed');
            }
        }
    }

    public function sanitizeInput($input, $type = 'string')
    {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return $this->sanitizeInput($item, $type);
            }, $input);
        }

        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            
            case 'url':
                return filter_var(trim($input), FILTER_SANITIZE_URL);
            
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
            case 'html':
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
            
            case 'string':
            default:
                return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
        }
    }

    public function validateInput($input, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $input[$field] ?? null;
            $fieldErrors = [];

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;

                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $fieldErrors[] = ucfirst($field) . ' is required';
                        }
                        break;

                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $fieldErrors[] = ucfirst($field) . ' must be a valid email address';
                        }
                        break;

                    case 'min':
                        if (!empty($value) && strlen($value) < $ruleValue) {
                            $fieldErrors[] = ucfirst($field) . " must be at least {$ruleValue} characters";
                        }
                        break;

                    case 'max':
                        if (!empty($value) && strlen($value) > $ruleValue) {
                            $fieldErrors[] = ucfirst($field) . " must not exceed {$ruleValue} characters";
                        }
                        break;

                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $fieldErrors[] = ucfirst($field) . ' must be a number';
                        }
                        break;

                    case 'url':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                            $fieldErrors[] = ucfirst($field) . ' must be a valid URL';
                        }
                        break;

                    case 'unique':
                        // Format: unique:table,column
                        if (!empty($value) && $ruleValue) {
                            list($table, $column) = explode(',', $ruleValue);
                            if ($this->checkUnique($table, $column, $value)) {
                                $fieldErrors[] = ucfirst($field) . ' already exists';
                            }
                        }
                        break;

                    case 'confirmed':
                        $confirmField = $field . '_confirmation';
                        if (!empty($value) && $value !== ($input[$confirmField] ?? '')) {
                            $fieldErrors[] = ucfirst($field) . ' confirmation does not match';
                        }
                        break;
                }
            }

            if (!empty($fieldErrors)) {
                $errors[$field] = $fieldErrors;
            }
        }

        return $errors;
    }

    private function checkUnique($table, $column, $value)
    {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
        $result = $db->fetch($sql, [$value]);
        return $result['count'] > 0;
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function generateRandomString($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    public function isValidFileType($filename, $allowedTypes = null)
    {
        if ($allowedTypes === null) {
            $app = Application::getInstance();
            $allowedTypes = $app->getConfig('upload.allowed_types');
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $allowedTypes);
    }

    public function preventBruteForce($identifier, $maxAttempts = null, $lockoutTime = null)
    {
        if ($maxAttempts === null) {
            $maxAttempts = $this->config['max_login_attempts'];
        }
        
        if ($lockoutTime === null) {
            $lockoutTime = $this->config['lockout_duration'];
        }

        $key = 'login_attempts_' . $identifier;
        $attempts = $_SESSION[$key] ?? [];
        
        // Clean old attempts
        $cutoff = time() - $lockoutTime;
        $attempts = array_filter($attempts, function($timestamp) use ($cutoff) {
            return $timestamp > $cutoff;
        });

        if (count($attempts) >= $maxAttempts) {
            return false; // Account is locked
        }

        return true; // Account is not locked
    }

    public function recordFailedAttempt($identifier)
    {
        $key = 'login_attempts_' . $identifier;
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        $_SESSION[$key][] = time();
    }

    public function clearFailedAttempts($identifier)
    {
        $key = 'login_attempts_' . $identifier;
        unset($_SESSION[$key]);
    }
}
