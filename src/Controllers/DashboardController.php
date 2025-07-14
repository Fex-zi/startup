<?php

namespace Controllers;

use Models\User;
use Models\Startup;
use Models\Investor;
use Models\Industry;
use Models\MatchModel;
use Services\MatchingService;

class DashboardController
{
    private $user;
    private $startup;
    private $investor;
    private $industry;
    private $match;
    private $matchingService;

    public function __construct()
    {
        $this->user = new User();
        $this->startup = new Startup();
        $this->investor = new Investor();
        $this->industry = new Industry();
        $this->match = new MatchModel();
        $this->matchingService = new MatchingService();
    }

    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        // Get user with profile
        $user = $this->user->getUserWithProfile($userId);

        if (!$user) {
            header('Location: login');
            exit;
        }

        // If profile not completed, redirect to profile creation
        if (!$user['profile_completed']) {
            header('Location: profile/create');
            exit;
        }

        if ($userType === 'startup') {
            $this->showStartupDashboard($user);
        } else {
            $this->showInvestorDashboard($user);
        }
    }

    private function showStartupDashboard($user)
    {
        // Get startup profile
        $startup = $this->startup->findBy('user_id', $user['id']);
        
        if (!$startup) {
            header('Location: profile/create');
            exit;
        }

        // Get matches for this startup
        $matches = $this->match->getMatchesForStartup($startup['id']);
        
        // Get match statistics
        $matchStats = $this->match->getMatchStats($user['id'], 'startup');
        
        // Get recent investors
        $recentInvestors = $this->investor->getRecentInvestors(5);
        
        // Get industries
        $industries = $this->industry->getActiveIndustries();

        $this->render('dashboard/startup', [
            'title' => 'Startup Dashboard',
            'user' => $user,
            'startup' => $startup,
            'matches' => array_slice($matches, 0, 5), // Show top 5 matches
            'match_stats' => $matchStats,
            'recent_investors' => $recentInvestors,
            'industries' => $industries
        ]);
    }

    private function showInvestorDashboard($user)
    {
        // Get investor profile
        $investor = $this->investor->findBy('user_id', $user['id']);
        
        if (!$investor) {
            header('Location: profile/create');
            exit;
        }

        // Get matches for this investor
        $matches = $this->match->getMatchesForInvestor($investor['id']);
        
        // Get match statistics
        $matchStats = $this->match->getMatchStats($user['id'], 'investor');
        
        // Get recent startups
        $recentStartups = $this->startup->getRecentStartups(5);
        
        // Get industries
        $industries = $this->industry->getActiveIndustries();

        $this->render('dashboard/investor', [
            'title' => 'Investor Dashboard',
            'user' => $user,
            'investor' => $investor,
            'matches' => array_slice($matches, 0, 5), // Show top 5 matches
            'match_stats' => $matchStats,
            'recent_startups' => $recentStartups,
            'industries' => $industries
        ]);
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
        }

        // Get the content
        $content = ob_get_clean();

        // Include layout
        include __DIR__ . '/../Views/layouts/dashboard.php';
    }

    private function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login');
            exit;
        }
    }

    private function getCurrentUser()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return $this->user->find($_SESSION['user_id']);
    }
}
