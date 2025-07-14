<?php

return [
    'app' => [
        'name' => 'Startup Investor Platform',
        'version' => '1.0.0',
        'debug' => true,
        'timezone' => 'America/Denver',
        'url' => 'http://localhost/startup',
    ],
    
    'security' => [
        'csrf_token_name' => '_token',
        'session_name' => 'startup_session',
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
    ],
    
    'upload' => [
        'max_file_size' => 5242880, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'ppt', 'pptx'],
        'upload_path' => '../public/uploads/',
        'temp_path' => '../storage/temp/',
    ],
    
    'cache' => [
        'enabled' => true,
        'default_ttl' => 3600, // 1 hour
        'path' => '../storage/cache/',
    ],
    
    'pagination' => [
        'per_page' => 20,
        'max_per_page' => 100,
    ],
];
