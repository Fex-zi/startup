<?php

namespace Controllers;

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
    }

    public function investors()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        // Get search parameters
        $filters = [
            'industry' => $_GET['industry'] ?? '',
            'investor_type' => $_GET['investor_type'] ?? '',
            'location' => $_GET['location'] ?? '',
            'investment_min' => $_GET['investment_min'] ?? '',
            'investment_max' => $_GET['investment_max'] ?? '',
            'search' => $_GET['search'] ?? '',
            'page' => (int)($_GET['page'] ?? 1)
        ];

        // Get search results
        $results = $this->searchService->searchInvestors($filters);
        $industries = $this->industry->getActiveIndustries();

        // Get current user info for context
        $currentUser = $this->user->find($_SESSION['user_id']);

        $this->render('search/investors', [
            'title' => 'Find Investors',
            'investors' => $results['data'],
            'pagination' => $results['pagination'],
            'industries' => $industries,
            'filters' => $filters,
            'currentUser' => $currentUser
        ]);
    }

    public function filter()
    {
        // AJAX endpoint for live filtering
        $this->security->validateRequest();

        $searchType = $_POST['search_type'] ?? 'startups';
        $filters = $_POST['filters'] ?? [];

        if ($searchType === 'startups') {
            $results = $this->searchService->searchStartups($filters);
        } else {
            $results = $this->searchService->searchInvestors($filters);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $results['data'],
            'pagination' => $results['pagination']
        ]);
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

        $suggestions = $this->searchService->getSearchSuggestions($query, $type);

        header('Content-Type: application/json');
        echo json_encode($suggestions);
        exit;
    }

    public function quickSearch()
    {
        // Quick search from dashboard
        $query = $_GET['q'] ?? '';
        $userType = $_SESSION['user_type'] ?? '';

        if ($userType === 'startup') {
            // Startup searching for investors
            redirect('search/investors?search=' . urlencode($query));
        } else {
            // Investor searching for startups
            redirect('search/startups?search=' . urlencode($query));
        }
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
