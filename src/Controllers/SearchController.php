<?php
namespace Controllers;

use Exception;
use Core\Security;
use Models\User;
use Models\Startup;
use Models\Investor;
use Models\Industry;
use Services\SearchService;

class SearchController
{
    private $user;
    private $startup;
    private $investor;
    private $industry;
    private $searchService;
    private $security;

    public function __construct()
    {
        $this->user = new User();
        $this->startup = new Startup();
        $this->investor = new Investor();
        $this->industry = new Industry();
        $this->searchService = new SearchService();
        $this->security = Security::getInstance();
    }

    public function startups()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        // Get search parameters
        $filters = [
            'industry' => $_GET['industry'] ?? '',
            'stage' => $_GET['stage'] ?? '',
            'location' => $_GET['location'] ?? '',
            'funding_min' => $_GET['funding_min'] ?? '',
            'funding_max' => $_GET['funding_max'] ?? '',
            'funding_type' => $_GET['funding_type'] ?? '',
            'search' => $_GET['search'] ?? '',
            'page' => (int)($_GET['page'] ?? 1)
        ];

        try {
            // Get search results
            $results = $this->searchService->searchStartups($filters);
            $industries = $this->industry->getActiveIndustries();

            // Get current user info for context
            $currentUser = $this->user->find($_SESSION['user_id']);

            $this->render('search/startups', [
                'title' => 'Find Startups',
                'startups' => $results['data'],
                'pagination' => $results['pagination'],
                'industries' => $industries,
                'filters' => $filters,
                'currentUser' => $currentUser
            ]);
        } catch (Exception $e) {
            error_log("Search startups error: " . $e->getMessage());
            $this->render('search/startups', [
                'title' => 'Find Startups',
                'startups' => [],
                'pagination' => ['total' => 0, 'current_page' => 1, 'last_page' => 1],
                'industries' => $this->industry->getActiveIndustries(),
                'filters' => $filters,
                'currentUser' => $this->user->find($_SESSION['user_id']),
                'error' => 'Search temporarily unavailable. Please try again.'
            ]);
        }
    }

    public function investors()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        // Get search parameters with proper defaults
        $filters = [
            'industry' => $_GET['industry'] ?? '',
            'investor_type' => $_GET['investor_type'] ?? '',
            'location' => $_GET['location'] ?? '',
            'investment_min' => $_GET['investment_min'] ?? '',
            'investment_max' => $_GET['investment_max'] ?? '',
            'search' => $_GET['search'] ?? '',
            'page' => (int)($_GET['page'] ?? 1)
        ];

        try {
            // Get search results
            $results = $this->searchService->searchInvestors($filters);
            $industries = $this->industry->getActiveIndustries();

            // Get current user info for context
            $currentUser = $this->user->find($_SESSION['user_id']);

            // Process investors data to ensure JSON fields are properly decoded
            $processedInvestors = [];
            foreach ($results['data'] as $investor) {
                // Safely decode JSON fields
                $investor['preferred_industries'] = $this->safeJsonDecode($investor['preferred_industries'], []);
                $investor['investment_stages'] = $this->safeJsonDecode($investor['investment_stages'], []);
                $investor['portfolio_companies'] = $this->safeJsonDecode($investor['portfolio_companies'], []);
                $processedInvestors[] = $investor;
            }

            $this->render('search/investors', [
                'title' => 'Find Investors',
                'investors' => $processedInvestors,
                'pagination' => $results['pagination'],
                'industries' => $industries,
                'filters' => $filters,
                'currentUser' => $currentUser
            ]);
        } catch (Exception $e) {
            error_log("Search investors error: " . $e->getMessage());
            $this->render('search/investors', [
                'title' => 'Find Investors',
                'investors' => [],
                'pagination' => ['total' => 0, 'current_page' => 1, 'last_page' => 1],
                'industries' => $this->industry->getActiveIndustries(),
                'filters' => $filters,
                'currentUser' => $this->user->find($_SESSION['user_id']),
                'error' => 'Search temporarily unavailable. Please try again.'
            ]);
        }
    }

    public function filter()
    {
        // AJAX endpoint for live filtering
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->security->validateRequest();

        $searchType = $_POST['search_type'] ?? 'startups';
        $filters = $_POST['filters'] ?? [];

        try {
            if ($searchType === 'startups') {
                $results = $this->searchService->searchStartups($filters);
            } else {
                $results = $this->searchService->searchInvestors($filters);
                
                // Process investor data for JSON fields
                foreach ($results['data'] as &$investor) {
                    $investor['preferred_industries'] = $this->safeJsonDecode($investor['preferred_industries'], []);
                    $investor['investment_stages'] = $this->safeJsonDecode($investor['investment_stages'], []);
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $results['data'],
                'pagination' => $results['pagination']
            ]);
        } catch (Exception $e) {
            error_log("Filter error: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Search failed. Please try again.'
            ]);
        }
        exit;
    }

    public function suggestions()
    {
        // AJAX endpoint for search suggestions
        $query = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? 'startups';

        if (strlen($query) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        try {
            $suggestions = $this->searchService->getSearchSuggestions($query, $type);

            header('Content-Type: application/json');
            echo json_encode($suggestions);
        } catch (Exception $e) {
            error_log("Suggestions error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([]);
        }
        exit;
    }

    public function quickSearch()
    {
        // Quick search from dashboard
        $query = $_GET['q'] ?? '';
        $userType = $_SESSION['user_type'] ?? '';

        if ($userType === 'startup') {
            // Startup searching for investors
            header('Location: ' . url('search/investors?search=' . urlencode($query)));
        } else {
            // Investor searching for startups
            header('Location: ' . url('search/startups?search=' . urlencode($query)));
        }
        exit;
    }

    /**
     * Safely decode JSON with fallback
     */
    private function safeJsonDecode($json, $default = [])
    {
        if (empty($json) || $json === null) {
            return $default;
        }

        $decoded = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg() . " for data: " . $json);
            return $default;
        }
        
        return $decoded !== null ? $decoded : $default;
    }

    private function render($view, $data = [])
    {
        // Extract data for use in view
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<h1>View not found: {$view}</h1>";
            echo "<p>Expected file: {$viewFile}</p>";
            echo "<p><a href='" . url('dashboard') . "'>Return to Dashboard</a></p>";
        }

        // Get the content
        $content = ob_get_clean();

        // Include layout
        include __DIR__ . '/../Views/layouts/dashboard.php';
    }
}