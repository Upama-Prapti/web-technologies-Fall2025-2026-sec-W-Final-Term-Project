<?php
require_once __DIR__ . '/../config/config.php';

class AdminModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getAdminByName($name) {
        $stmt = $this->conn->prepare("SELECT * FROM admin WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function getAdminById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM admin WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function changePassword($id, $newPassword) {
        $hashedPassword = sha1($newPassword);
        $stmt = $this->conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);
        return $stmt->execute();
    }
    
    public function getAllAdmins() {
        $result = $this->conn->query("SELECT * FROM admin ORDER BY id DESC");
        $admins = [];
        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }
        return $admins;
    }
    
    public function verifyPassword($password, $hashedPassword) {
        return sha1($password) === $hashedPassword;
    }
}
?>

