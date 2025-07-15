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
