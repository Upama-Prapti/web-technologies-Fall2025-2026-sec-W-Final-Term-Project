<?php
require_once __DIR__ . '/../config/config.php';

class PostModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getPostById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function getPostsByStatus($status, $limit = null) {
        $sql = "SELECT * FROM posts WHERE status = ? ORDER BY date DESC";
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $status, $limit);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $status);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        return $posts;
    }
    
    public function getPostsByAdmin($adminId) {
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE admin_id = ? ORDER BY date DESC");
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        return $posts;
    }
    
    public function createPost($adminId, $name, $title, $content, $category, $image) {
        $stmt = $this->conn->prepare("INSERT INTO posts (admin_id, name, title, content, category, image, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("isssss", $adminId, $name, $title, $content, $category, $image);
        return $stmt->execute();
    }
    
    public function updatePost($id, $title, $content, $category, $image = null) {
        if ($image) {
            $stmt = $this->conn->prepare("UPDATE posts SET title = ?, content = ?, category = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $title, $content, $category, $image, $id);
        } else {
            $stmt = $this->conn->prepare("UPDATE posts SET title = ?, content = ?, category = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $content, $category, $id);
        }
        return $stmt->execute();
    }
    
    public function deletePost($id) {
        $stmt = $this->conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function approvePost($id) {
        $stmt = $this->conn->prepare("UPDATE posts SET status = 'active' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function rejectPost($id) {
        $stmt = $this->conn->prepare("UPDATE posts SET status = 'deactive' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getPostsByCategory($category) {
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE category = ? AND status = 'active' ORDER BY date DESC");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        return $posts;
    }
    
    public function getPostsByAuthor($author) {
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE name = ? AND status = 'active' ORDER BY date DESC");
        $stmt->bind_param("s", $author);
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        return $posts;
    }
    
    public function searchPosts($searchTerm) {
        $searchTerm = "%{$searchTerm}%";
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE (title LIKE ? OR content LIKE ?) AND status = 'active' ORDER BY date DESC");
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        return $posts;
    }
    
    public function getPendingPosts() {
        $result = $this->conn->query("SELECT * FROM posts WHERE status = 'pending' ORDER BY date DESC");
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        return $posts;
    }
}
?>

