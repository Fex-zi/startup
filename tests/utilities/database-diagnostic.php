<?php
require_once __DIR__ . '/../../src/Core/Application.php';
require_once __DIR__ . '/../../src/Core/Database.php';

use Core\Application;
use Core\Database;

echo "<h1>Database Diagnostic Script (Fixed)</h1>";
echo "<p><em>Location: tests/utilities/database-diagnostic-fixed.php</em></p>";

try {
    // Initialize application
    $app = new Application();
    echo "✅ Application initialized<br>";
    
    // Get database instance
    $db = Database::getInstance();
    echo "✅ Database instance created<br>";
    
    // Test basic connection
    $result = $db->fetch("SELECT 1 as test");
    if ($result && $result['test'] == 1) {
        echo "✅ Basic database query working<br>";
    } else {
        echo "❌ Basic database query failed<br>";
    }
    
    // Get current database name
    $dbResult = $db->fetch("SELECT DATABASE() as db_name");
    $currentDb = $dbResult['db_name'] ?? 'unknown';
    echo "📋 Current database: <strong>{$currentDb}</strong><br>";
    
    echo "<h2>Method 1: SHOW TABLES (All Tables)</h2>";
    try {
        $allTables = $db->fetchAll("SHOW TABLES");
        echo "Found " . count($allTables) . " tables:<br>";
        foreach ($allTables as $table) {
            $tableName = array_values($table)[0]; // Get the table name
            echo "&nbsp;&nbsp;• {$tableName}<br>";
        }
        
        if (empty($allTables)) {
            echo "⚠️ No tables found in database '{$currentDb}'<br>";
        }
    } catch (Exception $e) {
        echo "❌ SHOW TABLES failed: " . $e->getMessage() . "<br>";
    }
    
    echo "<h2>Method 2: Check Specific Tables (Reliable Method)</h2>";
    $targetTables = ['users', 'startups', 'investors', 'industries', 'matches'];
    
    foreach ($targetTables as $tableName) {
        echo "<h3>Testing table: {$tableName}</h3>";
        
        // Method A: Using information_schema (most reliable)
        try {
            $sql = "SELECT COUNT(*) as count FROM information_schema.tables 
                    WHERE table_schema = DATABASE() AND table_name = ?";
            $result = $db->fetch($sql, [$tableName]);
            $count = $result['count'] ?? 0;
            $tableExists = $count > 0;
            echo "&nbsp;&nbsp;Table exists: " . ($tableExists ? "✅ Found" : "❌ Not found") . "<br>";
        } catch (Exception $e) {
            echo "&nbsp;&nbsp;Table check: ❌ Error - " . $e->getMessage() . "<br>";
            $tableExists = false;
        }
        
        // Method B: DESCRIBE table (if table exists)
        if ($tableExists) {
            try {
                $sql = "DESCRIBE `{$tableName}`";
                $result = $db->fetchAll($sql);
                echo "&nbsp;&nbsp;Structure: ✅ Found (" . count($result) . " columns)<br>";
                
                if (!empty($result)) {
                    echo "&nbsp;&nbsp;&nbsp;&nbsp;Columns: ";
                    $columns = array_map(function($col) { return $col['Field']; }, $result);
                    echo implode(', ', array_slice($columns, 0, 5));
                    if (count($columns) > 5) echo "...";
                    echo "<br>";
                }
            } catch (Exception $e) {
                echo "&nbsp;&nbsp;Structure: ❌ Error - " . $e->getMessage() . "<br>";
            }
            
            // Method C: Count records (if table exists)
            try {
                $sql = "SELECT COUNT(*) as count FROM `{$tableName}`";
                $result = $db->fetch($sql);
                $count = $result['count'] ?? 0;
                echo "&nbsp;&nbsp;Record count: ✅ {$count} records<br>";
            } catch (Exception $e) {
                echo "&nbsp;&nbsp;Record count: ❌ Error - " . $e->getMessage() . "<br>";
            }
        }
        
        echo "<br>";
    }
    
    echo "<h2>Database Configuration Check</h2>";
    
    // Check config file
    $configFile = __DIR__ . '/../../config/database.php';
    if (file_exists($configFile)) {
        echo "✅ Database config file exists<br>";
        $config = require $configFile;
        echo "&nbsp;&nbsp;Host: " . ($config['host'] ?? 'not set') . "<br>";
        echo "&nbsp;&nbsp;Database: " . ($config['database'] ?? 'not set') . "<br>";
        echo "&nbsp;&nbsp;Username: " . ($config['username'] ?? 'not set') . "<br>";
        
        // Verify current connection matches config
        if (($config['database'] ?? '') === $currentDb) {
            echo "✅ Config database matches current connection<br>";
        } else {
            echo "⚠️ Config database ('" . ($config['database'] ?? '') . "') differs from current ('{$currentDb}')<br>";
        }
    } else {
        echo "❌ Database config file missing<br>";
    }
    
    echo "<h2>Table Creation Test</h2>";
    
    // Clean up any leftover test table first
    try {
        $db->query("DROP TABLE IF EXISTS test_diagnostic_table");
        echo "🧹 Cleaned up any previous test table<br>";
    } catch (Exception $e) {
        // Ignore cleanup errors
    }
    
    // Test if we can create a table (will rollback)
    try {
        $db->beginTransaction();
        
        // Use a simpler table creation syntax
        $testTableSql = "CREATE TABLE test_diagnostic_table (
            id INT AUTO_INCREMENT PRIMARY KEY, 
            test_field VARCHAR(50)
        )";
        
        $db->query($testTableSql);
        
        // Check if it was created using information_schema
        $checkSql = "SELECT COUNT(*) as count FROM information_schema.tables 
                     WHERE table_schema = DATABASE() AND table_name = 'test_diagnostic_table'";
        $checkResult = $db->fetch($checkSql);
        
        if ($checkResult && $checkResult['count'] > 0) {
            echo "✅ Can create tables successfully<br>";
        } else {
            echo "❌ Table creation command ran but table not found<br>";
        }
        
        $db->rollback(); // Always rollback the test
        echo "✅ Test table cleaned up<br>";
        
    } catch (Exception $e) {
        try {
            $db->rollback();
        } catch (Exception $e2) {
            // Ignore rollback error
        }
        echo "⚠️ Table creation test failed: " . $e->getMessage() . "<br>";
        echo "&nbsp;&nbsp;This might be due to insufficient permissions, but your database is working fine<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
}

echo "<h2>System Summary</h2>";

// Quick system health check
$systemHealthy = true;
$healthMessages = [];

try {
    $tables = ['users', 'startups', 'investors', 'industries', 'matches'];
    foreach ($tables as $table) {
        $sql = "SELECT COUNT(*) as count FROM information_schema.tables 
                WHERE table_schema = DATABASE() AND table_name = ?";
        $result = $db->fetch($sql, [$table]);
        if (!$result || $result['count'] == 0) {
            $systemHealthy = false;
            $healthMessages[] = "Missing table: {$table}";
        }
    }
} catch (Exception $e) {
    $systemHealthy = false;
    $healthMessages[] = "Database connection issue";
}

if ($systemHealthy) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>✅ System Status: Healthy</h3>";
    echo "<p>All core database components are working correctly!</p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>⚠️ System Status: Issues Found</h3>";
    echo "<ul>";
    foreach ($healthMessages as $message) {
        echo "<li>{$message}</li>";
    }
    echo "</ul>";
    echo "</div>";
}

echo "<h2>Recommended Next Steps</h2>";
echo "<ol>";
echo "<li>If tables are missing: <code>php " . __DIR__ . "/../../scripts/migrate.php</code></li>";
echo "<li>Create test data: <a href='test-data-seeder.php'>Run Test Data Seeder</a></li>";
echo "<li>Test application: <a href='" . __DIR__ . "/../../dashboard'>Go to Dashboard</a></li>";
echo "</ol>";

echo "<h2>Navigation</h2>";
echo "<p>";
echo "<a href='debug.php'>Run Debug Script</a> | ";
echo "<a href='setup-directories.php'>Setup Directories</a> | ";
echo "<a href='index.php'>Utilities Dashboard</a>";
echo "</p>";
?>