<?php
require_once __DIR__ . '/../config/config.php';

class LikeModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function toggleLike($userId, $adminId, $postId) {
        // Check if like exists
        $stmt = $this->conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Unlike
            $stmt = $this->conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
            $stmt->bind_param("ii", $userId, $postId);
            return $stmt->execute();
        } else {
            // Like
            $stmt = $this->conn->prepare("INSERT INTO likes (user_id, admin_id, post_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $userId, $adminId, $postId);
            return $stmt->execute();
        }
    }
    
    public function hasLiked($userId, $postId) {
        $stmt = $this->conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    public function getLikeCount($postId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM likes WHERE post_id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    public function getLikesByUser($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM likes WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $likes = [];
        while ($row = $result->fetch_assoc()) {
            $likes[] = $row;
        }
        return $likes;
    }
}
?>

