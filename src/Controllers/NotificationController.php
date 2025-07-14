<?php
namespace Controllers;

use Models\User;
use Models\MatchModel;

class NotificationController
{
    private $user;
    private $match;

    public function __construct()
    {
        $this->user = new User();
        $this->match = new MatchModel();
    }

    /**
     * Get notification counts for the current user
     */
    public function getCounts()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        try {
            // Get match statistics
            $matchStats = $this->match->getMatchStats($userId, $userType);
            
            // Get new matches count (last 24 hours)
            $newMatchesCount = $this->getNewMatchesCount($userId, $userType);
            
            // Get unread messages count (placeholder - will be implemented with messaging system)
            $unreadMessages = 0; // TODO: Implement when messaging system is built
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'total_matches' => $matchStats['total_matches'] ?? 0,
                'mutual_matches' => $matchStats['mutual_matches'] ?? 0,
                'pending_matches' => $matchStats['pending_matches'] ?? 0,
                'new_matches' => $newMatchesCount,
                'unread_messages' => $unreadMessages,
                'notifications' => [
                    'matches' => $newMatchesCount > 0,
                    'messages' => $unreadMessages > 0
                ]
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Get count of new matches in last 24 hours
     */
    private function getNewMatchesCount($userId, $userType)
    {
        if ($userType === 'startup') {
            $sql = "
                SELECT COUNT(*) as count 
                FROM matches m 
                JOIN startups s ON m.startup_id = s.id 
                WHERE s.user_id = ? 
                AND m.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                AND m.status = 'pending'
            ";
        } else {
            $sql = "
                SELECT COUNT(*) as count 
                FROM matches m 
                JOIN investors i ON m.investor_id = i.id 
                WHERE i.user_id = ? 
                AND m.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                AND m.status = 'pending'
            ";
        }
        
        $result = $this->match->db->fetch($sql, [$userId]);
        return $result['count'] ?? 0;
    }
}