<?php
// Determine the correct path based on where this file is being included from
$basePath = dirname(__DIR__);
if (basename(getcwd()) === 'startup') {
    // Being included from root index.php
    require_once 'src/Core/Application.php';
} else {
    // Being accessed directly from public directory
    require_once '../src/Core/Application.php';
}

use Core\Application;
use Core\Router;

// Initialize application
$app = new Application();

// Set up routing
$router = new Router();

// Root redirect
$router->get('/', function() {
    if (isset($_SESSION['user_id'])) {
        header('Location: ' . url('dashboard'));
    } else {
        header('Location: ' . url('login'));
    }
    exit;
});

// Authentication routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/choose-type', 'AuthController@showChooseType');
$router->post('/choose-type', 'AuthController@setUserType');
$router->get('/logout', 'AuthController@logout');

// Dashboard routes
$router->get('/dashboard', 'DashboardController@index');

// Profile routes - SECURITY ENHANCED
$router->get('/profile', 'ProfileController@index'); // View own profile
$router->get('/profile/settings', 'ProfileController@edit'); // Edit own profile
$router->get('/profile/create', 'ProfileController@create');
$router->post('/profile/store', 'ProfileController@store');
$router->post('/profile/update', 'ProfileController@update');

// Public profile viewing - SECURE
$router->get('/startup/{slug}', 'ProfileController@viewStartupBySlug'); // Public startup profiles
$router->get('/investor/{id}', 'ProfileController@viewInvestorPublic'); // Public investor profiles
$router->get('/profile/view/{id}', 'ProfileController@viewSecure'); // Private access with permissions

// Search routes
$router->get('/search/startups', 'SearchController@startups');
$router->get('/search/investors', 'SearchController@investors');
$router->post('/search/filter', 'SearchController@filter');
$router->get('/search/suggestions', 'SearchController@suggestions');
$router->get('/search/quick', 'SearchController@quickSearch');

// Matching routes
$router->get('/matches', 'MatchingController@index');
$router->get('/matches/mutual', 'MatchingController@mutualMatches');
$router->get('/matches/view/{id}', 'MatchingController@viewMatch');

// Matching API routes
$router->post('/api/match/find', 'MatchingController@findMatches');
$router->post('/api/match/interest', 'MatchingController@expressInterest');
$router->get('/api/match/recommendations', 'MatchingController@getMatchRecommendations');
$router->post('/api/match/delete/{id}', 'MatchingController@deleteMatch');

// Message routes (placeholder for future implementation)
$router->get('/messages', 'MessageController@index');
$router->get('/messages/conversation/{id}', 'MessageController@conversation');
$router->post('/messages/send', 'MessageController@send');

// File upload routes (placeholder for future implementation)
$router->post('/upload/logo', 'FileController@uploadLogo');
$router->post('/upload/document', 'FileController@uploadDocument');

// Admin routes (placeholder for future implementation)
$router->get('/admin', 'AdminController@index');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/startups', 'AdminController@startups');
$router->get('/admin/investors', 'AdminController@investors');

// Handle the request
try {
    $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (Exception $e) {
    // Log error and show 404
    error_log("Router error: " . $e->getMessage());
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The requested page could not be found.</p>";
    echo "<p><a href='" . url('dashboard') . "'>Return to Dashboard</a></p>";
}