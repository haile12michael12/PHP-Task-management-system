<?php
require_once 'models/Category.php';
require_once 'utils/Session.php';
require_once 'utils/Validator.php';

class CategoryController {
    private $category;
    private $session;
    private $validator;

    public function __construct() {
        $this->category = new Category();
        $this->session = new Session();
        $this->validator = new Validator();
        
        // Check if user is logged in for all methods except login/register
        if (!$this->session->isLoggedIn() && !in_array($_SERVER['REQUEST_URI'], ['/login', '/register'])) {
            header("Location: /login");
            exit;
        }
    }

    // List all categories
    public function index() {
        $userId = $this->session->getUserId();
        
        // Get categories
        $categories = $this->category->getAllCategories($userId);
        
        include_once 'views/categories/index.php';
    }

    // Show category creation form
    public function create() {
        include_once 'views/categories/create.php';
    }

    // Store new category
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $this->session->getUserId();
            
            // Validate input
            $errors = [];
            
            if (empty($_POST['name'])) {
                $errors[] = "Name is required";
            }
            
            if (empty($_POST['color'])) {
                $errors[] = "Color is required";
            } else if (!$this->validator->validateHexColor($_POST['color'])) {
                $errors[] = "Invalid color format";
            }
            
            // If validation fails, redirect back with errors
            if (!empty($errors)) {
                $this->session->setFlash('errors', $errors);
                $this->session->setFlash('old', $_POST);
                header("Location: /categories/create");
                exit;
            }
            
            // Set category properties
            $this->category->name = $_POST['name'];
            $this->category->color = $_POST['color'];
            $this->category->user_id = $userId;
            
            // Create category
            if ($this->category->create()) {
                $this->session->setFlash('success', 'Category created successfully');
                header("Location: /categories");
                exit;
            } else {
                $this->session->setFlash('error', 'Failed to create category');
                $this->session->setFlash('old', $_POST);
                header("Location: /categories/create");
                exit;
            }
        }
    }

    // Show category edit form
    public function edit() {
        if (isset($_GET['id'])) {
            $userId = $this->session->getUserId();
            $categoryId = $_GET['id'];
            
            // Get category details
            if ($this->category->getCategory($categoryId, $userId)) {
                include_once 'views/categories/edit.php';
            } else {
                $this->session->setFlash('error', 'Category not found');
                header("Location: /categories");
                exit;
            }
        } else {
            header("Location: /categories");
            exit;
        }
    }

    // Update category
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $this->session->getUserId();
            
            // Validate input
            $errors = [];
            
            if (empty($_POST['id'])) {
                $errors[] = "Category ID is missing";
            }
            
            if (empty($_POST['name'])) {
                $errors[] = "Name is required";
            }
            
            if (empty($_POST['color'])) {
                $errors[] = "Color is required";
            } else if (!$this->validator->validateHexColor($_POST['color'])) {
                $errors[] = "Invalid color format";
            }
            
            // If validation fails, redirect back with errors
            if (!empty($errors)) {
                $this->session->setFlash('errors', $errors);
                $this->session->setFlash('old', $_POST);
                header("Location: /categories/edit?id=" . $_POST['id']);
                exit;
            }
            
            // Set category properties
            $this->category->id = $_POST['id'];
            $this->category->name = $_POST['name'];
            $this->category->color = $_POST['color'];
            $this->category->user_id = $userId;
            
            // Update category
            if ($this->category->update()) {
                $this->session->setFlash('success', 'Category updated successfully');
                header("Location: /categories");
                exit;
            } else {
                $this->session->setFlash('error', 'Failed to update category');
                $this->session->setFlash('old', $_POST);
                header("Location: /categories/edit?id=" . $_POST['id']);
                exit;
            }
        }
    }

    // Delete category
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $userId = $this->session->getUserId();
            
            $this->category->id = $_POST['id'];
            $this->category->user_id = $userId;
            
            if ($this->category->delete()) {
                $this->session->setFlash('success', 'Category deleted successfully');
            } else {
                $this->session->setFlash('error', 'Cannot delete category that is in use');
            }
            
            header("Location: /categories");
            exit;
        } else {
            header("Location: /categories");
            exit;
        }
    }
}
