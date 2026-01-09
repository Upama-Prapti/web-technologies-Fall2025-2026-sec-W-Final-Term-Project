<?php
require_once __DIR__ . '/../config/config.php';

function model_like_toggle($conn, $userId, $adminId, $postId) {
    $stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $userId, $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();
        return 'removed';
    } else {
        $stmt = $conn->prepare("INSERT INTO likes (user_id, admin_id, post_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $userId, $adminId, $postId);
        $stmt->execute();
        return 'added';
    }
}

function model_like_has_liked($conn, $userId, $postId) {
    $stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $userId, $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function model_like_get_count($conn, $postId) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM likes WHERE post_id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

function model_like_get_by_user($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $likes = [];
    while ($row = $result->fetch_assoc()) {
        $likes[] = $row;
    }
    return $likes;
}
?>
