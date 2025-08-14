<?php

/**
 * Global helper functions for the application
 */

if (!function_exists('url')) {
    /**
     * Generate a URL for the given path
     * 
     * @param string $path
     * @return string
     */
    function url($path = '') {
        return \Core\Application::url($path);
    }
}

if (!function_exists('asset')) {
    /**
     * Generate a URL for assets
     * 
     * @param string $path
     * @return string
     */
    function asset($path) {
        return url('/public/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('upload_url')) {
    /**
     * FIXED: Generate URL for uploaded files
     * 
     * @param string $path
     * @return string
     */
    function upload_url($path) {
        if (empty($path)) return '';
        
        // Handle both old and new path formats
        if (strpos($path, '/assets/uploads/') === 0) {
            // New format: /assets/uploads/logos/file.png
            return url('/public' . $path);
        } elseif (strpos($path, 'uploads/') === 0) {
            // Alternative format: uploads/logos/file.png
            return url('/public/assets/' . $path);
        } else {
            // Legacy format: documents/file.pdf or logos/file.png
            return url('/public/assets/uploads/' . $path);
        }
    }
}

if (!function_exists('route')) {
    /**
     * Alias for url() function
     * 
     * @param string $path
     * @return string
     */
    function route($path = '') {
        return url($path);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a given URL
     * 
     * @param string $path
     * @return void
     */
    function redirect($path) {
        header('Location: ' . url($path));
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back to the previous page
     * 
     * @return void
     */
    function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('dashboard');
        header('Location: ' . $referer);
        exit;
    }
}

if (!function_exists('showToast')) {
    /**
     * Generate toast notification script
     * 
     * @param string $message
     * @param string $type (success, error, info)
     * @return string
     */
    function showToast($message, $type = 'info') {
        return "<script>
            if (typeof showToast === 'function') {
                showToast('" . addslashes($message) . "', '$type');
            } else {
                console.log('Toast: $type - " . addslashes($message) . "');
            }
        </script>";
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format currency for display
     * 
     * @param float $amount
     * @param string $currency
     * @return string
     */
    function format_currency($amount, $currency = 'USD') {
        return '$' . number_format($amount, 0);
    }
}

if (!function_exists('time_ago')) {
    /**
     * Format time as "time ago"
     * 
     * @param string $datetime
     * @return string
     */
    function time_ago($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . 'm ago';
        if ($time < 86400) return floor($time/3600) . 'h ago';
        if ($time < 2592000) return floor($time/86400) . 'd ago';
        
        return date('M j, Y', strtotime($datetime));
    }
}
