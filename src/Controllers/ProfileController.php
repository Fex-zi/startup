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

    // FIXED: Clean, single upload directory structure
    private const UPLOAD_BASE_DIR = 'assets/uploads/';
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
        
        // Initialize upload directories
        $this->initializeUploadDirectories();
    }

    /**
     * FIXED: Create clean directory structure in public/assets/uploads/
     */
    private function initializeUploadDirectories()
    {
        // Determine correct document root based on request path
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        if (!file_exists($documentRoot . '/public')) {
            $documentRoot = dirname($_SERVER['DOCUMENT_ROOT']) . '/public';
        }
        
        $uploadRoot = $documentRoot . '/' . self::UPLOAD_BASE_DIR;
        
        // Clean, organized directory structure
        $directories = [
            'logos',                    // Company logos
            'profiles',                 // Profile pictures  
            'documents',                // Documents root
            'documents/pitch-decks',    // Pitch decks
            'documents/business-plans', // Business plans
            'documents/financials',     // Financial documents
            'documents/legal',          // Legal documents
            'temp'                      // Temporary processing
        ];

        foreach ($directories as $dir) {
            $fullPath = $uploadRoot . $dir;
            if (!file_exists($fullPath)) {
                if (!mkdir($fullPath, 0755, true)) {
                    error_log("Failed to create upload directory: " . $fullPath);
                }
            }
            
            // Add .htaccess for security to document directories
            if (strpos($dir, 'documents') !== false) {
                $htaccessPath = $fullPath . '/.htaccess';
                if (!file_exists($htaccessPath)) {
                    file_put_contents($htaccessPath, "deny from all\n");
                }
            }
        }
    }

    /**
     * Show current user's own profile (convenience route)
     * SECURE: Only shows own profile, cannot access others
     */
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $this->viewProfile($userId, true); // true = isOwnProfile
    }

    /**
     * 🚨 CRITICAL SECURITY FIX: Enhanced profile viewing with parameter validation
     */
    public function viewSecure($id = null)
    {
        // SECURITY: Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        // 🔥 CRITICAL FIX: Validate and sanitize the ID parameter
        if ($id === null || !is_numeric($id)) {
            error_log("SECURITY ALERT: Invalid profile ID attempted: " . var_export($id, true));
            $_SESSION['toast_message'] = 'Invalid profile ID';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('dashboard'));
            exit;
        }

        $currentUserId = (int)$_SESSION['user_id'];
        $targetUserId = (int)$id;

        // 🔥 CRITICAL FIX: Additional validation - ensure target user exists
        $targetUser = $this->user->find($targetUserId);
        if (!$targetUser || !$targetUser['is_active']) {
            error_log("SECURITY ALERT: Attempted to view invalid/inactive user: {$targetUserId}");
            $_SESSION['toast_message'] = 'Profile not found or inactive';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('dashboard'));
            exit;
        }

        // SECURITY: Check permissions before proceeding
        if ($currentUserId !== $targetUserId) {
            if (!$this->canViewProfile($currentUserId, $targetUserId)) {
                error_log("SECURITY ALERT: Unauthorized profile access attempt. User {$currentUserId} tried to view {$targetUserId}");
                $_SESSION['toast_message'] = 'You do not have permission to view this profile';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . url('dashboard'));
                exit;
            }
        }
        
        $isOwnProfile = ($currentUserId === $targetUserId);
        
        // 🔥 CRITICAL FIX: Log profile access for security audit
        error_log("Profile Access: User {$currentUserId} viewing profile {$targetUserId} (own: " . ($isOwnProfile ? 'yes' : 'no') . ")");
        
        $this->viewProfile($targetUserId, $isOwnProfile);
    }

    /**
     * View startup profile by slug (public access)
     */
    public function viewStartupBySlug($slug)
    {
        // 🔥 SECURITY FIX: Validate slug format
        if (empty($slug) || !preg_match('/^[a-z0-9-]+$/', $slug)) {
            error_log("SECURITY ALERT: Invalid startup slug attempted: " . var_export($slug, true));
            $_SESSION['toast_message'] = 'Invalid startup identifier';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('search/startups'));
            exit;
        }

        $startup = $this->startup->findBy('slug', $slug);
        if (!$startup) {
            $_SESSION['toast_message'] = 'Startup not found';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('search/startups'));
            exit;
        }
        
        // 🔥 SECURITY FIX: Verify the associated user exists and is active
        $user = $this->user->find($startup['user_id']);
        if (!$user || !$user['is_active']) {
            error_log("SECURITY ALERT: Startup slug {$slug} points to inactive/invalid user {$startup['user_id']}");
            $_SESSION['toast_message'] = 'This startup profile is no longer available';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('search/startups'));
            exit;
        }
        
        $this->viewProfile($startup['user_id'], false); // false = not own profile
    }

    /**
     * View investor profile (public access with restrictions)
     */
    public function viewInvestorPublic($id)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'startup') {
            $_SESSION['toast_message'] = 'Only startups can view investor profiles';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('dashboard'));
            exit;
        }
        
        // 🔥 SECURITY FIX: Validate investor ID
        if (!is_numeric($id)) {
            error_log("SECURITY ALERT: Invalid investor ID attempted: " . var_export($id, true));
            $_SESSION['toast_message'] = 'Invalid investor ID';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('search/investors'));
            exit;
        }
        
        $targetUserId = (int)$id;
        
        // 🔥 SECURITY FIX: Verify the user exists and is an investor
        $user = $this->user->find($targetUserId);
        if (!$user || $user['user_type'] !== 'investor' || !$user['is_active']) {
            error_log("SECURITY ALERT: Invalid investor access attempt for ID {$targetUserId}");
            $_SESSION['toast_message'] = 'Investor not found or inactive';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('search/investors'));
            exit;
        }
        
        $this->viewProfile($targetUserId, false); // false = not own profile
    }

    /**
     * 🔥 ENHANCED: Check if current user can view target profile
     */
    private function canViewProfile($currentUserId, $targetUserId)
    {
        // Users can always view their own profile
        if ($currentUserId === $targetUserId) {
            return true;
        }
        
        $currentUser = $this->user->find($currentUserId);
        $targetUser = $this->user->find($targetUserId);
        
        if (!$currentUser || !$targetUser) {
            error_log("Profile Access Denied: User not found. Current: {$currentUserId}, Target: {$targetUserId}");
            return false;
        }

        // 🔥 SECURITY: Only allow cross-type viewing (investors ↔ startups)
        $canView = false;
        
        if ($currentUser['user_type'] === 'investor' && $targetUser['user_type'] === 'startup') {
            $canView = true;
        } elseif ($currentUser['user_type'] === 'startup' && $targetUser['user_type'] === 'investor') {
            $canView = true;
        }

        // Log access attempts for security monitoring
        if (!$canView) {
            error_log("Profile Access Denied: {$currentUser['user_type']} (ID: {$currentUserId}) attempted to view {$targetUser['user_type']} profile (ID: {$targetUserId})");
        }
        
        return $canView;
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
            $_SESSION['toast_message'] = 'Profile creation failed: ' . $e->getMessage();
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
            $_SESSION['toast_message'] = 'Profile update failed: ' . $e->getMessage();
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('profile/edit'));
            exit;
        }
    }

    /**
     * 🔥 CRITICAL FIX: Enhanced profile viewing with data integrity checks
     */
    private function viewProfile($userId, $isOwnProfile)
    {
        $currentUserId = $_SESSION['user_id'] ?? null;
        
        // 🔥 CRITICAL FIX: Double-check user ID and get fresh data
        $user = $this->user->find($userId);
        if (!$user || !$user['is_active']) {
            error_log("CRITICAL SECURITY ALERT: Attempted to view invalid/inactive user profile: {$userId}");
            $_SESSION['toast_message'] = 'Profile not found';
            $_SESSION['toast_type'] = 'error';
            header('Location: ' . url('dashboard'));
            exit;
        }

        // SECURITY: Determine what information to show based on profile ownership and user types
        $showPrivateInfo = $isOwnProfile;
        $showContactInfo = $isOwnProfile || ($user['user_type'] === 'startup' && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'investor');
        $showDocuments = $isOwnProfile || (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'investor');
        $showFullDetails = $isOwnProfile || $this->canViewProfile($currentUserId, $userId);

        if ($user['user_type'] === 'startup') {
            $startup = $this->startup->findBy('user_id', $userId);
            if (!$startup) {
                $_SESSION['toast_message'] = 'Startup profile not found';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . url('dashboard'));
                exit;
            }

            // 🔥 CRITICAL FIX: Verify profile ownership - prevents showing wrong profiles
            if ($startup['user_id'] != $userId) {
                error_log("CRITICAL SECURITY ALERT: Profile ownership mismatch! Expected user {$userId}, got profile for user {$startup['user_id']}");
                $_SESSION['toast_message'] = 'Security error: Profile data mismatch';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . url('dashboard'));
                exit;
            }
            
            $industry = $this->industry->find($startup['industry_id']);
            
            // Get similar startups (exclude current one)
            $similarStartups = $this->startup->getSimilarStartups(
                $startup['industry_id'], 
                $startup['id'], 
                3
            );
            
            // Choose the right view based on whether it's own profile or public view
            $viewTemplate = $isOwnProfile ? 'profiles/startup/view_own' : 'profiles/startup/public';
            
            $this->render($viewTemplate, [
                'title' => $startup['company_name'] . ' - Startup Profile',
                'user' => $user,
                'startup' => $startup,
                'industry' => $industry,
                'similar_startups' => $similarStartups,
                'is_own_profile' => $isOwnProfile,
                'show_private_info' => $showPrivateInfo,
                'show_contact_info' => $showContactInfo,
                'show_documents' => $showDocuments,
                'show_full_details' => $showFullDetails,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
        } else {
            $investor = $this->investor->findBy('user_id', $userId);
            if (!$investor) {
                $_SESSION['toast_message'] = 'Investor profile not found';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . url('dashboard'));
                exit;
            }

            // 🔥 CRITICAL FIX: Verify profile ownership - prevents showing wrong profiles
            if ($investor['user_id'] != $userId) {
                error_log("CRITICAL SECURITY ALERT: Profile ownership mismatch! Expected user {$userId}, got profile for user {$investor['user_id']}");
                $_SESSION['toast_message'] = 'Security error: Profile data mismatch';
                $_SESSION['toast_type'] = 'error';
                header('Location: ' . url('dashboard'));
                exit;
            }
            
            // FIXED: Get actual industry names for display
            $preferredIndustryNames = [];
            if (!empty($investor['preferred_industries'])) {
                $industryIds = json_decode($investor['preferred_industries'], true) ?? [];
                if (!empty($industryIds)) {
                    $preferredIndustryNames = $this->industry->getIndustryNamesByIds($industryIds);
                }
            }
            
            // Choose the right view based on whether it's own profile or public view
            $viewTemplate = $isOwnProfile ? 'profiles/investor/view_own' : 'profiles/investor/public';
            
            $this->render($viewTemplate, [
                'title' => $user['first_name'] . ' ' . $user['last_name'] . ' - Investor Profile',
                'user' => $user,
                'investor' => $investor,
                'preferred_industry_names' => $preferredIndustryNames,
                'is_own_profile' => $isOwnProfile,
                'show_private_info' => $showPrivateInfo,
                'show_contact_info' => $showContactInfo,
                'show_full_details' => $showFullDetails,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
        }
    }

    /**
     * FIXED: Simplified file upload handling with consistent paths
     */
    private function handleFileUploads()
    {
        $uploadedFiles = [];

        // FIXED: Simple file mapping with consistent paths
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
                'dir' => 'documents/pitch-decks',
                'types' => self::ALLOWED_DOCUMENT_TYPES,
                'size' => self::MAX_DOCUMENT_SIZE
            ],
            'business_plan' => [
                'dir' => 'documents/business-plans',
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
                } catch (\Exception $e) {
                    throw new \Exception("Failed to upload {$field}: " . $e->getMessage());
                }
            }
        }

        return $uploadedFiles;
    }

    /**
     * FIXED: Consistent file upload processing with proper paths
     */
    private function processFileUpload($file, $directory, $allowedExtensions, $maxSize)
    {
        // Determine correct document root
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        if (!file_exists($documentRoot . '/public')) {
            $documentRoot = dirname($_SERVER['DOCUMENT_ROOT']) . '/public';
        }
        
        $uploadDir = $documentRoot . '/' . self::UPLOAD_BASE_DIR . $directory . '/';
        
        // Ensure directory exists
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \Exception('Cannot create upload directory');
            }
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            throw new \Exception('File size exceeds limit of ' . ($maxSize / 1024 / 1024) . 'MB');
        }

        // Validate file type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new \Exception('Invalid file type. Allowed: ' . implode(', ', $allowedExtensions));
        }

        // Generate secure filename
        $filename = $this->generateSecureFilename($file['name']);
        $filePath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Return web-accessible path for database storage
            return self::UPLOAD_BASE_DIR . $directory . '/' . $filename;
        } else {
            throw new \Exception('Failed to save uploaded file');
        }
    }

    private function generateSecureFilename($originalName)
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $safeName = substr($safeName, 0, 50);
        
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
            'website' => $this->security->sanitizeInput($_POST['website'] ?? ''),
            'funding_goal' => (float)($_POST['funding_goal'] ?? 0),
            'funding_type' => $_POST['funding_type'] ?? '',
            'slug' => $this->generateSlug($_POST['company_name'])
        ];

        // FIXED: Handle file uploads with correct database column names
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
            'company_name' => $this->security->sanitizeInput($_POST['company_name'] ?? ''),
            'bio' => $this->security->sanitizeInput($_POST['bio']),
            'location' => $this->security->sanitizeInput($_POST['location']),
            'investment_range_min' => (float)($_POST['investment_range_min'] ?? 0),
            'investment_range_max' => (float)($_POST['investment_range_max'] ?? 0),
            'preferred_industries' => json_encode($_POST['preferred_industries'] ?? []),
            'investment_stages' => json_encode($_POST['investment_stages'] ?? []),
            'website' => $this->security->sanitizeInput($_POST['website'] ?? ''),
            'linkedin_url' => $this->security->sanitizeInput($_POST['linkedin_url'] ?? '')
        ];

        // FIXED: Handle file uploads
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
            'website' => $this->security->sanitizeInput($_POST['website'] ?? ''),
            'funding_goal' => (float)($_POST['funding_goal'] ?? 0),
            'funding_type' => $_POST['funding_type'] ?? '',
            'slug' => $this->generateSlug($_POST['company_name'])
        ];

        // FIXED: Handle file uploads and cleanup old files
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
            'company_name' => $this->security->sanitizeInput($_POST['company_name'] ?? ''),
            'bio' => $this->security->sanitizeInput($_POST['bio']),
            'location' => $this->security->sanitizeInput($_POST['location']),
            'investment_range_min' => (float)($_POST['investment_range_min'] ?? 0),
            'investment_range_max' => (float)($_POST['investment_range_max'] ?? 0),
            'preferred_industries' => json_encode($_POST['preferred_industries'] ?? []),
            'investment_stages' => json_encode($_POST['investment_stages'] ?? []),
            'website' => $this->security->sanitizeInput($_POST['website'] ?? ''),
            'linkedin_url' => $this->security->sanitizeInput($_POST['linkedin_url'] ?? '')
        ];

        // FIXED: Handle file uploads and cleanup
        if (isset($uploadedFiles['profile_picture'])) {
            $data['profile_picture_url'] = $uploadedFiles['profile_picture'];
            $this->deleteOldFile($existingInvestor['profile_picture_url'] ?? '');
        }

        $this->validateAndUpdateProfile('investor', $userId, $existingInvestor, $data, $uploadedFiles);
    }

    private function generateSlug($text)
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
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
            'investment_range_min' => ['numeric'],
            'investment_range_max' => ['numeric']
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
            'investment_range_min' => ['numeric'],
            'investment_range_max' => ['numeric']
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
                header('Location: ' . url('profile/view/' . $userId));
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
     * FIXED: Safe file deletion with correct paths
     */
    private function deleteOldFile($relativePath)
    {
        if (empty($relativePath)) return;
        
        // Determine correct document root
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        if (!file_exists($documentRoot . '/public')) {
            $documentRoot = dirname($_SERVER['DOCUMENT_ROOT']) . '/public';
        }
        
        // Handle both old format (with uploads prefix) and new format
        $cleanPath = str_replace('assets/uploads/', '', $relativePath);
        $fullPath = $documentRoot . '/' . self::UPLOAD_BASE_DIR . $cleanPath;
        
        if (file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
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
            'old_input' => $data,
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
            'old_input' => $data,
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
