<?php

/**
 * Global helper functions for the application
 * 🔥 CRITICAL FIX: Added profile completion helpers for consistent data
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

if (!function_exists('get_profile_completion')) {
    /**
     * 🔥 CRITICAL FIX: Get profile completion data for current user
     * 
     * @param int $userId
     * @param string $userType
     * @return array
     */
    function get_profile_completion($userId, $userType) {
        require_once __DIR__ . '/ProfileCalculator.php';
        return \Utils\ProfileCalculator::calculateProfileCompletion($userId, $userType);
    }
}

if (!function_exists('get_progress_data')) {
    /**
     * 🔥 CRITICAL FIX: Get complete progress data for dashboard
     * 
     * @param int $userId
     * @param string $userType
     * @return array
     */
    function get_progress_data($userId, $userType) {
        require_once __DIR__ . '/ProfileCalculator.php';
        return \Utils\ProfileCalculator::getProgressData($userId, $userType);
    }
}

if (!function_exists('render_profile_completion_widget')) {
    /**
     * 🔥 NEW: Render profile completion widget HTML - PHP 8+ Safe
     * 
     * @param int $userId
     * @param string $userType
     * @return string
     */
    function render_profile_completion_widget($userId, $userType) {
        try {
            $data = get_profile_completion($userId, $userType);
            $percentage = $data['percentage'] ?? 0;
            $missingItems = $data['missing_items'] ?? [];
            $nextSteps = $data['next_steps'] ?? [];
            
            $colorClass = $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
            $statusText = $percentage >= 80 ? 'Excellent!' : ($percentage >= 50 ? 'Good progress' : 'Needs attention');
            
            ob_start();
            ?>
            <div class="profile-completion-widget card">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Profile Completeness: <?= $percentage ?>%
                        </h6>
                        <span class="badge bg-<?= $colorClass ?>"><?= $statusText ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-<?= $colorClass ?>" role="progressbar" 
                             style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" 
                             aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <?php if ($percentage < 100): ?>
                        <small class="text-muted">Complete your profile to attract more <?= $userType === 'startup' ? 'investors' : 'startups' ?>!</small>
                        
                        <?php if (!empty($missingItems)): ?>
                            <div class="mt-2">
                                <strong>Missing:</strong> <?= htmlspecialchars(implode(', ', array_slice($missingItems, 0, 3))) ?>
                                <?php if (count($missingItems) > 3): ?>
                                    <small class="text-muted">and <?= count($missingItems) - 3 ?> more...</small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <small class="text-success"><i class="fas fa-check-circle me-1"></i>Your profile is complete!</small>
                    <?php endif; ?>
                    
                    <?php if (!empty($nextSteps) && !in_array('Profile looks complete!', $nextSteps)): ?>
                        <div class="mt-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="fw-bold text-primary">Next Steps:</small>
                                <a href="<?= url('profile/edit') ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i>Complete Profile
                                </a>
                            </div>
                            <ul class="list-unstyled mt-2 mb-0">
                                <?php foreach (array_slice($nextSteps, 0, 3) as $step): ?>
                                    <li><small><i class="fas fa-arrow-right text-primary me-2"></i><?= htmlspecialchars((string)$step) ?></small></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
            
        } catch (Exception $e) {
            // Fallback widget on error
            error_log("Profile widget error: " . $e->getMessage());
            return '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Profile analysis loading...</div>';
        }
    }
}
