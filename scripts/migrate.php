<?php

require_once __DIR__ . '/../src/Core/Application.php';
require_once __DIR__ . '/../src/Core/Database.php';

use Core\Application;
use Core\Database;

// Initialize application
$app = new Application();

try {
    $db = Database::getInstance();
    
    // Create database if it doesn't exist
    $config = require __DIR__ . '/../config/database.php';
    $dbName = $config['database'];
    
    echo "Creating database '{$dbName}' if it doesn't exist...\n";
    $db->createDatabase($dbName);
    
    // Run migrations
    $migrationDir = __DIR__ . '/../database/migrations/';
    $migrations = glob($migrationDir . '*.sql');
    sort($migrations);
    
    echo "Running migrations...\n";
    
    foreach ($migrations as $migration) {
        $filename = basename($migration);
        echo "Running migration: {$filename}\n";
        
        try {
            $db->runMigration($migration);
            echo "✓ Migration {$filename} completed successfully\n";
        } catch (Exception $e) {
            echo "✗ Migration {$filename} failed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    // Run seeds
    $seedDir = __DIR__ . '/../database/seeds/';
    $seeds = glob($seedDir . '*.sql');
    sort($seeds);
    
    echo "\nRunning seeds...\n";
    
    foreach ($seeds as $seed) {
        $filename = basename($seed);
        echo "Running seed: {$filename}\n";
        
        try {
            $db->runMigration($seed);
            echo "✓ Seed {$filename} completed successfully\n";
        } catch (Exception $e) {
            echo "✗ Seed {$filename} failed: " . $e->getMessage() . "\n";
            // Seeds are not critical, continue with other seeds
        }
    }
    
    echo "\n✓ Database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Database setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
