<?php

require_once __DIR__ . '/../../src/Core/Application.php';
require_once __DIR__ . '/../../src/Core/Database.php';

use Core\Application;
use Core\Database;

echo "<h1>Startup Platform Debug Script</h1>";
echo "<p><em>Location: tests/utilities/debug.php</em></p>";

// Test 1: Application initialization
echo "<h2>1. Testing Application Initialization</h2>";
try {
    $app = new Application();
    echo "✅ Application initialized successfully<br>";
} catch (Exception $e) {
    echo "❌ Application initialization failed: " . $e->getMessage() . "<br>";
}

// Test 2: Database connection
echo "<h2>2. Testing Database Connection</h2>";
try {
    $db = Database::getInstance();
    echo "✅ Database connection established<br>";
    
    // Test a simple query
    $result = $db->fetch("SELECT 1 as test");
    if ($result && $result['test'] == 1) {
        echo "✅ Database query test successful<br>";
    } else {
        echo "❌ Database query test failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: Check if tables exist
echo "<h2>3. Testing Database Tables</h2>";
try {
    $tables = ['users', 'startups', 'investors', 'industries', 'matches'];
    foreach ($tables as $table) {
        if ($db->tableExists($table)) {
            echo "✅ Table '$table' exists<br>";
            
            // Count records using safe method
            $count = $db->getTableCount($table);
            echo "&nbsp;&nbsp;└─ Records: " . $count . "<br>";
        } else {
            echo "❌ Table '$table' missing<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Table check failed: " . $e->getMessage() . "<br>";
}

// Test 4: Test Models
echo "<h2>4. Testing Models</h2>";
try {
    require_once __DIR__ . '/../../src/Models/BaseModel.php';
    require_once __DIR__ . '/../../src/Models/User.php';
    require_once __DIR__ . '/../../src/Models/Industry.php';
    
    $userModel = new Models\User();
    echo "✅ User model instantiated<br>";
    
    $industryModel = new Models\Industry();
    $industries = $industryModel->getActiveIndustries();
    echo "✅ Industry model working - found " . count($industries) . " industries<br>";
    
} catch (Exception $e) {
    echo "❌ Model test failed: " . $e->getMessage() . "<br>";
}

// Test 5: Test Controllers
echo "<h2>5. Testing Controllers</h2>";
try {
    require_once __DIR__ . '/../../src/Controllers/DashboardController.php';
    require_once __DIR__ . '/../../src/Controllers/SearchController.php';
    require_once __DIR__ . '/../../src/Services/SearchService.php';
    
    echo "✅ Controller files loaded successfully<br>";
    
    // Test DashboardController instantiation
    $dashboardController = new Controllers\DashboardController();
    echo "✅ DashboardController instantiated successfully<br>";
    
} catch (Exception $e) {
    echo "❌ Controller test failed: " . $e->getMessage() . "<br>";
}

// Test 6: URL Generation
echo "<h2>6. Testing URL Generation</h2>";
try {
    if (function_exists('url')) {
        echo "✅ URL function available<br>";
        echo "&nbsp;&nbsp;Dashboard URL: " . url('dashboard') . "<br>";
        echo "&nbsp;&nbsp;Search URL: " . url('search/investors') . "<br>";
    } else {
        echo "❌ URL function not available<br>";
    }
} catch (Exception $e) {
    echo "❌ URL test failed: " . $e->getMessage() . "<br>";
}

// Test 7: Session functionality
echo "<h2>7. Testing Session</h2>";
try {
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "✅ Session is active<br>";
    } else {
        session_start();
        echo "✅ Session started<br>";
    }
    
    // Test session variables
    $_SESSION['test_var'] = 'test_value';
    if ($_SESSION['test_var'] === 'test_value') {
        echo "✅ Session variables working<br>";
        unset($_SESSION['test_var']);
    }
} catch (Exception $e) {
    echo "❌ Session test failed: " . $e->getMessage() . "<br>";
}

// Test 8: File permissions and directory structure
echo "<h2>8. Testing File Permissions and Directories</h2>";
$dirs_to_check = [
    'storage/cache',
    'storage/logs', 
    'public/uploads',
    'src/Views/dashboard',
    'src/Controllers'
];

foreach ($dirs_to_check as $dir) {
    $fullPath = __DIR__ . '/../../' . $dir;
    if (file_exists($fullPath)) {
        if (is_writable($fullPath)) {
            echo "✅ Directory '$dir' exists and is writable<br>";
        } else {
            echo "⚠️ Directory '$dir' exists but is not writable<br>";
        }
    } else {
        echo "❌ Directory '$dir' does not exist<br>";
        
        // Try to create missing directories
        if (strpos($dir, 'storage/') === 0 || strpos($dir, 'public/uploads') === 0) {
            try {
                if (mkdir($fullPath, 0755, true)) {
                    echo "&nbsp;&nbsp;✅ Created directory '$dir'<br>";
                } else {
                    echo "&nbsp;&nbsp;❌ Failed to create directory '$dir'<br>";
                }
            } catch (Exception $e) {
                echo "&nbsp;&nbsp;❌ Error creating '$dir': " . $e->getMessage() . "<br>";
            }
        }
    }
}

// Test 9: Critical Files Check
echo "<h2>9. Testing Critical Files</h2>";
$critical_files = [
    'src/Controllers/DashboardController.php',
    'src/Controllers/SearchController.php',
    'src/Views/dashboard/investor.php',
    'src/Views/dashboard/startup.php',
    'src/Views/layouts/dashboard.php',
    'config/database.php'
];

foreach ($critical_files as $file) {
    $fullPath = __DIR__ . '/../../' . $file;
    if (file_exists($fullPath)) {
        echo "✅ Critical file '$file' exists<br>";
    } else {
        echo "❌ Critical file '$file' missing<br>";
    }
}

// Test 10: Router Test
echo "<h2>10. Testing Router Functionality</h2>";
try {
    require_once __DIR__ . '/../../src/Core/Router.php';
    $router = new Core\Router();
    echo "✅ Router class loaded successfully<br>";
    
    // Test route registration
    $router->get('/test', function() { return 'test'; });
    echo "✅ Route registration working<br>";
    
} catch (Exception $e) {
    echo "❌ Router test failed: " . $e->getMessage() . "<br>";
}

echo "<h2>Debug Complete</h2>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li><a href='" . __DIR__ . "/../../dashboard'>Test Dashboard</a></li>";
echo "<li><a href='" . __DIR__ . "/../../search/investors'>Test Investor Search</a></li>";
echo "<li><a href='" . __DIR__ . "/../../search/startups'>Test Startup Search</a></li>";
echo "<li><a href='" . __DIR__ . "/../../login'>Test Login Page</a></li>";
echo "</ul>";

// Summary
$allTablesExist = true;
$tables = ['users', 'startups', 'investors', 'industries', 'matches'];
foreach ($tables as $table) {
    if (!$db->tableExists($table)) {
        $allTablesExist = false;
        break;
    }
}

if ($allTablesExist) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>✅ System Status: Ready</h3>";
    echo "<p>All core components are working. You can now use the platform!</p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>⚠️ System Status: Setup Required</h3>";
    echo "<p>Some database tables are missing. Please run the migration script:</p>";
    echo "<code>php " . __DIR__ . "/../../scripts/migrate.php</code>";
    echo "</div>";
}

echo "<h2>Navigation</h2>";
echo "<p>";
echo "<a href='" . __DIR__ . "/database-diagnostic.php'>Database Diagnostic</a> | ";
echo "<a href='" . __DIR__ . "/setup-directories.php'>Setup Directories</a> | ";
echo "<a href='" . __DIR__ . "/test-data-seeder.php'>Seed Test Data</a>";
echo "</p>";
?>