<?php
require_once __DIR__ . '/../config/config.php';

function model_comment_get_by_post($conn, $postId) {
    $stmt = $conn->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY date DESC");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    return $comments;
}

function model_comment_create($conn, $postId, $adminId, $userId, $userName, $comment) {
    $stmt = $conn->prepare("INSERT INTO comments (post_id, admin_id, user_id, user_name, comment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $postId, $adminId, $userId, $userName, $comment);
    return $stmt->execute();
}

function model_comment_delete($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function model_comment_get_by_user($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM comments WHERE user_id = ? ORDER BY date DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    return $comments;
}

function model_comment_get_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM comments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function model_comment_update($conn, $id, $comment) {
    $stmt = $conn->prepare("UPDATE comments SET comment = ? WHERE id = ?");
    $stmt->bind_param("si", $comment, $id);
    return $stmt->execute();
}
?>
