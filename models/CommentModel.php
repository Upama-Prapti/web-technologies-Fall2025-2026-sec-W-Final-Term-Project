<?php
require_once __DIR__ . '/../config/config.php';

class CommentModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getCommentsByPost($postId) {
        $stmt = $this->conn->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY date DESC");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        return $comments;
    }
    
    public function createComment($postId, $adminId, $userId, $userName, $comment) {
        $stmt = $this->conn->prepare("INSERT INTO comments (post_id, admin_id, user_id, user_name, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $postId, $adminId, $userId, $userName, $comment);
        return $stmt->execute();
    }
    
    public function deleteComment($id) {
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getCommentsByUser($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM comments WHERE user_id = ? ORDER BY date DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        return $comments;
    }
}
?>

