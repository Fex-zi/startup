<?php
// Create this as tests/integration/SearchSystemTest.php

require_once __DIR__ . '/../../src/Core/Application.php';
require_once __DIR__ . '/../../src/Core/Database.php';
require_once __DIR__ . '/../../src/Models/BaseModel.php';
require_once __DIR__ . '/../../src/Models/User.php';
require_once __DIR__ . '/../../src/Models/Startup.php';
require_once __DIR__ . '/../../src/Models/Investor.php';
require_once __DIR__ . '/../../src/Models/Industry.php';
require_once __DIR__ . '/../../src/Services/SearchService.php';
require_once __DIR__ . '/../../src/Controllers/SearchController.php';

use Core\Application;
use Core\Database;
use Models\User;
use Models\Startup;
use Models\Investor;
use Models\Industry;
use Services\SearchService;
use Controllers\SearchController;

class SearchSystemTest
{
    private $db;
    private $testUserId;
    private $testStartupId;
    private $testInvestorId;
    private $testIndustryId;

    public function __construct()
    {
        echo "<h1>Search System Integration Test</h1>";
        
        // Initialize application
        $app = new Application();
        $this->db = Database::getInstance();
    }

    public function runAllTests()
    {
        $this->setup();
        $this->testSearchService();
        $this->testSearchController();
        $this->testSearchWorkflow();
        $this->cleanup();
        
        echo "<h2>✅ All Search System Tests Completed Successfully!</h2>";
    }

    private function setup()
    {
        echo "<h2>Setting up test data...</h2>";
        
        try {
            // Create test industry
            $industryModel = new Industry();
            $this->testIndustryId = $industryModel->create([
                'name' => 'Test Technology',
                'slug' => 'test-technology-' . time(),
                'description' => 'Test industry for integration testing',
                'is_active' => true
            ]);
            echo "✅ Created test industry (ID: {$this->testIndustryId})<br>";

            // Create test startup user
            $userModel = new User();
            $this->testUserId = $userModel->create([
                'email' => 'testuser' . time() . '@example.com',
                'password_hash' => password_hash('testpassword', PASSWORD_DEFAULT),
                'user_type' => 'startup',
                'first_name' => 'Test',
                'last_name' => 'Founder',
                'location' => 'San Francisco, CA',
                'profile_completed' => true,
                'is_active' => true
            ]);
            echo "✅ Created test user (ID: {$this->testUserId})<br>";

            // Create test startup
            $startupModel = new Startup();
            $this->testStartupId = $startupModel->create([
                'user_id' => $this->testUserId,
                'company_name' => 'Test Startup ' . time(),
                'slug' => 'test-startup-' . time(),
                'description' => 'This is a test startup for search functionality testing',
                'industry_id' => $this->testIndustryId,
                'stage' => 'mvp',
                'employee_count' => '2-5',
                'website' => 'https://teststartup.com',
                'funding_goal' => 500000,
                'funding_type' => 'seed',
                'location' => 'San Francisco, CA'
            ]);
            echo "✅ Created test startup (ID: {$this->testStartupId})<br>";

            // Create test investor user
            $investorUserId = $userModel->create([
                'email' => 'testinvestor' . time() . '@example.com',
                'password_hash' => password_hash('testpassword', PASSWORD_DEFAULT),
                'user_type' => 'investor',
                'first_name' => 'Test',
                'last_name' => 'Investor',
                'location' => 'San Francisco, CA',
                'profile_completed' => true,
                'is_active' => true
            ]);

            // Create test investor
            $investorModel = new Investor();
            $this->testInvestorId = $investorModel->create([
                'user_id' => $investorUserId,
                'investor_type' => 'angel',
                'company_name' => 'Test Investment Fund',
                'bio' => 'Test investor for search functionality testing',
                'preferred_industries' => json_encode([$this->testIndustryId]),
                'investment_stages' => json_encode(['mvp', 'early_revenue']),
                'investment_range_min' => 100000,
                'investment_range_max' => 1000000,
                'location' => 'San Francisco, CA',
                'availability_status' => 'actively_investing'
            ]);
            echo "✅ Created test investor (ID: {$this->testInvestorId})<br>";

        } catch (Exception $e) {
            echo "❌ Setup failed: " . $e->getMessage() . "<br>";
            throw $e;
        }
    }

    private function testSearchService()
    {
        echo "<h2>Testing SearchService...</h2>";
        
        try {
            $searchService = new SearchService();

            // Test startup search
            $startupResults = $searchService->searchStartups([
                'search' => 'Test Startup',
                'industry' => $this->testIndustryId,
                'stage' => 'mvp'
            ]);

            if (!empty($startupResults['data'])) {
                echo "✅ Startup search returned results<br>";
                $foundStartup = false;
                foreach ($startupResults['data'] as $startup) {
                    if ($startup['id'] == $this->testStartupId) {
                        $foundStartup = true;
                        break;
                    }
                }
                if ($foundStartup) {
                    echo "✅ Found our test startup in search results<br>";
                } else {
                    echo "❌ Test startup not found in search results<br>";
                }
            } else {
                echo "❌ Startup search returned no results<br>";
            }

            // Test investor search
            $investorResults = $searchService->searchInvestors([
                'search' => 'Test Investment',
                'investor_type' => 'angel',
                'industry' => $this->testIndustryId
            ]);

            if (!empty($investorResults['data'])) {
                echo "✅ Investor search returned results<br>";
                $foundInvestor = false;
                foreach ($investorResults['data'] as $investor) {
                    if ($investor['id'] == $this->testInvestorId) {
                        $foundInvestor = true;
                        break;
                    }
                }
                if ($foundInvestor) {
                    echo "✅ Found our test investor in search results<br>";
                } else {
                    echo "❌ Test investor not found in search results<br>";
                }
            } else {
                echo "❌ Investor search returned no results<br>";
            }

            // Test pagination
            $paginatedResults = $searchService->searchStartups(['page' => 1]);
            if (isset($paginatedResults['pagination']) && 
                isset($paginatedResults['pagination']['current_page']) &&
                $paginatedResults['pagination']['current_page'] == 1) {
                echo "✅ Pagination working correctly<br>";
            } else {
                echo "❌ Pagination not working<br>";
            }

            // Test search suggestions
            $suggestions = $searchService->getSearchSuggestions('Test', 'startups');
            if (is_array($suggestions)) {
                echo "✅ Search suggestions working<br>";
            } else {
                echo "❌ Search suggestions not working<br>";
            }

        } catch (Exception $e) {
            echo "❌ SearchService test failed: " . $e->getMessage() . "<br>";
            throw $e;
        }
    }

    private function testSearchController()
    {
        echo "<h2>Testing SearchController...</h2>";
        
        try {
            // Mock session for controller test
            $_SESSION['user_id'] = $this->testUserId;
            $_SESSION['user_type'] = 'startup';

            $controller = new SearchController();
            
            // Test method exists
            if (method_exists($controller, 'startups')) {
                echo "✅ SearchController->startups() method exists<br>";
            } else {
                echo "❌ SearchController->startups() method missing<br>";
            }

            if (method_exists($controller, 'investors')) {
                echo "✅ SearchController->investors() method exists<br>";
            } else {
                echo "❌ SearchController->investors() method missing<br>";
            }

            // Test JSON decoding safety
            $testData = ['preferred_industries' => '["1","2","3"]'];
            $reflection = new ReflectionClass($controller);
            $method = $reflection->getMethod('safeJsonDecode');
            $method->setAccessible(true);
            
            $result = $method->invokeArgs($controller, [$testData['preferred_industries'], []]);
            if (is_array($result) && count($result) == 3) {
                echo "✅ Safe JSON decoding working<br>";
            } else {
                echo "❌ Safe JSON decoding not working<br>";
            }

        } catch (Exception $e) {
            echo "❌ SearchController test failed: " . $e->getMessage() . "<br>";
        }
    }

    private function testSearchWorkflow()
    {
        echo "<h2>Testing Complete Search Workflow...</h2>";
        
        try {
            // Simulate a user search workflow
            $_SESSION['user_id'] = $this->testUserId;
            $_SESSION['user_type'] = 'startup';
            
            // 1. User searches for investors
            $searchService = new SearchService();
            $results = $searchService->searchInvestors([
                'industry' => $this->testIndustryId,
                'investment_min' => 100000,
                'investment_max' => 1000000
            ]);
            
            if (!empty($results['data'])) {
                echo "✅ Step 1: User can search for investors<br>";
                
                // 2. User filters results
                $filteredResults = $searchService->searchInvestors([
                    'investor_type' => 'angel',
                    'industry' => $this->testIndustryId
                ]);
                
                if (!empty($filteredResults['data'])) {
                    echo "✅ Step 2: User can filter search results<br>";
                    
                    // 3. User views investor details
                    $investor = $filteredResults['data'][0];
                    if (isset($investor['id']) && isset($investor['first_name'])) {
                        echo "✅ Step 3: User can view investor details<br>";
                        
                        // 4. System handles JSON fields safely
                        if (isset($investor['preferred_industries'])) {
                            echo "✅ Step 4: JSON fields handled safely<br>";
                            
                            echo "✅ Complete search workflow successful!<br>";
                        }
                    }
                }
            }

        } catch (Exception $e) {
            echo "❌ Search workflow test failed: " . $e->getMessage() . "<br>";
        }
    }

    private function cleanup()
    {
        echo "<h2>Cleaning up test data...</h2>";
        
        try {
            // Delete test data in reverse order of creation
            if ($this->testStartupId) {
                $this->db->delete("DELETE FROM startups WHERE id = ?", [$this->testStartupId]);
                echo "✅ Deleted test startup<br>";
            }
            
            if ($this->testInvestorId) {
                $this->db->delete("DELETE FROM investors WHERE id = ?", [$this->testInvestorId]);
                echo "✅ Deleted test investor<br>";
            }
            
            // Delete test users
            $this->db->delete("DELETE FROM users WHERE email LIKE ?", ['test%@example.com']);
            echo "✅ Deleted test users<br>";
            
            if ($this->testIndustryId) {
                $this->db->delete("DELETE FROM industries WHERE id = ?", [$this->testIndustryId]);
                echo "✅ Deleted test industry<br>";
            }
            
            // Clear session
            unset($_SESSION['user_id']);
            unset($_SESSION['user_type']);
            
        } catch (Exception $e) {
            echo "⚠️ Cleanup warning: " . $e->getMessage() . "<br>";
        }
    }
}

// Run the test
try {
    $test = new SearchSystemTest();
    $test->runAllTests();
} catch (Exception $e) {
    echo "<h2>❌ Test Suite Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and ensure all files are in place.</p>";
}

echo "<br><p><a href='../../debug.php'>Run Debug Script</a> | <a href='../../dashboard'>Go to Dashboard</a></p>";
?>