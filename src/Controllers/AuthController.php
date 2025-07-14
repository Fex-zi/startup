<?php

namespace Controllers;

use Core\Security;
use Models\User;

class AuthController
{
    private $user;
    private $security;

    public function __construct()
    {
        $this->user = new User();
        $this->security = Security::getInstance();
    }

    public function showLogin()
    {
        // If user is already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: dashboard');
            exit;
        }

        $this->render('auth/login', [
            'title' => 'Login',
            'csrf_token' => $this->security->generateCSRFToken()
        ]);
    }

    public function login()
    {
        $this->security->validateRequest();

        $email = $this->security->sanitizeInput($_POST['email'], 'email');
        $password = $_POST['password'] ?? '';

        // Validate input
        $errors = $this->security->validateInput($_POST, [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8']
        ]);

        if (!empty($errors)) {
            $this->render('auth/login', [
                'title' => 'Login',
                'errors' => $errors,
                'old_input' => ['email' => $email],
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
            return;
        }

        // Attempt authentication
        $user = $this->user->authenticate($email, $password);

        if ($user) {
            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

            // Redirect based on profile completion
            if (!$user['profile_completed']) {
                header('Location: profile/create');
            } else {
                header('Location: dashboard');
            }
            exit;
        } else {
            $this->render('auth/login', [
                'title' => 'Login',
                'errors' => ['general' => ['Invalid email or password']],
                'old_input' => ['email' => $email],
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
        }
    }

    public function showRegister()
    {
        // If user is already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: dashboard');
            exit;
        }

        $this->render('auth/register', [
            'title' => 'Register',
            'csrf_token' => $this->security->generateCSRFToken()
        ]);
    }

    public function register()
    {
        $this->security->validateRequest();

        $data = [
            'first_name' => $this->security->sanitizeInput($_POST['first_name']),
            'last_name' => $this->security->sanitizeInput($_POST['last_name']),
            'email' => $this->security->sanitizeInput($_POST['email'], 'email'),
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
            'user_type' => $_POST['user_type'] ?? ''
        ];

        // Validate input
        $errors = $this->security->validateInput($data, [
            'first_name' => ['required', 'max:100'],
            'last_name' => ['required', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'user_type' => ['required']
        ]);

        // Validate user type
        if (!in_array($data['user_type'], ['startup', 'investor'])) {
            $errors['user_type'] = ['Please select a valid user type'];
        }

        if (!empty($errors)) {
            $this->render('auth/register', [
                'title' => 'Register',
                'errors' => $errors,
                'old_input' => $data,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
            return;
        }

        try {
            // Create user
            $userId = $this->user->createUser($data);

            if ($userId) {
                // Set session data
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_type'] = $data['user_type'];
                $_SESSION['user_email'] = $data['email'];
                $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];

                // Redirect to profile creation
                header('Location: profile/create');
                exit;
            } else {
                throw new \Exception('Failed to create user account');
            }
        } catch (\Exception $e) {
            $this->render('auth/register', [
                'title' => 'Register',
                'errors' => ['general' => ['Registration failed. Please try again.']],
                'old_input' => $data,
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
        }
    }

    public function showChooseType()
    {
        // If user is already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: dashboard');
            exit;
        }

        $this->render('auth/choose-type', [
            'title' => 'Choose Account Type',
            'csrf_token' => $this->security->generateCSRFToken()
        ]);
    }

    public function setUserType()
    {
        $this->security->validateRequest();

        $userType = $_POST['user_type'] ?? '';

        if (!in_array($userType, ['startup', 'investor'])) {
            $this->render('auth/choose-type', [
                'title' => 'Choose Account Type',
                'errors' => ['user_type' => ['Please select a valid account type']],
                'csrf_token' => $this->security->generateCSRFToken()
            ]);
            return;
        }

        // Store user type in session temporarily
        $_SESSION['temp_user_type'] = $userType;

        // Redirect to registration
        header('Location: register');
        exit;
    }

    public function logout()
    {
        // Clear all session data
        session_unset();
        session_destroy();

        // Redirect to login
        header('Location: login');
        exit;
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
        include __DIR__ . '/../Views/layouts/auth.php';
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    private function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $this->user->find($_SESSION['user_id']);
    }
}
