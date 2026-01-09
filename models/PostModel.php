<?php
require_once __DIR__ . '/../config/config.php';

function model_post_get_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function model_post_get_by_status($conn, $status, $limit = null) {
    $sql = "SELECT * FROM posts WHERE status = ? ORDER BY date DESC";
    if ($limit) {
        $sql .= " LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $limit);
    } else {
        $stmt = $conn->prepare($sql);
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

function model_post_get_by_admin($conn, $adminId) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE admin_id = ? ORDER BY date DESC");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}

function model_post_create($conn, $adminId, $name, $title, $content, $category, $image) {
    $stmt = $conn->prepare("INSERT INTO posts (admin_id, name, title, content, category, image, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("isssss", $adminId, $name, $title, $content, $category, $image);
    return $stmt->execute();
}

function model_post_update($conn, $id, $title, $content, $category, $image = null) {
    if ($image) {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, category = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title, $content, $category, $image, $id);
    } else {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, category = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $content, $category, $id);
    }
    return $stmt->execute();
}

function model_post_delete($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function model_post_approve($conn, $id) {
    $stmt = $conn->prepare("UPDATE posts SET status = 'active' WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function model_post_reject($conn, $id) {
    $stmt = $conn->prepare("UPDATE posts SET status = 'deactive' WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function model_post_get_by_category($conn, $category) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE category = ? AND status = 'active' ORDER BY date DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}

function model_post_get_by_author($conn, $author) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE name = ? AND status = 'active' ORDER BY date DESC");
    $stmt->bind_param("s", $author);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}

function model_post_search($conn, $searchTerm) {
    $searchTerm = "%{$searchTerm}%";
    $stmt = $conn->prepare("SELECT * FROM posts WHERE (title LIKE ? OR content LIKE ?) AND status = 'active' ORDER BY date DESC");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}

function model_post_get_pending($conn) {
    $result = $conn->query("SELECT * FROM posts WHERE status = 'pending' ORDER BY date DESC");
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}
?>
