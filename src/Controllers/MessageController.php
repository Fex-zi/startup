<?php
namespace Controllers;

use Models\User;

class MessageController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Get unread message count for the current user
     */
    public function getUnreadCount()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];

        try {
            // TODO: Replace with actual message counting when messaging system is implemented
            // For now, return 0 as placeholder
            $unreadCount = 0;
            
            // In the future, this would be something like:
            // $unreadCount = $this->message->getUnreadCountForUser($userId);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'unread_count' => $unreadCount
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
     * Show messages index page
     */
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        // TODO: Implement full messaging interface
        $this->render('messages/index', [
            'title' => 'Messages',
            'messages' => [], // Placeholder
            'conversations' => [] // Placeholder
        ]);
    }

    /**
     * Show specific conversation
     */
    public function conversation($conversationId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        // TODO: Implement conversation view
        $this->render('messages/conversation', [
            'title' => 'Conversation',
            'conversation_id' => $conversationId,
            'messages' => [] // Placeholder
        ]);
    }

    /**
     * Send a message
     */
    public function send()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // TODO: Implement message sending
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Messaging system not yet implemented'
        ]);
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
            echo "<p>Messaging system will be implemented in a future phase.</p>";
            echo "<p><a href='" . url('dashboard') . "'>Return to Dashboard</a></p>";
        }

        // Get the content
        $content = ob_get_clean();

        // Include layout
        include __DIR__ . '/../Views/layouts/dashboard.php';
    }
}