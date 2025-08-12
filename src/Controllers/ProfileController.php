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

    // IMPROVED: Better upload directory structure
    private const UPLOAD_BASE_DIR = DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
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
     * IMPROVED: Create upload directory structure if it doesn't exist
     */
    private function initializeUploadDirectories()
    {
        $uploadRoot = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_BASE_DIR;
        
        $directories = [
            'profiles' => ['startups', 'investors'],
            'documents' => ['pitch-decks', 'business-plans', 'financials', 'legal'],
            'images' => ['logos', 'avatars', 'gallery'],
            'temp' => ['processing']
        ];

        foreach ($directories as $category => $subdirs) {
            $categoryPath = $uploadRoot . $category;
            if (!file_exists($categoryPath)) {
                mkdir($categoryPath, 0755, true);
            }
            
            foreach ($subdirs as $subdir) {
                $fullPath = $categoryPath . DIRECTORY_SEPARATOR . $subdir;
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }
                
                // Add .htaccess for security (except for images)
                if ($category !== 'images') {
                    $htaccessPath = $fullPath . DIRECTORY_SEPARATOR . '.htaccess';
                    if (!file_exists($htaccessPath)) {
                        file_put_contents($htaccessPath, "deny from all\n");
                    }
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
            $this->renderCreateForm('startup', $userId, $errors, $data);
            return;
        }

        try {
            $startupId = $this->startup->create($data);

            if ($startupId) {
                $this->user->markProfileCompleted($userId);
                $_SESSION['toast_message'] = 'Startup profile created successfully!';
                $_SESSION['toast_type'] = 'success';
                header('Location: ' . url('dashboard'));
                exit;
            } else {
                throw new \Exception('Failed to create startup profile');
            }
        } catch (\Exception $e) {
            $this->renderCreateForm('startup', $userId, ['general' => ['Profile creation failed. Please try again.']], $data);
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
            $this->renderCreateForm('investor', $userId, $errors, $data);
            return;
        }

        try {
            $investorId = $this->investor->create($data);

            if ($investorId) {
                $this->user->markProfileCompleted($userId);
                $_SESSION['toast_message'] = 'Investor profile created successfully!';
                $_SESSION['toast_type'] = 'success';
                header('Location: ' . url('dashboard'));
                exit;
            } else {
                throw new \Exception('Failed to create investor profile');
            }
        } catch (\Exception $e) {
            $this->renderCreateForm('investor', $userId, ['general' => ['Profile creation failed. Please try again.']], $data);
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

    /**
     * IMPROVED: Enhanced file upload handling with better organization
     */
    private function handleFileUploads()
    {
        $uploadedFiles = [];
        $uploadBase = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_BASE_DIR;

        // Define file upload mappings
        $fileMapping = [
            'logo' => [
                'dir' => 'images/logos',
                'types' => self::ALLOWED_IMAGE_TYPES,
                'size' => self::MAX_IMAGE_SIZE
            ],
            'profile_picture' => [
                'dir' => 'images/avatars',
                'types' => self::ALLOWED_IMAGE_TYPES,
                'size' => self::MAX_IMAGE_SIZE
            ],
            'pitch_deck' => [
                'dir' => 'documents/pitch-decks',
                'types' => self::ALLOWED_DOCUMENT_TYPES,
                'size' => self::MAX_DOCUMENT_SIZE
            ],
            'business_plan' => [
                'dir' => 'documents/business-plans',
                'types' => self::ALLOWED_DOCUMENT_TYPES,
                'size' => self::MAX_DOCUMENT_SIZE
            ],
            'financial_document' => [
                'dir' => 'documents/financials',
                'types' => self::ALLOWED_DOCUMENT_TYPES,
                'size' => self::MAX_DOCUMENT_SIZE
            ]
        ];

        foreach ($fileMapping as $field => $config) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $uploadedFiles[$field] = $this->processFileUpload(
                    $_FILES[$field], 
                    $config['dir'], 
                    $config['types'], 
                    $config['size']
                );
            }
        }

        return $uploadedFiles;
    }

    /**
     * IMPROVED: Secure file upload processing with better validation
     */
    private function processFileUpload($file, $directory, $allowedExtensions, $maxSize)
    {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_BASE_DIR . $directory . DIRECTORY_SEPARATOR;
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            throw new \Exception('File size exceeds maximum allowed size of ' . ($maxSize / 1024 / 1024) . 'MB');
        }

        // Validate file type by extension and MIME type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new \Exception('Invalid file type. Allowed: ' . implode(', ', $allowedExtensions));
        }

        // Additional MIME type validation for security
        $this->validateMimeType($file['tmp_name'], $fileExtension);

        // Generate secure filename
        $filename = $this->generateSecureFilename($file['name']);
        $filePath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $directory . '/' . $filename; // Return relative path
        } else {
            throw new \Exception('Failed to upload file');
        }
    }

    /**
     * IMPROVED: MIME type validation for additional security
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
            throw new \Exception('File content does not match extension');
        }
    }

    /**
     * IMPROVED: Generate secure filename
     */
    private function generateSecureFilename($originalName)
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $safeName = substr($safeName, 0, 50); // Limit length
        
        return uniqid($safeName . '_', true) . '.' . $extension;
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
            'website' => $this->security->sanitizeInput($_POST['website'], 'url'),
            'funding_goal' => (float)($_POST['funding_goal'] ?? 0),
            'funding_type' => $_POST['funding_type'] ?? '',
            'location' => $this->security->sanitizeInput($_POST['location'])
        ];

        // Handle file uploads
        foreach (['logo', 'pitch_deck', 'business_plan'] as $field) {
            if (isset($uploadedFiles[$field])) {
                $data[$field . '_url'] = $uploadedFiles[$field];
                // Delete old file
                $this->deleteOldFile($existingStartup[$field . '_url'] ?? '');
            }
        }

        // Update slug if company name changed
        if ($data['company_name'] !== $existingStartup['company_name']) {
            $data['slug'] = $this->generateSlug($data['company_name']);
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
            'company_name' => $this->security->sanitizeInput($_POST['company_name']),
            'bio' => $this->security->sanitizeInput($_POST['bio']),
            'preferred_industries' => json_encode($_POST['preferred_industries'] ?? []),
            'investment_stages' => json_encode($_POST['investment_stages'] ?? []),
            'min_investment' => (float)($_POST['min_investment'] ?? 0),
            'max_investment' => (float)($_POST['max_investment'] ?? 0),
            'location' => $this->security->sanitizeInput($_POST['location']),
            'website' => $this->security->sanitizeInput($_POST['website'], 'url'),
            'linkedin' => $this->security->sanitizeInput($_POST['linkedin_url'], 'url'),
            'years_experience' => $_POST['years_experience'] ?? '',
            'portfolio_size' => $_POST['portfolio_size'] ?? '',
            'investment_philosophy' => $this->security->sanitizeInput($_POST['investment_philosophy'])
        ];

        // Handle file uploads
        if (isset($uploadedFiles['profile_picture'])) {
            $data['profile_picture_url'] = $uploadedFiles['profile_picture'];
            $this->deleteOldFile($existingInvestor['profile_picture_url'] ?? '');
        }

        $this->validateAndUpdateProfile('investor', $userId, $existingInvestor, $data, $uploadedFiles);
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
     * IMPROVED: Safe file deletion with full path handling
     */
    private function deleteOldFile($relativePath)
    {
        if (empty($relativePath)) return;
        
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . self::UPLOAD_BASE_DIR . ltrim($relativePath, '/');
        if (file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    private function generateSlug($text)
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        $baseSlug = $slug;
        $counter = 1;
        
        while ($this->startup->findBy('slug', $slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    private function renderCreateForm($type, $userId, $errors, $data)
    {
        $industries = $this->industry->getActiveIndustries();
        $user = $this->user->find($userId);
        
        $this->render("profiles/{$type}/create", [
            'title' => "Create " . ucfirst($type) . " Profile",
            'user' => $user,
            'industries' => $industries,
            'errors' => $errors,
            'old_input' => $data,
            'csrf_token' => $this->security->generateCSRFToken()
        ]);
    }

    private function renderEditForm($type, $userId, $existing, $errors, $data)
    {
        $industries = $this->industry->getActiveIndustries();
        $user = $this->user->find($userId);
        
        $this->render("profiles/{$type}/edit", [
            'title' => "Edit " . ucfirst($type) . " Profile",
            'user' => $user,
            $type => array_merge($existing, $data),
            'industries' => $industries,
            'errors' => $errors,
            'old_input' => $data,
            'csrf_token' => $this->security->generateCSRFToken()
        ]);
    }

    public function view($id)
    {
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

    private function render($view, $data = [])
    {
        extract($data);
        
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: $view");
        }
        
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        
        if (isset($layout)) {
            $layoutPath = __DIR__ . '/../Views/layouts/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                include $layoutPath;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }
}