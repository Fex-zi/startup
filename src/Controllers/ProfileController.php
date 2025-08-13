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

    // FIXED: Use simple directory structure that actually exists
    private const UPLOAD_BASE_DIR = '/assets/uploads/';
    private const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'webp'];
    private const ALLOWED_DOCUMENT_TYPES = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
    private const MAX_IMAGE_SIZE = 2 * 1024 * 1024; // 2MB
    private const MAX_DOCUMENT_SIZE = 10 * 1024 * 1024; // 10MB

    public function __construct()
    {
        $this->user = new User();
        $this->startup = new Startup();
        $this->investor = new Investor();
        $this->industry = new Industry();
        $this->security = Security::getInstance();
        
        // Ensure upload directories exist
        $this->initializeUploadDirectories();
    }

    /**
     * FIXED: Create simple directory structure that matches working setup
     */
    private function initializeUploadDirectories()
    {
        $uploadRoot = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_BASE_DIR;
        
        // Simple directory structure that matches the diagnostic results
        $directories = [
            'logos',      // For company logos
            'profiles',   // For profile pictures  
            'documents'   // For all documents (pitch decks, business plans, etc.)
        ];

        foreach ($directories as $dir) {
            $fullPath = $uploadRoot . $dir;
            if (!file_exists($fullPath)) {
                if (!mkdir($fullPath, 0755, true)) {
                    error_log("Failed to create upload directory: " . $fullPath);
                }
            }
        }
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];
        $user = $this->user->find($userId);
        
        if (!$user) {
            header('Location: ' . url('login'));
            exit;
        }

        if ($user['profile_completed']) {
            header('Location: ' . url('dashboard'));
            exit;
        }

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
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        $this->security->validateRequest();

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        try {
            // Handle file uploads first
            $uploadedFiles = $this->handleFileUploads();

            if ($userType === 'startup') {
                $this->createStartupProfile($userId, $uploadedFiles);
            } else {
                $this->createInvestorProfile($userId, $uploadedFiles);
            }
        } catch (\Exception $e) {
            $_SESSION['toast_message'] = 'Upload failed: ' . $e->getMessage();
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('profile/create'));
            exit;
        }
    }

    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        $user = $this->user->getUserWithProfile($userId);
        if (!$user) {
            header('Location: ' . url('login'));
            exit;
        }

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
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        $this->security->validateRequest();

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        try {
            // Handle file uploads first
            $uploadedFiles = $this->handleFileUploads();

            if ($userType === 'startup') {
                $this->updateStartupProfile($userId, $uploadedFiles);
            } else {
                $this->updateInvestorProfile($userId, $uploadedFiles);
            }
        } catch (\Exception $e) {
            $_SESSION['toast_message'] = 'Upload failed: ' . $e->getMessage();
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('profile/edit'));
            exit;
        }
    }

    public function view($id)
    {
        $user = $this->user->getUserWithProfile($id);
        if (!$user) {
            header('Location: ' . url('dashboard'));
            exit;
        }

        if ($user['user_type'] === 'startup') {
            $startup = $this->startup->findBy('user_id', $id);
            $industry = $this->industry->find($startup['industry_id']);
            
            $this->render('profiles/startup/view', [
                'title' => $startup['company_name'] . ' - Startup Profile',
                'user' => $user,
                'startup' => $startup,
                'industry' => $industry
            ]);
        } else {
            $investor = $this->investor->findBy('user_id', $id);
            
            $this->render('profiles/investor/view', [
                'title' => $user['first_name'] . ' ' . $user['last_name'] . ' - Investor Profile',
                'user' => $user,
                'investor' => $investor
            ]);
        }
    }

    /**
     * FIXED: Simple file upload handling that works with existing directories
     */
    private function handleFileUploads()
    {
        $uploadedFiles = [];

        // FIXED: Use simple file mapping that matches existing directories
        $fileMapping = [
            'logo' => [
                'dir' => 'logos',
                'types' => self::ALLOWED_IMAGE_TYPES,
                'size' => self::MAX_IMAGE_SIZE
            ],
            'profile_picture' => [
                'dir' => 'profiles', 
                'types' => self::ALLOWED_IMAGE_TYPES,
                'size' => self::MAX_IMAGE_SIZE
            ],
            'pitch_deck' => [
                'dir' => 'documents',
                'types' => self::ALLOWED_DOCUMENT_TYPES,
                'size' => self::MAX_DOCUMENT_SIZE
            ],
            'business_plan' => [
                'dir' => 'documents',
                'types' => self::ALLOWED_DOCUMENT_TYPES,
                'size' => self::MAX_DOCUMENT_SIZE
            ],
            'financial_document' => [
                'dir' => 'documents',
                'types' => self::ALLOWED_DOCUMENT_TYPES,
                'size' => self::MAX_DOCUMENT_SIZE
            ]
        ];

        foreach ($fileMapping as $field => $config) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                try {
                    $uploadedFiles[$field] = $this->processFileUpload(
                        $_FILES[$field], 
                        $config['dir'], 
                        $config['types'], 
                        $config['size']
                    );
                    
                    // Log successful upload for debugging
                    error_log("Successfully uploaded file: " . $field . " to " . $uploadedFiles[$field]);
                    
                } catch (\Exception $e) {
                    error_log("File upload failed for " . $field . ": " . $e->getMessage());
                    throw $e;
                }
            }
        }

        return $uploadedFiles;
    }

    /**
     * FIXED: File upload processing that actually saves to the file system
     */
    private function processFileUpload($file, $directory, $allowedExtensions, $maxSize)
    {
        // FIXED: Use simple path structure
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_BASE_DIR . $directory . '/';
        
        // Ensure directory exists
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \Exception('Cannot create upload directory: ' . $uploadDir);
            }
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            throw new \Exception('File size exceeds maximum allowed size of ' . ($maxSize / 1024 / 1024) . 'MB');
        }

        // Validate file type by extension
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new \Exception('Invalid file type. Allowed: ' . implode(', ', $allowedExtensions));
        }

        // Additional MIME type validation for security
        $this->validateMimeType($file['tmp_name'], $fileExtension);

        // Generate secure filename
        $filename = $this->generateSecureFilename($file['name']);
        $filePath = $uploadDir . $filename;

        // FIXED: Actually move the uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Return relative web path for database storage
            $relativePath = self::UPLOAD_BASE_DIR . $directory . '/' . $filename;
            error_log("File successfully moved to: " . $filePath . " (relative: " . $relativePath . ")");
            return $relativePath;
        } else {
            throw new \Exception('Failed to move uploaded file to: ' . $filePath);
        }
    }

    /**
     * MIME type validation for additional security
     */
    private function validateMimeType($filePath, $extension)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        $allowedMimes = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'webp' => ['image/webp'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'ppt' => ['application/vnd.ms-powerpoint'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation']
        ];

        if (!in_array($mimeType, $allowedMimes[$extension] ?? [])) {
            throw new \Exception('File content does not match extension. Expected: ' . implode(', ', $allowedMimes[$extension] ?? []) . ', Got: ' . $mimeType);
        }
    }

    /**
     * Generate secure filename
     */
    private function generateSecureFilename($originalName)
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $safeName = substr($safeName, 0, 50); // Limit length
        
        return uniqid($safeName . '_', true) . '.' . $extension;
    }

    private function createStartupProfile($userId, $uploadedFiles)
    {
        $data = [
            'user_id' => $userId,
            'company_name' => $this->security->sanitizeInput($_POST['company_name']),
            'description' => $this->security->sanitizeInput($_POST['description']),
            'industry_id' => (int)($_POST['industry_id'] ?? 0),
            'stage' => $_POST['stage'] ?? '',
            'employee_count' => $_POST['employee_count'] ?? '',
            'location' => $this->security->sanitizeInput($_POST['location']),
            'founding_date' => $_POST['founding_date'] ?? null,
            'website_url' => $this->security->sanitizeInput($_POST['website_url']),
            'funding_goal' => (float)($_POST['funding_goal'] ?? 0),
            'funding_type' => $_POST['funding_type'] ?? '',
            'min_investment' => (float)($_POST['min_investment'] ?? 0),
            'max_investment' => (float)($_POST['max_investment'] ?? 0),
            'equity_offered' => (float)($_POST['equity_offered'] ?? 0),
        ];

        // FIXED: Handle file uploads properly
        if (isset($uploadedFiles['logo'])) {
            $data['logo_url'] = $uploadedFiles['logo'];
        }
        if (isset($uploadedFiles['pitch_deck'])) {
            $data['pitch_deck_url'] = $uploadedFiles['pitch_deck'];
        }
        if (isset($uploadedFiles['business_plan'])) {
            $data['business_plan_url'] = $uploadedFiles['business_plan'];
        }

        $this->validateAndStoreProfile('startup', $userId, $data);
    }

    private function createInvestorProfile($userId, $uploadedFiles)
    {
        $data = [
            'user_id' => $userId,
            'investor_type' => $_POST['investor_type'] ?? '',
            'bio' => $this->security->sanitizeInput($_POST['bio']),
            'location' => $this->security->sanitizeInput($_POST['location']),
            'min_investment' => (float)($_POST['min_investment'] ?? 0),
            'max_investment' => (float)($_POST['max_investment'] ?? 0),
            'preferred_stages' => $_POST['preferred_stages'] ?? '',
            'portfolio_size' => $_POST['portfolio_size'] ?? '',
            'investment_philosophy' => $this->security->sanitizeInput($_POST['investment_philosophy'])
        ];

        // FIXED: Handle file uploads properly
        if (isset($uploadedFiles['profile_picture'])) {
            $data['profile_picture_url'] = $uploadedFiles['profile_picture'];
        }

        $this->validateAndStoreProfile('investor', $userId, $data);
    }

    private function updateStartupProfile($userId, $uploadedFiles)
    {
        $existingStartup = $this->startup->findBy('user_id', $userId);
        if (!$existingStartup) {
            header('Location: ' . url('profile/create'));
            exit;
        }

        $data = [
            'company_name' => $this->security->sanitizeInput($_POST['company_name']),
            'description' => $this->security->sanitizeInput($_POST['description']),
            'industry_id' => (int)($_POST['industry_id'] ?? 0),
            'stage' => $_POST['stage'] ?? '',
            'employee_count' => $_POST['employee_count'] ?? '',
            'location' => $this->security->sanitizeInput($_POST['location']),
            'founding_date' => $_POST['founding_date'] ?? null,
            'website_url' => $this->security->sanitizeInput($_POST['website_url']),
            'funding_goal' => (float)($_POST['funding_goal'] ?? 0),
            'funding_type' => $_POST['funding_type'] ?? '',
            'min_investment' => (float)($_POST['min_investment'] ?? 0),
            'max_investment' => (float)($_POST['max_investment'] ?? 0),
            'equity_offered' => (float)($_POST['equity_offered'] ?? 0),
        ];

        // FIXED: Handle file uploads and cleanup
        if (isset($uploadedFiles['logo'])) {
            $data['logo_url'] = $uploadedFiles['logo'];
            $this->deleteOldFile($existingStartup['logo_url'] ?? '');
        }
        if (isset($uploadedFiles['pitch_deck'])) {
            $data['pitch_deck_url'] = $uploadedFiles['pitch_deck'];
            $this->deleteOldFile($existingStartup['pitch_deck_url'] ?? '');
        }
        if (isset($uploadedFiles['business_plan'])) {
            $data['business_plan_url'] = $uploadedFiles['business_plan'];
            $this->deleteOldFile($existingStartup['business_plan_url'] ?? '');
        }

        $this->validateAndUpdateProfile('startup', $userId, $existingStartup, $data, $uploadedFiles);
    }

    private function updateInvestorProfile($userId, $uploadedFiles)
    {
        $existingInvestor = $this->investor->findBy('user_id', $userId);
        if (!$existingInvestor) {
            header('Location: ' . url('profile/create'));
            exit;
        }

        $data = [
            'investor_type' => $_POST['investor_type'] ?? '',
            'bio' => $this->security->sanitizeInput($_POST['bio']),
            'location' => $this->security->sanitizeInput($_POST['location']),
            'min_investment' => (float)($_POST['min_investment'] ?? 0),
            'max_investment' => (float)($_POST['max_investment'] ?? 0),
            'preferred_stages' => $_POST['preferred_stages'] ?? '',
            'portfolio_size' => $_POST['portfolio_size'] ?? '',
            'investment_philosophy' => $this->security->sanitizeInput($_POST['investment_philosophy'])
        ];

        // FIXED: Handle file uploads and cleanup
        if (isset($uploadedFiles['profile_picture'])) {
            $data['profile_picture_url'] = $uploadedFiles['profile_picture'];
            $this->deleteOldFile($existingInvestor['profile_picture_url'] ?? '');
        }

        $this->validateAndUpdateProfile('investor', $userId, $existingInvestor, $data, $uploadedFiles);
    }

    private function validateAndStoreProfile($type, $userId, $data)
    {
        $validationRules = $type === 'startup' ? [
            'company_name' => ['required', 'max:255'],
            'description' => ['required'],
            'industry_id' => ['required', 'numeric'],
            'stage' => ['required'],
            'funding_goal' => ['required', 'numeric'],
            'funding_type' => ['required'],
            'location' => ['required']
        ] : [
            'investor_type' => ['required'],
            'bio' => ['required'],
            'min_investment' => ['numeric'],
            'max_investment' => ['numeric']
        ];

        $errors = $this->security->validateInput($data, $validationRules);

        if (!empty($errors)) {
            $this->renderCreateForm($type, $userId, $errors, $data);
            return;
        }

        try {
            $model = $type === 'startup' ? $this->startup : $this->investor;
            $profileId = $model->create($data);

            if ($profileId) {
                // Mark profile as completed
                $this->user->update($userId, ['profile_completed' => 1]);
                
                $_SESSION['toast_message'] = 'Profile created successfully!';
                $_SESSION['toast_type'] = 'success';
                header('Location: ' . url('dashboard'));
                exit;
            } else {
                throw new \Exception('Failed to create profile');
            }
        } catch (\Exception $e) {
            $this->renderCreateForm($type, $userId, ['general' => ['Profile creation failed. Please try again.']], $data);
        }
    }

    private function validateAndUpdateProfile($type, $userId, $existing, $data, $uploadedFiles)
    {
        $validationRules = $type === 'startup' ? [
            'company_name' => ['required', 'max:255'],
            'description' => ['required'],
            'industry_id' => ['required', 'numeric'],
            'stage' => ['required'],
            'funding_goal' => ['required', 'numeric'],
            'funding_type' => ['required'],
            'location' => ['required']
        ] : [
            'investor_type' => ['required'],
            'bio' => ['required'],
            'min_investment' => ['numeric'],
            'max_investment' => ['numeric']
        ];

        $errors = $this->security->validateInput($data, $validationRules);

        if (!empty($errors)) {
            $this->renderEditForm($type, $userId, $existing, $errors, $data);
            return;
        }

        try {
            $model = $type === 'startup' ? $this->startup : $this->investor;
            $updated = $model->update($existing['id'], $data);

            if ($updated) {
                $_SESSION['toast_message'] = 'Profile updated successfully!';
                $_SESSION['toast_type'] = 'success';
                header('Location: ' . url('dashboard'));
                exit;
            } else {
                throw new \Exception('Failed to update profile');
            }
        } catch (\Exception $e) {
            // Clean up uploaded files on error
            foreach ($uploadedFiles as $filePath) {
                $this->deleteOldFile($filePath);
            }

            $this->renderEditForm($type, $userId, $existing, ['general' => ['Profile update failed. Please try again.']], $data);
        }
    }

    /**
     * FIXED: Safe file deletion with proper path handling
     */
    private function deleteOldFile($relativePath)
    {
        if (empty($relativePath)) return;
        
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $relativePath;
        
        if (file_exists($fullPath) && is_file($fullPath)) {
            if (unlink($fullPath)) {
                error_log("Successfully deleted old file: " . $fullPath);
            } else {
                error_log("Failed to delete old file: " . $fullPath);
            }
        }
    }

    private function renderCreateForm($type, $userId, $errors = [], $data = [])
    {
        $user = $this->user->find($userId);
        $industries = $this->industry->getActiveIndustries();

        $this->render("profiles/{$type}/create", [
            'title' => 'Create ' . ucfirst($type) . ' Profile',
            'user' => $user,
            'industries' => $industries,
            'errors' => $errors,
            'old' => $data,
            'csrf_token' => $this->security->generateCSRFToken()
        ]);
    }

    private function renderEditForm($type, $userId, $existing, $errors = [], $data = [])
    {
        $user = $this->user->find($userId);
        $industries = $this->industry->getActiveIndustries();

        $this->render("profiles/{$type}/edit", [
            'title' => 'Edit ' . ucfirst($type) . ' Profile',
            'user' => $user,
            $type => $existing,
            'industries' => $industries,
            'errors' => $errors,
            'old' => $data,
            'csrf_token' => $this->security->generateCSRFToken()
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
            echo "<p>Expected file: {$viewFile}</p>";
            echo "<p>Profile view will be created.</p>";
            echo "<p><a href='" . url('dashboard') . "'>Return to Dashboard</a></p>";
        }

        // Get the content
        $content = ob_get_clean();

        // Include layout if specified
        if (isset($layout) && $layout) {
            include __DIR__ . '/../Views/layouts/' . $layout . '.php';
        } else {
            echo $content;
        }
    }
}