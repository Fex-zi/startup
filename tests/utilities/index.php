<?php
require_once __DIR__ . '/../../src/Core/Application.php';
require_once __DIR__ . '/../../src/Core/Database.php';

use Core\Application;
use Core\Database;

// Basic system check
$systemStatus = 'unknown';
$dbConnected = false;
$tablesExist = false;

try {
    $app = new Application();
    $db = Database::getInstance();
    $dbConnected = true;
    
    // Quick table check
    $requiredTables = ['users', 'startups', 'investors', 'industries', 'matches'];
    $existingTables = 0;
    foreach ($requiredTables as $table) {
        if ($db->tableExists($table)) {
            $existingTables++;
        }
    }
    $tablesExist = ($existingTables === count($requiredTables));
    
    if ($tablesExist) {
        $systemStatus = 'ready';
    } else {
        $systemStatus = 'setup_needed';
    }
} catch (Exception $e) {
    $systemStatus = 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Utilities Dashboard</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }
        .status-bar {
            padding: 20px 30px;
            border-bottom: 1px solid #e9ecef;
        }
        .status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 0.5px;
        }
        .status.ready {
            background: #d4edda;
            color: #155724;
        }
        .status.setup_needed {
            background: #fff3cd;
            color: #856404;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
        }
        .content {
            padding: 30px;
        }
        .utilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .utility-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            transition: all 0.3s ease;
            background: #fff;
        }
        .utility-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .utility-card h3 {
            margin: 0 0 15px 0;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .utility-card .icon {
            font-size: 1.5em;
        }
        .utility-card p {
            margin: 0 0 20px 0;
            color: #6c757d;
            font-size: 0.95em;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn.btn-success {
            background: #28a745;
        }
        .btn.btn-success:hover {
            background: #1e7e34;
        }
        .btn.btn-warning {
            background: #ffc107;
            color: #212529;
        }
        .btn.btn-warning:hover {
            background: #e0a800;
        }
        .btn.btn-danger {
            background: #dc3545;
        }
        .btn.btn-danger:hover {
            background: #c82333;
        }
        .btn.btn-secondary {
            background: #6c757d;
        }
        .btn.btn-secondary:hover {
            background: #545b62;
        }
        .quick-actions {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .quick-actions h3 {
            margin: 0 0 20px 0;
            color: #495057;
        }
        .navigation {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
        }
        .navigation h3 {
            margin: 0 0 20px 0;
            color: #495057;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Test Utilities Dashboard</h1>
            <p>Development and testing tools for the Startup-Investor Platform</p>
        </div>
        
        <div class="status-bar">
            <strong>System Status:</strong> 
            <span class="status <?= $systemStatus ?>">
                <?php
                switch($systemStatus) {
                    case 'ready':
                        echo '✅ Ready';
                        break;
                    case 'setup_needed':
                        echo '⚠️ Setup Required';
                        break;
                    case 'error':
                        echo '❌ Error';
                        break;
                    default:
                        echo '❓ Unknown';
                }
                ?>
            </span>
            
            <?php if ($dbConnected): ?>
                <span style="margin-left: 20px;">Database: ✅ Connected</span>
            <?php else: ?>
                <span style="margin-left: 20px;">Database: ❌ Not Connected</span>
            <?php endif; ?>
            
            <?php if ($tablesExist): ?>
                <span style="margin-left: 20px;">Tables: ✅ All Present</span>
            <?php else: ?>
                <span style="margin-left: 20px;">Tables: ❌ Missing Tables</span>
            <?php endif; ?>
        </div>
        
        <div class="content">
            <?php if ($systemStatus === 'setup_needed'): ?>
            <div class="warning">
                <strong>⚠️ Setup Required:</strong> Some database tables are missing. Run the setup utilities below to initialize the system.
            </div>
            <?php endif; ?>
            
            <?php if ($systemStatus === 'error'): ?>
            <div class="warning">
                <strong>❌ System Error:</strong> Cannot connect to database. Check your configuration and run the diagnostic tools.
            </div>
            <?php endif; ?>
            
            <div class="quick-actions">
                <h3>🚀 Quick Actions</h3>
                <?php if ($systemStatus === 'setup_needed'): ?>
                    <a href="setup-directories.php" class="btn btn-warning">1. Setup Directories</a>
                    <a href="../../scripts/migrate.php" class="btn btn-warning">2. Run Migrations</a>
                    <a href="test-data-seeder.php" class="btn btn-success">3. Create Test Data</a>
                    <a href="debug.php" class="btn btn-secondary">4. Verify Setup</a>
                <?php elseif ($systemStatus === 'ready'): ?>
                    <a href="debug.php" class="btn btn-success">🔍 Run Full Diagnostic</a>
                    <a href="test-data-seeder.php" class="btn btn-success">🌱 Create Test Data</a>
                    <a href="../../dashboard" class="btn btn-primary">🏠 Go to Dashboard</a>
                    <a href="../../login" class="btn btn-primary">🔑 Test Login</a>
                <?php else: ?>
                    <a href="database-diagnostic.php" class="btn btn-danger">🔧 Database Diagnostic</a>
                    <a href="debug.php" class="btn btn-danger">🔍 System Debug</a>
                <?php endif; ?>
            </div>
            
            <div class="utilities-grid">
                <div class="utility-card">
                    <h3><span class="icon">🔍</span>System Debug</h3>
                    <p>Comprehensive system diagnostic that tests application initialization, database connectivity, table structure, models, controllers, and file permissions.</p>
                    <a href="debug.php" class="btn">Run Debug</a>
                </div>
                
                <div class="utility-card">
                    <h3><span class="icon">🗄️</span>Database Diagnostic</h3>
                    <p>Specialized database testing with detailed table analysis, connection verification, and structure validation. Perfect for troubleshooting database issues.</p>
                    <a href="database-diagnostic.php" class="btn">Run Diagnostic</a>
                </div>
                
                <div class="utility-card">
                    <h3><span class="icon">📁</span>Directory Setup</h3>
                    <p>Creates all required directories and essential files with proper permissions. Run this first when setting up a new environment.</p>
                    <a href="setup-directories.php" class="btn btn-success">Setup Directories</a>
                </div>
                
                <div class="utility-card">
                    <h3><span class="icon">🌱</span>Test Data Seeder</h3>
                    <p>Creates realistic test data including industries, users, startups, and investors. Includes login credentials for testing all user flows.</p>
                    <a href="test-data-seeder.php" class="btn btn-success">Seed Test Data</a>
                </div>
                
                <div class="utility-card">
                    <h3><span class="icon">🧹</span>Data Cleanup</h3>
                    <p>Safely removes test data with options for partial or complete cleanup. Includes confirmation prompts to prevent accidental data loss.</p>
                    <a href="cleanup-helper.php" class="btn btn-warning">Cleanup Data</a>
                </div>
                
                <div class="utility-card">
                    <h3><span class="icon">⚗️</span>Integration Tests</h3>
                    <p>Run complete workflow tests for matching system, search functionality, and user management. Validates end-to-end functionality.</p>
                    <a href="../integration/MatchingSystemTest.php" class="btn">Matching Test</a>
                    <a href="../integration/SearchSystemTest.php" class="btn">Search Test</a>
                </div>
            </div>
            
            <div class="info">
                <strong>💡 Pro Tip:</strong> Always run the debug script first when troubleshooting issues. It provides a comprehensive overview of system health and identifies common problems.
            </div>
            
            <div class="navigation">
                <h3>🧭 Navigation</h3>
                <a href="../../" class="btn btn-primary">🏠 Application Home</a>
                <a href="../../dashboard" class="btn btn-primary">📊 Dashboard</a>
                <a href="../../login" class="btn btn-secondary">🔑 Login Page</a>
                <a href="../../register" class="btn btn-secondary">📝 Register Page</a>
                <a href="../" class="btn btn-secondary">📁 Tests Directory</a>
                <a href="../integration/" class="btn btn-secondary">⚗️ Integration Tests</a>
            </div>
            
            <div class="warning">
                <strong>⚠️ Security Notice:</strong> These utilities are for development only. Never run test utilities on production systems! The cleanup utility can delete all application data.
            </div>
        </div>
    </div>
</body>
</html>