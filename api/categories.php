<?php
// Start session
session_start();

// Load required files
require_once '../config/database.php';
require_once '../models/Category.php';
require_once '../utils/Session.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
$session = new Session();
if (!$session->isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Please log in.'
    ]);
    exit;
}

// Get user ID
$userId = $session->getUserId();

// Initialize category model
$category = new Category();

// Handle API requests
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'get_categories':
        handleGetCategories($category, $userId);
        break;
        
    case 'get_category':
        handleGetCategory($category, $userId);
        break;
        
    case 'create_category':
        handleCreateCategory($category, $userId);
        break;
        
    case 'update_category':
        handleUpdateCategory($category, $userId);
        break;
        
    case 'delete_category':
        handleDeleteCategory($category, $userId);
        break;
        
    case 'get_stats':
        handleGetCategoryStats($category, $userId);
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action specified.'
        ]);
        break;
}

/**
 * Handle getting all categories
 * 
 * @param Category $category The category model
 * @param int $userId The user ID
 */
function handleGetCategories($category, $userId) {
    // Get categories
    $categories = $category->getAllCategories($userId);
    
    // Convert to array
    $categoriesArray = [];
    while ($row = $categories->fetch()) {
        $categoriesArray[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'categories' => $categoriesArray
    ]);
}

/**
 * Handle getting a single category
 * 
 * @param Category $category The category model
 * @param int $userId The user ID
 */
function handleGetCategory($category, $userId) {
    // Validate parameters
    if (!isset($_POST['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required parameter: id.'
        ]);
        return;
    }
    
    $categoryId = (int) $_POST['id'];
    
    // Get category
    if ($category->getCategory($categoryId, $userId)) {
        echo json_encode([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'color' => $category->color,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Category not found or access denied.'
        ]);
    }
}

/**
 * Handle creating a new category
 * 
 * @param Category $category The category model
 * @param int $userId The user ID
 */
function handleCreateCategory($category, $userId) {
    // Validate parameters
    if (!isset($_POST['name']) || !isset($_POST['color'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required parameters.'
        ]);
        return;
    }
    
    // Validate color format
    if (!preg_match('/^#([A-Fa-f0-9]{6})$/', $_POST['color'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid color format. Use hexadecimal format (e.g., #3498db).'
        ]);
        return;
    }
    
    // Set category properties
    $category->name = htmlspecialchars(strip_tags($_POST['name']));
    $category->color = htmlspecialchars(strip_tags($_POST['color']));
    $category->user_id = $userId;
    
    // Create category
    if ($category->create()) {
        echo json_encode([
            'success' => true,
            'message' => 'Category created successfully.',
            'category_id' => $category->id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create category.'
        ]);
    }
}

/**
 * Handle updating a category
 * 
 * @param Category $category The category model
 * @param int $userId The user ID
 */
function handleUpdateCategory($category, $userId) {
    // Validate parameters
    if (!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['color'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required parameters.'
        ]);
        return;
    }
    
    // Validate color format
    if (!preg_match('/^#([A-Fa-f0-9]{6})$/', $_POST['color'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid color format. Use hexadecimal format (e.g., #3498db).'
        ]);
        return;
    }
    
    $categoryId = (int) $_POST['id'];
    
    // Check if category exists and belongs to user
    if (!$category->getCategory($categoryId, $userId)) {
        echo json_encode([
            'success' => false,
            'message' => 'Category not found or access denied.'
        ]);
        return;
    }
    
    // Set category properties
    $category->id = $categoryId;
    $category->name = htmlspecialchars(strip_tags($_POST['name']));
    $category->color = htmlspecialchars(strip_tags($_POST['color']));
    $category->user_id = $userId;
    
    // Update category
    if ($category->update()) {
        echo json_encode([
            'success' => true,
            'message' => 'Category updated successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update category.'
        ]);
    }
}

/**
 * Handle deleting a category
 * 
 * @param Category $category The category model
 * @param int $userId The user ID
 */
function handleDeleteCategory($category, $userId) {
    // Validate parameters
    if (!isset($_POST['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required parameter: id.'
        ]);
        return;
    }
    
    $categoryId = (int) $_POST['id'];
    
    // Check if category exists and belongs to user
    if (!$category->getCategory($categoryId, $userId)) {
        echo json_encode([
            'success' => false,
            'message' => 'Category not found or access denied.'
        ]);
        return;
    }
    
    // Set category ID
    $category->id = $categoryId;
    $category->user_id = $userId;
    
    // Delete category
    if ($category->delete()) {
        echo json_encode([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete category. It might be in use by one or more tasks.'
        ]);
    }
}

/**
 * Handle getting category statistics
 * 
 * @param Category $category The category model
 * @param int $userId The user ID
 */
function handleGetCategoryStats($category, $userId) {
    // Get category stats
    $stats = $category->getTaskCountByCategory($userId);
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
}
