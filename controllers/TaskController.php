<?php
require_once 'models/Task.php';
require_once 'models/Category.php';
require_once 'utils/Session.php';
require_once 'utils/Validator.php';

class TaskController {
    private $task;
    private $category;
    private $session;
    private $validator;

    public function __construct() {
        $this->task = new Task();
        $this->category = new Category();
        $this->session = new Session();
        $this->validator = new Validator();
        
        // Check if user is logged in for all methods except login/register
        if (!$this->session->isLoggedIn() && !in_array($_SERVER['REQUEST_URI'], ['/login', '/register'])) {
            header("Location: /login");
            exit;
        }
    }

    // Dashboard view
    public function dashboard() {
        $userId = $this->session->getUserId();
        
        // Get task statistics
        $taskCountByStatus = $this->task->getTaskCountByStatus($userId);
        $upcomingTasks = $this->task->getUpcomingTasks($userId);
        $overdueTasks = $this->task->getOverdueTasks($userId);
        $categoryStats = $this->category->getTaskCountByCategory($userId);
        
        include_once 'views/dashboard.php';
    }

    // List all tasks
    public function index() {
        $userId = $this->session->getUserId();
        
        // Get filter parameters from GET request
        $filters = [
            'status' => isset($_GET['status']) ? $_GET['status'] : '',
            'priority' => isset($_GET['priority']) ? $_GET['priority'] : '',
            'category_id' => isset($_GET['category_id']) ? $_GET['category_id'] : '',
            'due_date' => isset($_GET['due_date']) ? $_GET['due_date'] : '',
            'search' => isset($_GET['search']) ? $_GET['search'] : '',
            'sort' => isset($_GET['sort']) ? $_GET['sort'] : 'due_date',
            'direction' => isset($_GET['direction']) ? $_GET['direction'] : 'asc'
        ];
        
        // Get tasks with filters
        $tasks = $this->task->getAllTasks($userId, $filters);
        
        // Get categories for filter dropdown
        $categories = $this->category->getAllCategories($userId);
        
        include_once 'views/tasks/index.php';
    }

    // Show task creation form
    public function create() {
        $userId = $this->session->getUserId();
        
        // Get categories for dropdown
        $categories = $this->category->getAllCategories($userId);
        
        include_once 'views/tasks/create.php';
    }

    // Store new task
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $this->session->getUserId();
            
            // Validate input
            $errors = [];
            
            if (empty($_POST['title'])) {
                $errors[] = "Title is required";
            }
            
            if (empty($_POST['status'])) {
                $errors[] = "Status is required";
            }
            
            if (empty($_POST['priority'])) {
                $errors[] = "Priority is required";
            }
            
            if (empty($_POST['due_date'])) {
                $errors[] = "Due date is required";
            } else if (!$this->validator->validateDate($_POST['due_date'])) {
                $errors[] = "Invalid due date format";
            }
            
            // If validation fails, redirect back with errors
            if (!empty($errors)) {
                $this->session->setFlash('errors', $errors);
                $this->session->setFlash('old', $_POST);
                header("Location: /tasks/create");
                exit;
            }
            
            // Set task properties
            $this->task->title = $_POST['title'];
            $this->task->description = $_POST['description'] ?? '';
            $this->task->status = $_POST['status'];
            $this->task->priority = $_POST['priority'];
            $this->task->due_date = $_POST['due_date'];
            $this->task->category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
            $this->task->user_id = $userId;
            
            // Create task
            if ($this->task->create()) {
                $this->session->setFlash('success', 'Task created successfully');
                header("Location: /tasks");
                exit;
            } else {
                $this->session->setFlash('error', 'Failed to create task');
                $this->session->setFlash('old', $_POST);
                header("Location: /tasks/create");
                exit;
            }
        }
    }

    // Show task edit form
    public function edit() {
        if (isset($_GET['id'])) {
            $userId = $this->session->getUserId();
            $taskId = $_GET['id'];
            
            // Get task details
            if ($this->task->getTask($taskId, $userId)) {
                // Get categories for dropdown
                $categories = $this->category->getAllCategories($userId);
                
                include_once 'views/tasks/edit.php';
            } else {
                $this->session->setFlash('error', 'Task not found');
                header("Location: /tasks");
                exit;
            }
        } else {
            header("Location: /tasks");
            exit;
        }
    }

    // Update task
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $this->session->getUserId();
            
            // Validate input
            $errors = [];
            
            if (empty($_POST['id'])) {
                $errors[] = "Task ID is missing";
            }
            
            if (empty($_POST['title'])) {
                $errors[] = "Title is required";
            }
            
            if (empty($_POST['status'])) {
                $errors[] = "Status is required";
            }
            
            if (empty($_POST['priority'])) {
                $errors[] = "Priority is required";
            }
            
            if (empty($_POST['due_date'])) {
                $errors[] = "Due date is required";
            } else if (!$this->validator->validateDate($_POST['due_date'])) {
                $errors[] = "Invalid due date format";
            }
            
            // If validation fails, redirect back with errors
            if (!empty($errors)) {
                $this->session->setFlash('errors', $errors);
                $this->session->setFlash('old', $_POST);
                header("Location: /tasks/edit?id=" . $_POST['id']);
                exit;
            }
            
            // Set task properties
            $this->task->id = $_POST['id'];
            $this->task->title = $_POST['title'];
            $this->task->description = $_POST['description'] ?? '';
            $this->task->status = $_POST['status'];
            $this->task->priority = $_POST['priority'];
            $this->task->due_date = $_POST['due_date'];
            $this->task->category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
            $this->task->user_id = $userId;
            
            // Update task
            if ($this->task->update()) {
                $this->session->setFlash('success', 'Task updated successfully');
                header("Location: /tasks");
                exit;
            } else {
                $this->session->setFlash('error', 'Failed to update task');
                $this->session->setFlash('old', $_POST);
                header("Location: /tasks/edit?id=" . $_POST['id']);
                exit;
            }
        }
    }

    // View task details
    public function view() {
        if (isset($_GET['id'])) {
            $userId = $this->session->getUserId();
            $taskId = $_GET['id'];
            
            // Get task details
            if ($this->task->getTask($taskId, $userId)) {
                include_once 'views/tasks/view.php';
            } else {
                $this->session->setFlash('error', 'Task not found');
                header("Location: /tasks");
                exit;
            }
        } else {
            header("Location: /tasks");
            exit;
        }
    }

    // Delete task
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $userId = $this->session->getUserId();
            
            $this->task->id = $_POST['id'];
            $this->task->user_id = $userId;
            
            if ($this->task->delete()) {
                $this->session->setFlash('success', 'Task deleted successfully');
            } else {
                $this->session->setFlash('error', 'Failed to delete task');
            }
            
            header("Location: /tasks");
            exit;
        } else {
            header("Location: /tasks");
            exit;
        }
    }

    // Update task status via AJAX
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
            $userId = $this->session->getUserId();
            $taskId = $_POST['id'];
            $newStatus = $_POST['status'];
            
            // Validate status
            $validStatuses = ['pending', 'in_progress', 'completed'];
            if (!in_array($newStatus, $validStatuses)) {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                exit;
            }
            
            // Get task
            if ($this->task->getTask($taskId, $userId)) {
                // Update status
                if ($this->task->updateStatus($newStatus)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Task not found']);
            }
            
            exit;
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }
}
