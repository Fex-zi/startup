<?php
// tests/utilities/cleanup-test-profiles-fixed.php
// Fixed cleanup script using correct Database methods

require_once __DIR__ . '/../../src/Core/Application.php';
require_once __DIR__ . '/../../src/Core/Database.php';

use Core\Application;
use Core\Database;

echo "<h1>🧹 Profile Test Data Cleanup (Fixed)</h1>";
echo "<p>Removing any leftover test data from profile tests...</p>";

try {
    // Initialize application
    $app = new Application();
    $db = Database::getInstance();
    
    echo "<h2>Cleaning up test users...</h2>";
    
    // Remove test users by email pattern
    $testEmails = [
        'testprofile.startup@example.com',
        'testprofile.investor@example.com'
    ];
    
    foreach ($testEmails as $email) {
        // Find user first
        $user = $db->fetch("SELECT id, user_type FROM users WHERE email = ?", [$email]);
        
        if ($user) {
            echo "Found test user: $email (ID: {$user['id']})<br>";
            
            // Delete related profile data first
            if ($user['user_type'] === 'startup') {
                $startup = $db->fetch("SELECT id FROM startups WHERE user_id = ?", [$user['id']]);
                if ($startup) {
                    // Use delete() method correctly
                    $deleted = $db->delete("DELETE FROM startups WHERE user_id = ?", [$user['id']]);
                    echo "&nbsp;&nbsp;✅ Deleted startup profile ($deleted records)<br>";
                }
            } else {
                $investor = $db->fetch("SELECT id FROM investors WHERE user_id = ?", [$user['id']]);
                if ($investor) {
                    // Use delete() method correctly
                    $deleted = $db->delete("DELETE FROM investors WHERE user_id = ?", [$user['id']]);
                    echo "&nbsp;&nbsp;✅ Deleted investor profile ($deleted records)<br>";
                }
            }
            
            // Delete user
            $deleted = $db->delete("DELETE FROM users WHERE id = ?", [$user['id']]);
            echo "&nbsp;&nbsp;✅ Deleted user: $email ($deleted records)<br>";
        } else {
            echo "No test user found for: $email<br>";
        }
    }
    
    echo "<h2>Cleaning up test industries...</h2>";
    
    // Remove test industries
    $testIndustries = $db->fetchAll("SELECT id, name FROM industries WHERE name LIKE 'Test%Profile%'");
    
    if ($testIndustries) {
        foreach ($testIndustries as $industry) {
            $deleted = $db->delete("DELETE FROM industries WHERE id = ?", [$industry['id']]);
            echo "✅ Deleted test industry: {$industry['name']} ($deleted records)<br>";
        }
    } else {
        echo "No test industries found<br>";
    }
    
    echo "<h2>Cleaning up any orphaned test data...</h2>";
    
    // Clean up any startups without users
    $orphanStartups = $db->delete("DELETE FROM startups WHERE user_id NOT IN (SELECT id FROM users)");
    if ($orphanStartups > 0) {
        echo "✅ Cleaned up $orphanStartups orphaned startup records<br>";
    } else {
        echo "No orphaned startup records found<br>";
    }
    
    // Clean up any investors without users  
    $orphanInvestors = $db->delete("DELETE FROM investors WHERE user_id NOT IN (SELECT id FROM users)");
    if ($orphanInvestors > 0) {
        echo "✅ Cleaned up $orphanInvestors orphaned investor records<br>";
    } else {
        echo "No orphaned investor records found<br>";
    }
    
    // Clean up any matches with invalid references
    if ($db->tableExists('matches')) {
        $orphanMatches = $db->delete("DELETE FROM matches WHERE startup_id NOT IN (SELECT id FROM startups) OR investor_id NOT IN (SELECT id FROM investors)");
        if ($orphanMatches > 0) {
            echo "✅ Cleaned up $orphanMatches orphaned match records<br>";
        } else {
            echo "No orphaned match records found<br>";
        }
    }
    
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2>✅ Cleanup Complete!</h2>";
    echo "<p>All test data has been cleaned up. You can now run the profile test again.</p>";
    echo "</div>";
    
    // Show current table counts
    echo "<h2>📊 Current Database Status</h2>";
    $tables = ['users', 'startups', 'investors', 'industries', 'matches'];
    
    foreach ($tables as $table) {
        if ($db->tableExists($table)) {
            $count = $db->getTableCount($table);
            echo "• $table: $count records<br>";
        } else {
            echo "• $table: ❌ Table doesn't exist<br>";
        }
    }
    
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go back to <code>tests/integration/ProfileSystemTest.php</code></li>";
    echo "<li>Run the test again - it should work perfectly now</li>";
    echo "<li>All tests should pass with the GD extension enabled</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2>❌ Cleanup Error</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<h2>🔄 Alternative Quick Fix</h2>";
echo "<p>If you prefer a quick manual cleanup, run these SQL commands in phpMyAdmin:</p>";
echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "DELETE FROM users WHERE email LIKE 'testprofile%';\n";
echo "DELETE FROM industries WHERE name LIKE 'Test%Profile%';\n";
echo "DELETE FROM startups WHERE user_id NOT IN (SELECT id FROM users);\n";
echo "DELETE FROM investors WHERE user_id NOT IN (SELECT id FROM users);\n";
echo "</pre>";

echo "<p><a href='../integration/ProfileSystemTest.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Run Profile Test Again</a></p>";
?>