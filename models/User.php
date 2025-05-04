<?php
require_once 'config/database.php';

class User {
    private $conn;
    private $table = 'users';

    // User properties
    public $id;
    public $name;
    public $email;
    public $password;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create new user (register)
    public function create() {
        $sql = "INSERT INTO " . $this->table . " 
                (name, email, password, created_at, updated_at) 
                VALUES 
                (:name, :email, :password, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                RETURNING id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Clean data
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->email = htmlspecialchars(strip_tags($this->email));
            // Hash password
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
            
            // Bind parameters
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $hashedPassword);
            
            if ($stmt->execute()) {
                $row = $stmt->fetch();
                $this->id = $row['id'];
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Check if email exists
    public function emailExists() {
        $sql = "SELECT id, name, email, password FROM " . $this->table . " WHERE email = :email";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->password = $row['password']; // Hashed password from DB
                
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Login user
    public function login() {
        if ($this->emailExists()) {
            // Get the stored hashed password
            $storedPassword = $this->password;
            
            // Verify password (comparing plaintext input with stored hash)
            if (password_verify($this->password, $storedPassword)) {
                return true;
            }
        }
        
        return false;
    }

    // Get user by ID
    public function getUserById($id) {
        $sql = "SELECT id, name, email FROM " . $this->table . " WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $row = $stmt->fetch();
            
            if ($row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Update user profile
    public function update() {
        $sql = "UPDATE " . $this->table . " 
                SET 
                name = :name, 
                email = :email, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Clean data
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->email = htmlspecialchars(strip_tags($this->email));
            
            // Bind parameters
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':id', $this->id);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Update password
    public function updatePassword() {
        $sql = "UPDATE " . $this->table . " 
                SET 
                password = :password, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Hash new password
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
            
            // Bind parameters
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $this->id);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
