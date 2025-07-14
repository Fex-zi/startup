<?php
require_once 'src/Core/Application.php';
require_once 'src/Core/Database.php';

use Core\Application;
use Core\Database;

echo "<h1>Database Diagnostic Script</h1>";

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
    
    echo "<h2>Method 2: Check Specific Tables</h2>";
    $targetTables = ['users', 'startups', 'investors', 'industries', 'matches'];
    
    foreach ($targetTables as $tableName) {
        echo "<h3>Testing table: {$tableName}</h3>";
        
        // Method A: SHOW TABLES LIKE
        try {
            $sql = "SHOW TABLES LIKE ?";
            $result = $db->fetch($sql, [$tableName]);
            echo "&nbsp;&nbsp;SHOW TABLES LIKE: " . ($result ? "✅ Found" : "❌ Not found") . "<br>";
        } catch (Exception $e) {
            echo "&nbsp;&nbsp;SHOW TABLES LIKE: ❌ Error - " . $e->getMessage() . "<br>";
        }
        
        // Method B: information_schema
        try {
            $sql = "SELECT COUNT(*) as count FROM information_schema.tables 
                    WHERE table_schema = DATABASE() AND table_name = ?";
            $result = $db->fetch($sql, [$tableName]);
            $count = $result['count'] ?? 0;
            echo "&nbsp;&nbsp;information_schema: " . ($count > 0 ? "✅ Found" : "❌ Not found") . "<br>";
        } catch (Exception $e) {
            echo "&nbsp;&nbsp;information_schema: ❌ Error - " . $e->getMessage() . "<br>";
        }
        
        // Method C: DESCRIBE table
        try {
            $sql = "DESCRIBE `{$tableName}`";
            $result = $db->fetchAll($sql);
            echo "&nbsp;&nbsp;DESCRIBE: " . (!empty($result) ? "✅ Found (" . count($result) . " columns)" : "❌ Not found") . "<br>";
            
            if (!empty($result)) {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;Columns: ";
                $columns = array_map(function($col) { return $col['Field']; }, $result);
                echo implode(', ', array_slice($columns, 0, 5));
                if (count($columns) > 5) echo "...";
                echo "<br>";
            }
        } catch (Exception $e) {
            echo "&nbsp;&nbsp;DESCRIBE: ❌ Error - " . $e->getMessage() . "<br>";
        }
        
        // Method D: Count records
        try {
            $sql = "SELECT COUNT(*) as count FROM `{$tableName}`";
            $result = $db->fetch($sql);
            $count = $result['count'] ?? 0;
            echo "&nbsp;&nbsp;Record count: ✅ {$count} records<br>";
        } catch (Exception $e) {
            echo "&nbsp;&nbsp;Record count: ❌ Error - " . $e->getMessage() . "<br>";
        }
        
        echo "<br>";
    }
    
    echo "<h2>Database Configuration Check</h2>";
    
    // Check config file
    $configFile = 'config/database.php';
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
    
    // Test if we can create a table (will rollback)
    try {
        $db->beginTransaction();
        $testTableSql = "CREATE TABLE test_diagnostic_table (id INT PRIMARY KEY, test_field VARCHAR(50))";
        $db->query($testTableSql);
        
        // Check if it was created
        $exists = $db->fetch("SHOW TABLES LIKE 'test_diagnostic_table'");
        if ($exists) {
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
        echo "❌ Cannot create tables: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
}

echo "<h2>Recommended Next Steps</h2>";
echo "<ol>";
echo "<li>Check if you're connected to the correct database</li>";
echo "<li>Verify table names are exactly: users, startups, investors, industries, matches</li>";
echo "<li>Run migration script if tables are missing: <code>php scripts/migrate.php</code></li>";
echo "<li>Check database user permissions</li>";
echo "</ol>";

echo "<p><a href='debug.php'>Back to Debug Script</a> | <a href='dashboard'>Go to Dashboard</a></p>";
?>