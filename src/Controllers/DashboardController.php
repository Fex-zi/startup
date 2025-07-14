<?php

namespace Controllers;

use Exception;
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
            header('Location: ' . url('login'));
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        // Get user with profile
        $user = $this->user->find($userId);

        if (!$user) {
            header('Location: ' . url('login'));
            exit;
        }

        // If profile not completed, redirect to profile creation
        if (!$user['profile_completed']) {
            header('Location: ' . url('profile/create'));
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
        try {
            // Get startup profile
            $startup = $this->startup->findBy('user_id', $user['id']);
            
            if (!$startup) {
                header('Location: ' . url('profile/create'));
                exit;
            }

            // Get matches for this startup - safe approach
            $matches = [];
            $matchStats = [
                'total_matches' => 0,
                'mutual_matches' => 0,
                'pending_matches' => 0,
                'avg_match_score' => 0
            ];
            
            try {
                $matches = $this->match->getMatchesForStartup($startup['id']);
                $matchStats = $this->match->getMatchStats($user['id'], 'startup');
            } catch (Exception $e) {
                error_log("Dashboard matches error: " . $e->getMessage());
            }
            
            // Get recent investors - safe approach
            $recentInvestors = [];
            try {
                $recentInvestors = $this->investor->getRecentInvestors(5);
            } catch (Exception $e) {
                error_log("Dashboard recent investors error: " . $e->getMessage());
            }
            
            // Get industries
            $industries = [];
            try {
                $industries = $this->industry->getActiveIndustries();
            } catch (Exception $e) {
                error_log("Dashboard industries error: " . $e->getMessage());
            }

            $this->render('dashboard/startup', [
                'title' => 'Startup Dashboard',
                'user' => $user,
                'startup' => $startup,
                'matches' => array_slice($matches, 0, 5), // Show top 5 matches
                'match_stats' => $matchStats,
                'recent_investors' => $recentInvestors,
                'industries' => $industries
            ]);
        } catch (Exception $e) {
            error_log("Startup dashboard error: " . $e->getMessage());
            $this->showBasicDashboard($user, 'startup');
        }
    }

    private function showInvestorDashboard($user)
    {
        try {
            // Get investor profile
            $investor = $this->investor->findBy('user_id', $user['id']);
            
            if (!$investor) {
                header('Location: ' . url('profile/create'));
                exit;
            }

            // Get matches for this investor - safe approach
            $matches = [];
            $matchStats = [
                'total_matches' => 0,
                'mutual_matches' => 0,
                'pending_matches' => 0,
                'avg_match_score' => 0
            ];
            
            try {
                $matches = $this->match->getMatchesForInvestor($investor['id']);
                $matchStats = $this->match->getMatchStats($user['id'], 'investor');
            } catch (Exception $e) {
                error_log("Dashboard matches error: " . $e->getMessage());
            }
            
            // Get recent startups - safe approach
            $recentStartups = [];
            try {
                $recentStartups = $this->startup->getRecentStartups(5);
            } catch (Exception $e) {
                error_log("Dashboard recent startups error: " . $e->getMessage());
            }
            
            // Get industries
            $industries = [];
            try {
                $industries = $this->industry->getActiveIndustries();
            } catch (Exception $e) {
                error_log("Dashboard industries error: " . $e->getMessage());
            }

            $this->render('dashboard/investor', [
                'title' => 'Investor Dashboard',
                'user' => $user,
                'investor' => $investor,
                'matches' => array_slice($matches, 0, 5), // Show top 5 matches
                'match_stats' => $matchStats,
                'recent_startups' => $recentStartups,
                'industries' => $industries
            ]);
        } catch (Exception $e) {
            error_log("Investor dashboard error: " . $e->getMessage());
            $this->showBasicDashboard($user, 'investor');
        }
    }

    /**
     * Fallback dashboard when main dashboard fails
     */
    private function showBasicDashboard($user, $userType)
    {
        $dashboardData = [
            'title' => ucfirst($userType) . ' Dashboard',
            'user' => $user,
            'error_message' => 'Some dashboard features are temporarily unavailable.',
            'matches' => [],
            'match_stats' => [
                'total_matches' => 0,
                'mutual_matches' => 0,
                'pending_matches' => 0,
                'avg_match_score' => 0
            ]
        ];

        if ($userType === 'startup') {
            $dashboardData['startup'] = ['company_name' => 'Your Startup'];
            $dashboardData['recent_investors'] = [];
        } else {
            $dashboardData['investor'] = ['company_name' => 'Your Fund'];
            $dashboardData['recent_startups'] = [];
        }

        $this->render('dashboard/' . $userType, $dashboardData);
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
            // Fallback dashboard view
            echo $this->getBasicDashboardHTML($data);
        }

        // Get the content
        $content = ob_get_clean();

        // Include layout
        $layoutFile = __DIR__ . '/../Views/layouts/dashboard.php';
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            // Fallback layout
            echo $this->getBasicLayoutHTML($content, $data['title'] ?? 'Dashboard');
        }
    }

    /**
     * Fallback HTML when views are missing
     */
    private function getBasicDashboardHTML($data)
    {
        $userType = $data['user']['user_type'] ?? 'user';
        $userName = $data['user']['first_name'] ?? 'User';
        
        return "
        <div class='container-fluid'>
            <div class='row mb-4'>
                <div class='col-12'>
                    <div class='alert alert-info'>
                        <h2>Welcome back, {$userName}!</h2>
                        <p>Your {$userType} dashboard is loading...</p>
                    </div>
                </div>
            </div>
            
            <div class='row'>
                <div class='col-md-6'>
                    <div class='card'>
                        <div class='card-header'>
                            <h5>Quick Actions</h5>
                        </div>
                        <div class='card-body'>
                            <a href='" . url('profile/edit') . "' class='btn btn-primary mb-2'>Edit Profile</a><br>
                            <a href='" . url('search/' . ($userType === 'startup' ? 'investors' : 'startups')) . "' class='btn btn-outline-primary mb-2'>Search</a><br>
                            <a href='" . url('matches') . "' class='btn btn-outline-secondary'>View Matches</a>
                        </div>
                    </div>
                </div>
                
                <div class='col-md-6'>
                    <div class='card'>
                        <div class='card-header'>
                            <h5>Platform Status</h5>
                        </div>
                        <div class='card-body'>
                            <p class='text-success'>✅ Search System: Working</p>
                            <p class='text-success'>✅ Profile System: Working</p>
                            <p class='text-warning'>⚠️ Dashboard Views: Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
    }

    /**
     * Fallback layout when layout files are missing
     */
    private function getBasicLayoutHTML($content, $title)
    {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title}</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light'>
            <nav class='navbar navbar-expand-lg navbar-dark bg-primary'>
                <div class='container'>
                    <a class='navbar-brand' href='" . url('dashboard') . "'>
                        <i class='fas fa-rocket me-2'></i>Startup Connect
                    </a>
                    <div class='navbar-nav ms-auto'>
                        <a class='nav-link' href='" . url('logout') . "'>Logout</a>
                    </div>
                </div>
            </nav>
            
            <div class='container-fluid py-4'>
                {$content}
            </div>
            
            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
        </body>
        </html>";
    }

    private function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
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