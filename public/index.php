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

// Matching routes
$router->get('/matches', 'MatchingController@index');
$router->post('/api/match', 'MatchingController@findMatches');

// Handle the request
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
