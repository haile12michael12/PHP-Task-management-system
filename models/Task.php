<?php
require_once 'config/database.php';

class Task {
    private $conn;
    private $table = 'tasks';

    // Task properties
    public $id;
    public $title;
    public $description;
    public $status;
    public $priority;
    public $due_date;
    public $category_id;
    public $user_id;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all tasks for a user
    public function getAllTasks($userId, $filters = []) {
        $sql = "SELECT t.*, c.name as category_name 
                FROM " . $this->table . " t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :user_id";
        
        $params = [':user_id' => $userId];
        
        // Apply filters if any
        if (!empty($filters)) {
            if (isset($filters['status']) && $filters['status'] !== '') {
                $sql .= " AND t.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (isset($filters['priority']) && $filters['priority'] !== '') {
                $sql .= " AND t.priority = :priority";
                $params[':priority'] = $filters['priority'];
            }
            
            if (isset($filters['category_id']) && $filters['category_id'] !== '') {
                $sql .= " AND t.category_id = :category_id";
                $params[':category_id'] = $filters['category_id'];
            }
            
            if (isset($filters['due_date']) && $filters['due_date'] !== '') {
                // Get tasks due today or before
                if ($filters['due_date'] === 'today') {
                    $sql .= " AND t.due_date <= CURRENT_DATE";
                } 
                // Get tasks due within the week
                else if ($filters['due_date'] === 'week') {
                    $sql .= " AND t.due_date <= CURRENT_DATE + INTERVAL '7 days'";
                }
                // Get tasks due on specific date
                else {
                    $sql .= " AND t.due_date = :due_date";
                    $params[':due_date'] = $filters['due_date'];
                }
            }
            
            if (isset($filters['search']) && $filters['search'] !== '') {
                $sql .= " AND (t.title ILIKE :search OR t.description ILIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
        }
        
        // Apply sorting
        $sql .= " ORDER BY ";
        if (isset($filters['sort']) && $filters['sort'] !== '') {
            $sortField = $filters['sort'];
            $direction = (isset($filters['direction']) && $filters['direction'] === 'desc') ? 'DESC' : 'ASC';
            
            if ($sortField === 'due_date') {
                $sql .= "t.due_date " . $direction;
            } elseif ($sortField === 'priority') {
                $sql .= "t.priority " . $direction;
            } elseif ($sortField === 'status') {
                $sql .= "t.status " . $direction;
            } elseif ($sortField === 'category') {
                $sql .= "c.name " . $direction;
            } else {
                $sql .= "t.created_at DESC"; // Default sort
            }
        } else {
            $sql .= "t.created_at DESC"; // Default sort
        }
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Get task count by status for dashboard
    public function getTaskCountByStatus($userId) {
        $sql = "SELECT status, COUNT(*) as count 
                FROM " . $this->table . " 
                WHERE user_id = :user_id 
                GROUP BY status";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Get upcoming tasks for dashboard
    public function getUpcomingTasks($userId, $limit = 5) {
        $sql = "SELECT t.*, c.name as category_name 
                FROM " . $this->table . " t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :user_id 
                AND t.status != 'completed' 
                AND t.due_date >= CURRENT_DATE
                ORDER BY t.due_date ASC
                LIMIT :limit";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Get overdue tasks for dashboard
    public function getOverdueTasks($userId) {
        $sql = "SELECT t.*, c.name as category_name 
                FROM " . $this->table . " t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :user_id 
                AND t.status != 'completed' 
                AND t.due_date < CURRENT_DATE
                ORDER BY t.due_date ASC";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Get a single task
    public function getTask($id, $userId) {
        $sql = "SELECT t.*, c.name as category_name 
                FROM " . $this->table . " t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.id = :id AND t.user_id = :user_id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            $row = $stmt->fetch();
            
            if ($row) {
                $this->id = $row['id'];
                $this->title = $row['title'];
                $this->description = $row['description'];
                $this->status = $row['status'];
                $this->priority = $row['priority'];
                $this->due_date = $row['due_date'];
                $this->category_id = $row['category_id'];
                $this->user_id = $row['user_id'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Create new task
    public function create() {
        $sql = "INSERT INTO " . $this->table . " 
                (title, description, status, priority, due_date, category_id, user_id, created_at, updated_at) 
                VALUES 
                (:title, :description, :status, :priority, :due_date, :category_id, :user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                RETURNING id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Clean data
            $this->title = htmlspecialchars(strip_tags($this->title));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->priority = htmlspecialchars(strip_tags($this->priority));
            
            // Bind parameters
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':priority', $this->priority);
            $stmt->bindParam(':due_date', $this->due_date);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->bindParam(':user_id', $this->user_id);
            
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Update task
    public function update() {
        $sql = "UPDATE " . $this->table . " 
                SET 
                title = :title, 
                description = :description, 
                status = :status, 
                priority = :priority, 
                due_date = :due_date, 
                category_id = :category_id, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id AND user_id = :user_id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Clean data
            $this->title = htmlspecialchars(strip_tags($this->title));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->priority = htmlspecialchars(strip_tags($this->priority));
            
            // Bind parameters
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':priority', $this->priority);
            $stmt->bindParam(':due_date', $this->due_date);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':user_id', $this->user_id);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Delete task
    public function delete() {
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':user_id', $this->user_id);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Update task status
    public function updateStatus($newStatus) {
        $sql = "UPDATE " . $this->table . " 
                SET status = :status, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id AND user_id = :user_id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Clean data
            $newStatus = htmlspecialchars(strip_tags($newStatus));
            
            // Bind parameters
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':user_id', $this->user_id);
            
            if ($stmt->execute()) {
                $this->status = $newStatus;
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
