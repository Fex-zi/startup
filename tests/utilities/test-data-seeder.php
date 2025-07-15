<?php
require_once __DIR__ . '/../../src/Core/Application.php';
require_once __DIR__ . '/../../src/Core/Database.php';
require_once __DIR__ . '/../../src/Models/BaseModel.php';
require_once __DIR__ . '/../../src/Models/User.php';
require_once __DIR__ . '/../../src/Models/Startup.php';
require_once __DIR__ . '/../../src/Models/Investor.php';
require_once __DIR__ . '/../../src/Models/Industry.php';

use Core\Application;
use Core\Database;
use Models\User;
use Models\Startup;
use Models\Investor;
use Models\Industry;

echo "<h1>Test Data Seeder</h1>";
echo "<p><em>Location: tests/utilities/test-data-seeder.php</em></p>";

try {
    // Initialize application
    $app = new Application();
    $db = Database::getInstance();
    
    echo "<h2>Creating Test Industries...</h2>";
    $industryModel = new Industry();
    
    $industries = [
        ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Software, hardware, and tech services'],
        ['name' => 'Healthcare', 'slug' => 'healthcare', 'description' => 'Medical devices, digital health, biotech'],
        ['name' => 'FinTech', 'slug' => 'fintech', 'description' => 'Financial technology and services'],
        ['name' => 'E-commerce', 'slug' => 'ecommerce', 'description' => 'Online retail and marketplaces'],
        ['name' => 'Education', 'slug' => 'education', 'description' => 'EdTech and learning platforms'],
    ];
    
    $industryIds = [];
    foreach ($industries as $industry) {
        // Check if industry exists
        $existing = $db->fetch("SELECT id FROM industries WHERE slug = ?", [$industry['slug']]);
        if ($existing) {
            $industryIds[] = $existing['id'];
            echo "✅ Industry '{$industry['name']}' already exists (ID: {$existing['id']})<br>";
        } else {
            $industry['is_active'] = true;
            $industryId = $industryModel->create($industry);
            $industryIds[] = $industryId;
            echo "✅ Created industry '{$industry['name']}' (ID: {$industryId})<br>";
        }
    }
    
    echo "<h2>Creating Test Users...</h2>";
    $userModel = new User();
    
    // Create test startup users
    $startupUsers = [
        [
            'email' => 'founder1@testcompany.com',
            'password_hash' => password_hash('testpass123', PASSWORD_DEFAULT),
            'user_type' => 'startup',
            'first_name' => 'John',
            'last_name' => 'Smith',
            'location' => 'San Francisco, CA',
            'profile_completed' => true,
            'is_active' => true
        ],
        [
            'email' => 'founder2@techstartup.com',
            'password_hash' => password_hash('testpass123', PASSWORD_DEFAULT),
            'user_type' => 'startup',
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'location' => 'Austin, TX',
            'profile_completed' => true,
            'is_active' => true
        ],
    ];
    
    // Create test investor users
    $investorUsers = [
        [
            'email' => 'investor1@vcfund.com',
            'password_hash' => password_hash('testpass123', PASSWORD_DEFAULT),
            'user_type' => 'investor',
            'first_name' => 'Michael',
            'last_name' => 'Brown',
            'location' => 'New York, NY',
            'profile_completed' => true,
            'is_active' => true
        ],
        [
            'email' => 'investor2@angelgroup.com',
            'password_hash' => password_hash('testpass123', PASSWORD_DEFAULT),
            'user_type' => 'investor',
            'first_name' => 'Emily',
            'last_name' => 'Davis',
            'location' => 'Boston, MA',
            'profile_completed' => true,
            'is_active' => true
        ],
    ];
    
    $createdUsers = [];
    foreach (array_merge($startupUsers, $investorUsers) as $userData) {
        // Check if user exists
        $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$userData['email']]);
        if ($existing) {
            $createdUsers[] = $existing['id'];
            echo "✅ User '{$userData['email']}' already exists (ID: {$existing['id']})<br>";
        } else {
            $userId = $userModel->create($userData);
            $createdUsers[] = $userId;
            echo "✅ Created user '{$userData['email']}' (ID: {$userId})<br>";
        }
    }
    
    echo "<h2>Creating Test Startups...</h2>";
    $startupModel = new Startup();
    
    $startups = [
        [
            'user_id' => $createdUsers[0], // First startup user
            'company_name' => 'TechNova Solutions',
            'slug' => 'technova-solutions',
            'industry_id' => $industryIds[0], // Technology
            'description' => 'AI-powered business automation platform for small businesses',
            'founded_year' => 2023,
            'funding_stage' => 'seed',
            'funding_goal' => 500000,
            'current_revenue' => 50000,
            'team_size' => 5,
            'website_url' => 'https://technova.com',
            'location' => 'San Francisco, CA',
            'business_model' => 'SaaS',
            'target_market' => 'Small and medium businesses',
            'competitive_advantage' => 'First-to-market AI automation specifically for SMBs',
            'is_active' => true
        ],
        [
            'user_id' => $createdUsers[1], // Second startup user
            'company_name' => 'HealthTrack Pro',
            'slug' => 'healthtrack-pro',
            'industry_id' => $industryIds[1], // Healthcare
            'description' => 'Digital health platform for chronic disease management',
            'founded_year' => 2022,
            'funding_stage' => 'series_a',
            'funding_goal' => 2000000,
            'current_revenue' => 200000,
            'team_size' => 12,
            'website_url' => 'https://healthtrackpro.com',
            'location' => 'Austin, TX',
            'business_model' => 'B2B2C',
            'target_market' => 'Healthcare providers and patients',
            'competitive_advantage' => 'Proprietary ML algorithms for personalized care plans',
            'is_active' => true
        ],
    ];
    
    foreach ($startups as $startupData) {
        // Check if startup exists
        $existing = $db->fetch("SELECT id FROM startups WHERE slug = ?", [$startupData['slug']]);
        if ($existing) {
            echo "✅ Startup '{$startupData['company_name']}' already exists (ID: {$existing['id']})<br>";
        } else {
            $startupId = $startupModel->create($startupData);
            echo "✅ Created startup '{$startupData['company_name']}' (ID: {$startupId})<br>";
        }
    }
    
    echo "<h2>Creating Test Investors...</h2>";
    $investorModel = new Investor();
    
    $investors = [
        [
            'user_id' => $createdUsers[2], // First investor user
            'company_name' => 'TechVentures Capital',
            'investor_type' => 'vc',
            'preferred_industries' => json_encode([$industryIds[0], $industryIds[2]]), // Tech, FinTech
            'investment_range_min' => 100000,
            'investment_range_max' => 5000000,
            'investment_stages' => json_encode(['seed', 'series_a']),
            'bio' => 'Early-stage venture capital fund focused on technology startups',
            'website' => 'https://techventures.com',
            'location' => 'New York, NY',
            'availability_status' => 'actively_investing'
        ],
        [
            'user_id' => $createdUsers[3], // Second investor user
            'company_name' => 'Angel Syndicate Boston',
            'investor_type' => 'angel',
            'preferred_industries' => json_encode([$industryIds[1], $industryIds[4]]), // Healthcare, Education
            'investment_range_min' => 25000,
            'investment_range_max' => 500000,
            'investment_stages' => json_encode(['pre_seed', 'seed']),
            'bio' => 'Angel investor network specializing in healthcare and education technology',
            'website' => 'https://angelsyndicateboston.com',
            'location' => 'Boston, MA',
            'availability_status' => 'actively_investing'
        ],
    ];
    
    foreach ($investors as $investorData) {
        // Check if investor exists by user_id instead of slug
        $existing = $db->fetch("SELECT id FROM investors WHERE user_id = ?", [$investorData['user_id']]);
        if ($existing) {
            echo "✅ Investor '{$investorData['company_name']}' already exists (ID: {$existing['id']})<br>";
        } else {
            $investorId = $investorModel->create($investorData);
            echo "✅ Created investor '{$investorData['company_name']}' (ID: {$investorId})<br>";
        }
    }
    
    echo "<h2>✅ Test Data Seeding Complete!</h2>";
    echo "<p><strong>Test Users Created:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Startup Founders:</strong></li>";
    echo "<ul>";
    echo "<li>founder1@testcompany.com (password: testpass123)</li>";
    echo "<li>founder2@techstartup.com (password: testpass123)</li>";
    echo "</ul>";
    echo "<li><strong>Investors:</strong></li>";
    echo "<ul>";
    echo "<li>investor1@vcfund.com (password: testpass123)</li>";
    echo "<li>investor2@angelgroup.com (password: testpass123)</li>";
    echo "</ul>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "❌ Error seeding test data: " . $e->getMessage() . "<br>";
}

echo "<h2>Navigation</h2>";
echo "<p>";
echo "<a href='" . __DIR__ . "/debug.php'>Run Debug Script</a> | ";
echo "<a href='" . __DIR__ . "/cleanup-helper.php'>Cleanup Test Data</a> | ";
echo "<a href='" . __DIR__ . "/../../login'>Test Login</a>";
echo "</p>";
?>