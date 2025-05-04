<?php
require_once 'models/User.php';
require_once 'utils/Session.php';
require_once 'utils/Validator.php';

class UserController {
    private $user;
    private $session;
    private $validator;

    public function __construct() {
        $this->user = new User();
        $this->session = new Session();
        $this->validator = new Validator();
    }

    // Show login form
    public function loginForm() {
        // If already logged in, redirect to dashboard
        if ($this->session->isLoggedIn()) {
            header("Location: /");
            exit;
        }
        
        include_once 'views/users/login.php';
    }

    // Process login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate input
            $errors = [];
            
            if (empty($_POST['email'])) {
                $errors[] = "Email is required";
            } else if (!$this->validator->validateEmail($_POST['email'])) {
                $errors[] = "Invalid email format";
            }
            
            if (empty($_POST['password'])) {
                $errors[] = "Password is required";
            }
            
            // If validation fails, redirect back with errors
            if (!empty($errors)) {
                $this->session->setFlash('errors', $errors);
                $this->session->setFlash('old', $_POST);
                header("Location: /login");
                exit;
            }
            
            // Set user properties
            $this->user->email = $_POST['email'];
            $this->user->password = $_POST['password'];
            
            // Try to login
            if ($this->user->login()) {
                // Set user session
                $this->session->setUserId($this->user->id);
                $this->session->setUserName($this->user->name);
                
                header("Location: /");
                exit;
            } else {
                $this->session->setFlash('error', 'Invalid email or password');
                $this->session->setFlash('old', ['email' => $_POST['email']]);
                header("Location: /login");
                exit;
            }
        }
    }

    // Show register form
    public function registerForm() {
        // If already logged in, redirect to dashboard
        if ($this->session->isLoggedIn()) {
            header("Location: /");
            exit;
        }
        
        include_once 'views/users/register.php';
    }

    // Process registration
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate input
            $errors = [];
            
            if (empty($_POST['name'])) {
                $errors[] = "Name is required";
            }
            
            if (empty($_POST['email'])) {
                $errors[] = "Email is required";
            } else if (!$this->validator->validateEmail($_POST['email'])) {
                $errors[] = "Invalid email format";
            }
            
            if (empty($_POST['password'])) {
                $errors[] = "Password is required";
            } else if (strlen($_POST['password']) < 6) {
                $errors[] = "Password must be at least 6 characters";
            }
            
            if (empty($_POST['confirm_password'])) {
                $errors[] = "Confirm password is required";
            } else if ($_POST['password'] !== $_POST['confirm_password']) {
                $errors[] = "Passwords do not match";
            }
            
            // If validation fails, redirect back with errors
            if (!empty($errors)) {
                $this->session->setFlash('errors', $errors);
                $this->session->setFlash('old', $_POST);
                header("Location: /register");
                exit;
            }
            
            // Set user properties
            $this->user->name = $_POST['name'];
            $this->user->email = $_POST['email'];
            $this->user->password = $_POST['password'];
            
            // Check if email already exists
            if ($this->user->emailExists()) {
                $this->session->setFlash('error', 'Email already exists');
                $this->session->setFlash('old', $_POST);
                header("Location: /register");
                exit;
            }
            
            // Create user
            if ($this->user->create()) {
                // Set user session
                $this->session->setUserId($this->user->id);
                $this->session->setUserName($this->user->name);
                
                $this->session->setFlash('success', 'Registration successful');
                header("Location: /");
                exit;
            } else {
                $this->session->setFlash('error', 'Failed to register user');
                $this->session->setFlash('old', $_POST);
                header("Location: /register");
                exit;
            }
        }
    }

    // Logout
    public function logout() {
        $this->session->destroy();
        header("Location: /login");
        exit;
    }

    // Show profile form
    public function profile() {
        // Check if user is logged in
        if (!$this->session->isLoggedIn()) {
            header("Location: /login");
            exit;
        }
        
        $userId = $this->session->getUserId();
        
        // Get user details
        $this->user->getUserById($userId);
        
        include_once 'views/users/profile.php';
    }

    // Update profile
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!$this->session->isLoggedIn()) {
                header("Location: /login");
                exit;
            }
            
            $userId = $this->session->getUserId();
            
            // Validate input
            $errors = [];
            
            if (empty($_POST['name'])) {
                $errors[] = "Name is required";
            }
            
            if (empty($_POST['email'])) {
                $errors[] = "Email is required";
            } else if (!$this->validator->validateEmail($_POST['email'])) {
                $errors[] = "Invalid email format";
            }
            
            // If validation fails, redirect back with errors
            if (!empty($errors)) {
                $this->session->setFlash('errors', $errors);
                $this->session->setFlash('old', $_POST);
                header("Location: /profile");
                exit;
            }
            
            // Set user properties
            $this->user->id = $userId;
            $this->user->name = $_POST['name'];
            $this->user->email = $_POST['email'];
            
            // Update user
            if ($this->user->update()) {
                // Update session name
                $this->session->setUserName($this->user->name);
                
                $this->session->setFlash('success', 'Profile updated successfully');
            } else {
                $this->session->setFlash('error', 'Failed to update profile');
            }
            
            header("Location: /profile");
            exit;
        }
    }

    // Show change password form
    public function changePasswordForm() {
        // Check if user is logged in
        if (!$this->session->isLoggedIn()) {
            header("Location: /login");
            exit;
        }
        
        include_once 'views/users/change_password.php';
    }

    // Update password
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!$this->session->isLoggedIn()) {
                header("Location: /login");
                exit;
            }
            
            $userId = $this->session->getUserId();
            
            // Validate input
            $errors = [];
            
            if (empty($_POST['current_password'])) {
                $errors[] = "Current password is required";
            }
            
            if (empty($_POST['new_password'])) {
                $errors[] = "New password is required";
            } else if (strlen($_POST['new_password']) < 6) {
                $errors[] = "New password must be at least 6 characters";
            }
            
            if (empty($_POST['confirm_password'])) {
                $errors[] = "Confirm password is required";
            } else if ($_POST['new_password'] !== $_POST['confirm_password']) {
                $errors[] = "Passwords do not match";
            }
            
            // If validation fails, redirect back with errors
            if (!empty($errors)) {
                $this->session->setFlash('errors', $errors);
                header("Location: /change-password");
                exit;
            }
            
            // Verify current password
            $this->user->getUserById($userId);
            $this->user->password = $_POST['current_password'];
            
            if (!$this->user->login()) {
                $this->session->setFlash('error', 'Current password is incorrect');
                header("Location: /change-password");
                exit;
            }
            
            // Set new password
            $this->user->password = $_POST['new_password'];
            
            // Update password
            if ($this->user->updatePassword()) {
                $this->session->setFlash('success', 'Password updated successfully');
            } else {
                $this->session->setFlash('error', 'Failed to update password');
            }
            
            header("Location: /change-password");
            exit;
        }
    }
}
