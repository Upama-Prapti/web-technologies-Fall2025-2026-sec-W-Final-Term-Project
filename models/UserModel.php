<?php
require_once __DIR__ . '/../config/config.php';

class UserModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function createUser($name, $email, $password) {
        $hashedPassword = sha1($password);
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);
        return $stmt->execute();
    }
    
    public function updateUser($id, $name, $email) {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);
        return $stmt->execute();
    }
    
    public function changePassword($id, $newPassword) {
        $hashedPassword = sha1($newPassword);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);
        return $stmt->execute();
    }
    
    public function setAdmin($userId, $isAdmin) {
        $stmt = $this->conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $stmt->bind_param("ii", $isAdmin, $userId);
        return $stmt->execute();
    }
    
    public function getAllUsers() {
        $result = $this->conn->query("SELECT * FROM users ORDER BY id DESC");
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }
    
    public function verifyPassword($password, $hashedPassword) {
        return sha1($password) === $hashedPassword;
    }
}
?>

