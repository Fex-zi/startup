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

// Authentication routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/choose-type', 'AuthController@showChooseType');
$router->post('/choose-type', 'AuthController@setUserType');
$router->get('/logout', 'AuthController@logout');

// Dashboard routes
$router->get('/', 'DashboardController@index');
$router->get('/dashboard', 'DashboardController@index');

// Profile routes
$router->get('/profile/create', 'ProfileController@create');
$router->post('/profile/store', 'ProfileController@store');
$router->get('/profile/edit', 'ProfileController@edit');
$router->post('/profile/update', 'ProfileController@update');
$router->get('/profile/view/{id}', 'ProfileController@view');

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
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);