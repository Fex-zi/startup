<?php

namespace Controllers;

use Core\Security;
use Models\User;
use Models\Startup;
use Models\Investor;
use Models\Industry;

class ProfileController
{
    private $user;
    private $startup;
    private $investor;
    private $industry;
    private $security;

    public function __construct()
    {
        $this->user = new User();
        $this->startup = new Startup();
        $this->investor = new Investor();
        $this->industry = new Industry();
        $this->security = Security::getInstance();
    }

    public function create()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        // Get user data
        $user = $this->user->find($userId);
        if (!$user) {
            header('Location: login');
            exit;
        }

        // If profile already completed, redirect to dashboard
        if ($user['profile_completed']) {
            header('Location: ' . url('dashboard'));
            exit;
        }

        // Get industries for dropdown
        $industries = $this->industry->getActiveIndustries();

        if ($userType === 'startup') {
            $this->render('profiles/startup/create', [
                'title' => 'Create Startup Profile',
                'user' => $user,
                'industries' => $industries,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
        } else {
            $this->render('profiles/investor/create', [
                'title' => 'Create Investor Profile',
                'user' => $user,
                'industries' => $industries,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
        }
    }

    public function store()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: login');
            exit;
        }

        $this->security->validateRequest();

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        if ($userType === 'startup') {
            $this->storeStartupProfile($userId);
        } else {
            $this->storeInvestorProfile($userId);
        }
    }

    private function storeStartupProfile($userId)
    {
        $data = [
            'user_id' => $userId,
            'company_name' => $this->security->sanitizeInput($_POST['company_name']),
            'description' => $this->security->sanitizeInput($_POST['description']),
            'industry_id' => (int)($_POST['industry_id'] ?? 0),
            'stage' => $_POST['stage'] ?? '',
            'employee_count' => $_POST['employee_count'] ?? '',
            'website' => $this->security->sanitizeInput($_POST['website'], 'url'),
            'funding_goal' => (float)($_POST['funding_goal'] ?? 0),
            'funding_type' => $_POST['funding_type'] ?? '',
            'location' => $this->security->sanitizeInput($_POST['location'])
        ];

        // Generate slug from company name
        $data['slug'] = $this->generateSlug($data['company_name']);

        // Validate input
        $errors = $this->security->validateInput($data, [
            'company_name' => ['required', 'max:255'],
            'description' => ['required'],
            'industry_id' => ['required', 'numeric'],
            'stage' => ['required'],
            'funding_goal' => ['required', 'numeric'],
            'funding_type' => ['required'],
            'location' => ['required']
        ]);

        if (!empty($errors)) {
            $industries = $this->industry->getActiveIndustries();
            $user = $this->user->find($userId);
            
            $this->render('profiles/startup/create', [
                'title' => 'Create Startup Profile',
                'user' => $user,
                'industries' => $industries,
                'errors' => $errors,
                'old_input' => $data,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
            return;
        }

        try {
            // Create startup profile
            $startupId = $this->startup->create($data);

            if ($startupId) {
                // Mark profile as completed
                $this->user->markProfileCompleted($userId);

                // Redirect to dashboard
                header('Location: ' . url('dashboard'));
                exit;
            } else {
                throw new \Exception('Failed to create startup profile');
            }
        } catch (\Exception $e) {
            $industries = $this->industry->getActiveIndustries();
            $user = $this->user->find($userId);
            
            $this->render('profiles/startup/create', [
                'title' => 'Create Startup Profile',
                'user' => $user,
                'industries' => $industries,
                'errors' => ['general' => ['Profile creation failed. Please try again.']],
                'old_input' => $data,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
        }
    }

    private function storeInvestorProfile($userId)
    {
        $data = [
            'user_id' => $userId,
            'investor_type' => $_POST['investor_type'] ?? '',
            'company_name' => $this->security->sanitizeInput($_POST['company_name']),
            'bio' => $this->security->sanitizeInput($_POST['bio']),
            'preferred_industries' => json_encode($_POST['preferred_industries'] ?? []),
            'investment_stages' => json_encode($_POST['investment_stages'] ?? []),
            'investment_range_min' => (float)($_POST['investment_range_min'] ?? 0),
            'investment_range_max' => (float)($_POST['investment_range_max'] ?? 0),
            'location' => $this->security->sanitizeInput($_POST['location']),
            'linkedin_url' => $this->security->sanitizeInput($_POST['linkedin_url'], 'url'),
            'website' => $this->security->sanitizeInput($_POST['website'], 'url')
        ];

        // Validate input
        $errors = $this->security->validateInput($data, [
            'investor_type' => ['required'],
            'bio' => ['required'],
            'investment_range_min' => ['required', 'numeric'],
            'investment_range_max' => ['required', 'numeric'],
            'location' => ['required']
        ]);

        if (!empty($errors)) {
            $industries = $this->industry->getActiveIndustries();
            $user = $this->user->find($userId);
            
            $this->render('profiles/investor/create', [
                'title' => 'Create Investor Profile',
                'user' => $user,
                'industries' => $industries,
                'errors' => $errors,
                'old_input' => $data,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
            return;
        }

        try {
            // Create investor profile
            $investorId = $this->investor->create($data);

            if ($investorId) {
                // Mark profile as completed
                $this->user->markProfileCompleted($userId);

                // Redirect to dashboard
                header('Location: ' . url('dashboard'));
                exit;
            } else {
                throw new \Exception('Failed to create investor profile');
            }
        } catch (\Exception $e) {
            $industries = $this->industry->getActiveIndustries();
            $user = $this->user->find($userId);
            
            $this->render('profiles/investor/create', [
                'title' => 'Create Investor Profile',
                'user' => $user,
                'industries' => $industries,
                'errors' => ['general' => ['Profile creation failed. Please try again.']],
                'old_input' => $data,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
        }
    }

    public function edit()
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

        // Get industries for dropdown
        $industries = $this->industry->getActiveIndustries();

        if ($userType === 'startup') {
            $startup = $this->startup->findBy('user_id', $userId);
            $this->render('profiles/startup/edit', [
                'title' => 'Edit Startup Profile',
                'user' => $user,
                'startup' => $startup,
                'industries' => $industries,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
        } else {
            $investor = $this->investor->findBy('user_id', $userId);
            $this->render('profiles/investor/edit', [
                'title' => 'Edit Investor Profile',
                'user' => $user,
                'investor' => $investor,
                'industries' => $industries,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
        }
    }

    public function update()
    {
        // Similar to store but for updating existing profiles
        // Implementation would be similar to store methods
        header('Location: ' . url('dashboard'));
        exit;
    }

    public function view($id)
    {
        // View public profile
        $user = $this->user->find($id);
        if (!$user) {
            header('Location: ' . url('dashboard'));
            exit;
        }

        if ($user['user_type'] === 'startup') {
            $startup = $this->startup->findBy('user_id', $id);
            $this->render('profiles/startup/public', [
                'title' => $startup['company_name'] ?? 'Startup Profile',
                'user' => $user,
                'startup' => $startup
            ]);
        } else {
            $investor = $this->investor->findBy('user_id', $id);
            $this->render('profiles/investor/public', [
                'title' => ($investor['company_name'] ?? $user['first_name'] . ' ' . $user['last_name']),
                'user' => $user,
                'investor' => $investor
            ]);
        }
    }

    private function generateSlug($text)
    {
        // Convert to lowercase and replace spaces with hyphens
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while ($this->startup->findBy('slug', $slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
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
            echo "<p><a href='dashboard'>Return to Dashboard</a></p>";
        }

        // Get the content
        $content = ob_get_clean();

        // Include layout
        include __DIR__ . '/../Views/layouts/dashboard.php';
    }
}
