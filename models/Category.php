<?php
require_once 'config/database.php';

class Category {
    private $conn;
    private $table = 'categories';

    // Category properties
    public $id;
    public $name;
    public $color;
    public $user_id;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all categories for a user
    public function getAllCategories($userId) {
        $sql = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY name ASC";
        
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

    // Get a single category
    public function getCategory($id, $userId) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            $row = $stmt->fetch();
            
            if ($row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->color = $row['color'];
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

    // Create new category
    public function create() {
        $sql = "INSERT INTO " . $this->table . " 
                (name, color, user_id, created_at, updated_at) 
                VALUES 
                (:name, :color, :user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                RETURNING id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Clean data
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->color = htmlspecialchars(strip_tags($this->color));
            
            // Bind parameters
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':color', $this->color);
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

    // Update category
    public function update() {
        $sql = "UPDATE " . $this->table . " 
                SET 
                name = :name, 
                color = :color, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id AND user_id = :user_id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Clean data
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->color = htmlspecialchars(strip_tags($this->color));
            
            // Bind parameters
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':color', $this->color);
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

    // Delete category
    public function delete() {
        // First check if category is being used
        $sqlCheck = "SELECT COUNT(*) as count FROM tasks WHERE category_id = :id";
        
        try {
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->bindParam(':id', $this->id);
            $stmtCheck->execute();
            $result = $stmtCheck->fetch();
            
            if ($result['count'] > 0) {
                return false; // Category is in use
            }
            
            // If not in use, delete it
            $sql = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
            
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

    // Get task count by category for dashboard
    public function getTaskCountByCategory($userId) {
        $sql = "SELECT c.id, c.name, c.color, COUNT(t.id) as task_count 
                FROM " . $this->table . " c
                LEFT JOIN tasks t ON c.id = t.category_id
                WHERE c.user_id = :user_id
                GROUP BY c.id, c.name, c.color
                ORDER BY c.name ASC";
        
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
}
