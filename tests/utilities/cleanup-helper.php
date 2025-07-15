<?php
require_once __DIR__ . '/../../src/Core/Application.php';
require_once __DIR__ . '/../../src/Core/Database.php';

use Core\Application;
use Core\Database;

echo "<h1>Test Data Cleanup Helper</h1>";
echo "<p><em>Location: tests/utilities/cleanup-helper.php</em></p>";

// Safety check
$confirmAction = $_GET['confirm'] ?? '';
if ($confirmAction !== 'yes') {
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; margin: 20px 0; border: 1px solid #ffeaa7; border-radius: 4px;'>";
    echo "<h3>⚠️ Warning: This will delete test data!</h3>";
    echo "<p>This script will remove all test data created by the test-data-seeder.php script.</p>";
    echo "<p><strong>Are you sure you want to proceed?</strong></p>";
    echo "<p>";
    echo "<a href='?confirm=yes&action=test_data' class='btn btn-danger' style='background: #dc3545; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin-right: 10px;'>Yes, Delete Test Data Only</a>";
    echo "<a href='?confirm=yes&action=all_data' class='btn btn-danger' style='background: #dc3545; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin-right: 10px;'>Yes, Delete ALL Data</a>";
    echo "<a href='" . __DIR__ . "/debug.php' style='padding: 8px 16px; text-decoration: none; border: 1px solid #ccc; border-radius: 4px;'>Cancel</a>";
    echo "</p>";
    echo "</div>";
    
    echo "<h2>What will be deleted:</h2>";
    echo "<h3>Test Data Only:</h3>";
    echo "<ul>";
    echo "<li>Test users (emails ending with testcompany.com, techstartup.com, vcfund.com, angelgroup.com)</li>";
    echo "<li>Startups associated with test users</li>";
    echo "<li>Investors associated with test users</li>";
    echo "<li>Matches involving test data</li>";
    echo "</ul>";
    
    echo "<h3>ALL Data:</h3>";
    echo "<ul>";
    echo "<li>All users</li>";
    echo "<li>All startups</li>";
    echo "<li>All investors</li>";
    echo "<li>All matches</li>";
    echo "<li>All industries (except default ones)</li>";
    echo "</ul>";
    
    exit;
}

$action = $_GET['action'] ?? 'test_data';

try {
    // Initialize application
    $app = new Application();
    $db = Database::getInstance();
    
    if ($action === 'test_data') {
        echo "<h2>Cleaning Test Data Only...</h2>";
        
        // Define test email patterns
        $testEmailPatterns = [
            '%testcompany.com',
            '%techstartup.com', 
            '%vcfund.com',
            '%angelgroup.com'
        ];
        
        // Get test user IDs
        $testUserIds = [];
        foreach ($testEmailPatterns as $pattern) {
            $users = $db->fetchAll("SELECT id FROM users WHERE email LIKE ?", [$pattern]);
            foreach ($users as $user) {
                $testUserIds[] = $user['id'];
            }
        }
        
        if (empty($testUserIds)) {
            echo "✅ No test users found to delete<br>";
        } else {
            // Get startup and investor IDs for test users
            $testStartupIds = [];
            $testInvestorIds = [];
            
            foreach ($testUserIds as $userId) {
                // Get startup IDs
                $startups = $db->fetchAll("SELECT id FROM startups WHERE user_id = ?", [$userId]);
                foreach ($startups as $startup) {
                    $testStartupIds[] = $startup['id'];
                }
                
                // Get investor IDs  
                $investors = $db->fetchAll("SELECT id FROM investors WHERE user_id = ?", [$userId]);
                foreach ($investors as $investor) {
                    $testInvestorIds[] = $investor['id'];
                }
            }
            
            // Delete matches involving test startups or investors
            if (!empty($testStartupIds) || !empty($testInvestorIds)) {
                $whereConditions = [];
                $deleteParams = [];
                
                if (!empty($testStartupIds)) {
                    $startupPlaceholders = str_repeat('?,', count($testStartupIds) - 1) . '?';
                    $whereConditions[] = "startup_id IN ($startupPlaceholders)";
                    $deleteParams = array_merge($deleteParams, $testStartupIds);
                }
                
                if (!empty($testInvestorIds)) {
                    $investorPlaceholders = str_repeat('?,', count($testInvestorIds) - 1) . '?';
                    $whereConditions[] = "investor_id IN ($investorPlaceholders)";
                    $deleteParams = array_merge($deleteParams, $testInvestorIds);
                }
                
                $whereClause = implode(' OR ', $whereConditions);
                $deletedMatches = $db->query("DELETE FROM matches WHERE $whereClause", $deleteParams);
                echo "✅ Deleted matches involving test users<br>";
            }
            
            $userIdsList = implode(',', $testUserIds);
            
            // Delete startups of test users
            $deletedStartups = $db->query("DELETE FROM startups WHERE user_id IN ($userIdsList)");
            echo "✅ Deleted startups of test users<br>";
            
            // Delete investors of test users
            $deletedInvestors = $db->query("DELETE FROM investors WHERE user_id IN ($userIdsList)");
            echo "✅ Deleted investors of test users<br>";
            
            // Delete test users
            $deletedUsers = $db->query("DELETE FROM users WHERE id IN ($userIdsList)");
            echo "✅ Deleted test users<br>";
        }
        
        // Delete test companies by slug patterns (startups only)
        $testStartupSlugs = ['technova-solutions', 'healthtrack-pro'];
        foreach ($testStartupSlugs as $slug) {
            $deleted = $db->query("DELETE FROM startups WHERE slug = ?", [$slug]);
            if ($deleted) {
                echo "✅ Deleted startup with slug: $slug<br>";
            }
        }
        
    } else if ($action === 'all_data') {
        echo "<h2>Cleaning ALL Data...</h2>";
        
        // Delete all data in proper order (respecting foreign keys)
        $db->query("DELETE FROM matches");
        echo "✅ Deleted all matches<br>";
        
        $db->query("DELETE FROM startups");
        echo "✅ Deleted all startups<br>";
        
        $db->query("DELETE FROM investors");
        echo "✅ Deleted all investors<br>";
        
        $db->query("DELETE FROM users");
        echo "✅ Deleted all users<br>";
        
        // Keep core industries, delete any test ones
        $db->query("DELETE FROM industries WHERE slug LIKE 'test-%'");
        echo "✅ Deleted test industries<br>";
        
        // Reset auto-increment counters
        $db->query("ALTER TABLE users AUTO_INCREMENT = 1");
        $db->query("ALTER TABLE startups AUTO_INCREMENT = 1");
        $db->query("ALTER TABLE investors AUTO_INCREMENT = 1");
        $db->query("ALTER TABLE matches AUTO_INCREMENT = 1");
        echo "✅ Reset auto-increment counters<br>";
    }
    
    echo "<h2>✅ Cleanup Complete!</h2>";
    
    // Show current counts
    echo "<h3>Current Database Counts:</h3>";
    $counts = [
        'users' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'],
        'startups' => $db->fetch("SELECT COUNT(*) as count FROM startups")['count'],
        'investors' => $db->fetch("SELECT COUNT(*) as count FROM investors")['count'],
        'industries' => $db->fetch("SELECT COUNT(*) as count FROM industries")['count'],
        'matches' => $db->fetch("SELECT COUNT(*) as count FROM matches")['count']
    ];
    
    foreach ($counts as $table => $count) {
        echo "• {$table}: {$count} records<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error during cleanup: " . $e->getMessage() . "<br>";
}

echo "<h2>Navigation</h2>";
echo "<p>";
echo "<a href='" . __DIR__ . "/debug.php'>Run Debug Script</a> | ";
echo "<a href='" . __DIR__ . "/test-data-seeder.php'>Create Test Data</a> | ";
echo "<a href='" . __DIR__ . "/../../dashboard'>Go to Dashboard</a>";
echo "</p>";
?>