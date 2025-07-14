<?php

namespace Controllers;

use Core\Security;
use Models\User;
use Models\Startup;
use Models\Investor;
use Models\MatchModel;
use Services\MatchingService;

class MatchingController
{
    private $user;
    private $startup;
    private $investor;
    private $match;
    private $matchingService;
    private $security;

    public function __construct()
    {
        $this->user = new User();
        $this->startup = new Startup();
        $this->investor = new Investor();
        $this->match = new MatchModel();
        $this->matchingService = new MatchingService();
        $this->security = Security::getInstance();
    }

    /**
     * Display all matches for the current user
     */
    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        // Get user profile
        $user = $this->user->find($userId);
        if (!$user) {
            header('Location: ' . url('login'));
            exit;
        }

        if ($userType === 'startup') {
            $this->showStartupMatches($user);
        } else {
            $this->showInvestorMatches($user);
        }
    }

    /**
     * Display matches for startup users
     */
    private function showStartupMatches($user)
    {
        $startup = $this->startup->findBy('user_id', $user['id']);
        if (!$startup) {
            header('Location: ' . url('profile/create'));
            exit;
        }

        // Get all matches for this startup
        $allMatches = $this->match->getMatchesForStartup($startup['id']);
        $mutualMatches = $this->match->getMatchesForStartup($startup['id'], 'mutual_interest');
        $pendingMatches = $this->match->getMatchesForStartup($startup['id'], 'pending');

        // Get match statistics
        $stats = $this->match->getMatchStats($user['id'], 'startup');

        $this->render('matching/startup_matches', [
            'title' => 'Your Investor Matches',
            'user' => $user,
            'startup' => $startup,
            'all_matches' => $allMatches,
            'mutual_matches' => $mutualMatches,
            'pending_matches' => $pendingMatches,
            'stats' => $stats
        ]);
    }

    /**
     * Display matches for investor users
     */
    private function showInvestorMatches($user)
    {
        $investor = $this->investor->findBy('user_id', $user['id']);
        if (!$investor) {
            header('Location: ' . url('profile/create'));
            exit;
        }

        // Get all matches for this investor
        $allMatches = $this->match->getMatchesForInvestor($investor['id']);
        $mutualMatches = $this->match->getMatchesForInvestor($investor['id'], 'mutual_interest');
        $pendingMatches = $this->match->getMatchesForInvestor($investor['id'], 'pending');

        // Get match statistics
        $stats = $this->match->getMatchStats($user['id'], 'investor');

        $this->render('matches/investor', [
            'title' => 'Your Startup Matches',
            'user' => $user,
            'investor' => $investor,
            'all_matches' => $allMatches,
            'mutual_matches' => $mutualMatches,
            'pending_matches' => $pendingMatches,
            'stats' => $stats
        ]);
    }

    /**
     * API endpoint to find new matches
     */
    public function findMatches()
    {
        // Validate CSRF and login
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->security->validateRequest();

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        try {
            if ($userType === 'startup') {
                $startup = $this->startup->findBy('user_id', $userId);
                if (!$startup) {
                    throw new \Exception('Startup profile not found');
                }

                $newMatches = $this->matchingService->generateMatchesForStartup($startup['id']);
                $message = count($newMatches) . ' new matches found!';
            } else {
                $investor = $this->investor->findBy('user_id', $userId);
                if (!$investor) {
                    throw new \Exception('Investor profile not found');
                }

                $newMatches = $this->matchingService->generateMatchesForInvestor($investor['id']);
                $message = count($newMatches) . ' new matches found!';
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $message,
                'new_matches' => count($newMatches)
            ]);

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Express interest in a match
     */
    public function expressInterest()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->security->validateRequest();

        $matchId = $_POST['match_id'] ?? null;
        $interested = $_POST['interested'] ?? false;
        $userType = $_SESSION['user_type'];

        if (!$matchId) {
            http_response_code(400);
            echo json_encode(['error' => 'Match ID required']);
            exit;
        }

        try {
            $result = $this->match->recordInterest($matchId, $userType, $interested);
            
            if ($result) {
                // Get updated match status
                $updatedMatch = $this->match->find($matchId);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => $interested ? 'Interest recorded!' : 'Declined successfully',
                    'status' => $updatedMatch['status'] ?? 'pending'
                ]);
            } else {
                throw new \Exception('Failed to record interest');
            }

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * View detailed match information
     */
    public function viewMatch($matchId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        $match = $this->match->getMatchWithDetails($matchId);
        if (!$match) {
            header('Location: ' . url('matches'));
            exit;
        }

        // Verify user has access to this match
        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        $hasAccess = false;
        if ($userType === 'startup') {
            $startup = $this->startup->findBy('user_id', $userId);
            $hasAccess = $startup && $startup['id'] == $match['startup_id'];
        } else {
            $investor = $this->investor->findBy('user_id', $userId);
            $hasAccess = $investor && $investor['id'] == $match['investor_id'];
        }

        if (!$hasAccess) {
            header('Location: ' . url('matches'));
            exit;
        }

        $this->render('matches/detail', [
            'title' => 'Match Details',
            'match' => $match,
            'user_type' => $userType
        ]);
    }

    /**
     * Get match recommendations
     */
    public function recommendations()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        if ($userType === 'startup') {
            $startup = $this->startup->findBy('user_id', $userId);
            if (!$startup) {
                header('Location: ' . url('profile/create'));
                exit;
            }

            $recommendations = $this->matchingService->findMatchesForStartup($startup['id']);
        } else {
            $investor = $this->investor->findBy('user_id', $userId);
            if (!$investor) {
                header('Location: ' . url('profile/create'));
                exit;
            }

            $recommendations = $this->matchingService->findMatchesForInvestor($investor['id']);
        }

        $this->render('matches/recommendations', [
            'title' => 'Recommended Matches',
            'recommendations' => $recommendations,
            'user_type' => $userType
        ]);
    }

    /**
     * Bulk operations on matches
     */
    public function bulkAction()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->security->validateRequest();

        $action = $_POST['action'] ?? '';
        $matchIds = $_POST['match_ids'] ?? [];
        $userType = $_SESSION['user_type'];

        if (empty($matchIds) || !is_array($matchIds)) {
            http_response_code(400);
            echo json_encode(['error' => 'Match IDs required']);
            exit;
        }

        try {
            $successCount = 0;
            foreach ($matchIds as $matchId) {
                switch ($action) {
                    case 'express_interest':
                        $result = $this->match->recordInterest($matchId, $userType, true);
                        break;
                    case 'decline':
                        $result = $this->match->recordInterest($matchId, $userType, false);
                        break;
                    default:
                        throw new \Exception('Invalid action');
                }
                
                if ($result) {
                    $successCount++;
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => "Successfully processed {$successCount} matches",
                'processed' => $successCount
            ]);

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Render view with layout
     */
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
            echo "<p><a href='" . url('matches') . "'>Return to Matches</a></p>";
        }

        // Get the content
        $content = ob_get_clean();

        // Include layout
        include __DIR__ . '/../Views/layouts/dashboard.php';
    }
}