<?php
// Start session
session_start();

// Load required files
require_once '../config/database.php';
require_once '../models/Task.php';
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

// Initialize task model
$task = new Task();

// Handle API requests
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_status':
        handleUpdateStatus($task, $userId);
        break;
        
    case 'get_tasks':
        handleGetTasks($task, $userId);
        break;
    
    case 'get_upcoming':
        handleGetUpcomingTasks($task, $userId);
        break;
        
    case 'get_overdue':
        handleGetOverdueTasks($task, $userId);
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action specified.'
        ]);
        break;
}

/**
 * Handle updating a task's status
 * 
 * @param Task $task The task model
 * @param int $userId The user ID
 */
function handleUpdateStatus($task, $userId) {
    // Validate parameters
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required parameters.'
        ]);
        return;
    }
    
    $taskId = (int) $_POST['id'];
    $newStatus = $_POST['status'];
    
    // Validate status
    $validStatuses = ['pending', 'in_progress', 'completed'];
    if (!in_array($newStatus, $validStatuses)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid status value.'
        ]);
        return;
    }
    
    // Get task
    if (!$task->getTask($taskId, $userId)) {
        echo json_encode([
            'success' => false,
            'message' => 'Task not found or access denied.'
        ]);
        return;
    }
    
    // Update status
    if ($task->updateStatus($newStatus)) {
        echo json_encode([
            'success' => true,
            'message' => 'Task status updated successfully.',
            'new_status' => $newStatus
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update task status.'
        ]);
    }
}

/**
 * Handle getting a list of tasks
 * 
 * @param Task $task The task model
 * @param int $userId The user ID
 */
function handleGetTasks($task, $userId) {
    // Get filter parameters
    $filters = [
        'status' => $_POST['status'] ?? '',
        'priority' => $_POST['priority'] ?? '',
        'category_id' => $_POST['category_id'] ?? '',
        'due_date' => $_POST['due_date'] ?? '',
        'search' => $_POST['search'] ?? '',
        'sort' => $_POST['sort'] ?? 'due_date',
        'direction' => $_POST['direction'] ?? 'asc'
    ];
    
    // Get tasks
    $tasks = $task->getAllTasks($userId, $filters);
    
    // Convert to array
    $tasksArray = [];
    while ($row = $tasks->fetch()) {
        $tasksArray[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'tasks' => $tasksArray
    ]);
}

/**
 * Handle getting upcoming tasks
 * 
 * @param Task $task The task model
 * @param int $userId The user ID
 */
function handleGetUpcomingTasks($task, $userId) {
    $limit = (int) ($_POST['limit'] ?? 5);
    
    // Get upcoming tasks
    $tasks = $task->getUpcomingTasks($userId, $limit);
    
    // Convert to array
    $tasksArray = [];
    while ($row = $tasks->fetch()) {
        $tasksArray[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'tasks' => $tasksArray
    ]);
}

/**
 * Handle getting overdue tasks
 * 
 * @param Task $task The task model
 * @param int $userId The user ID
 */
function handleGetOverdueTasks($task, $userId) {
    // Get overdue tasks
    $tasks = $task->getOverdueTasks($userId);
    
    // Convert to array
    $tasksArray = [];
    while ($row = $tasks->fetch()) {
        $tasksArray[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'tasks' => $tasksArray
    ]);
}
